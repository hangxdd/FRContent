<template>
  <div class="min-h-screen flex items-start justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-2">
      <div class="rounded-md overflow-hidden">
        <div class="w-full h-64 relative">
          <video
            v-show="isVideoActive"
            ref="videoEl"
            autoplay="true"
            playsinline
            @loadedmetadata="runModel"
            class="absolute w-full h-full mx-auto rounded-lg"
          />
          <canvas
            v-show="isVideoActive"
            ref="canvasEl"
            class="absolute w-full h-full rounded-lg"
          />
        </div>
      </div>
      <div class="flex items-center justify-between">
        <button
          @click="toggleVideo"
          class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          {{ isVideoActive ? "Stop Video" : "Start Video" }}
        </button>
      </div>
      <div class="flex items-center justify-between">
        <button
          @click="recordEmotion"
          class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
        >
          Record Emotion
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import * as faceAPI from "face-api.js";
import { onMounted, reactive, ref, computed } from "vue";
import { authStore } from "../stores/authstore";

const useAuth = authStore();

export default {
  name: "Video",
  setup() {
    const initParams = reactive({
      modelUri: "/models",
      option: new faceAPI.SsdMobilenetv1Options({ minConfidence: 0.5 }),
    });
    const constraints = reactive({
      video: {
        width: {
          min: 320,
          ideal: 1280,
          max: 1920,
        },
        height: {
          min: 240,
          ideal: 720,
          max: 1080,
        },
        frameRate: {
          min: 15,
          ideal: 30,
          max: 60,
        },
        facingMode: "environment",
      },
    });
    const videoEl = ref(null);
    const canvasEl = ref(null);
    const expressions = reactive({ value: {} }); // Initialize with an empty object
    const isVideoActive = ref(false); // New reactive variable to track video state

    const highestExpression = computed(() => {
      let highest = { expression: "", score: 0 };
      if (expressions.value && typeof expressions.value === "object") {
        for (const [expression, score] of Object.entries(expressions.value)) {
          if (score > highest.score) {
            highest = { expression, score };
          }
        }
      }
      return highest.expression
        ? `${highest.expression} (${highest.score.toFixed(2)})`
        : "";
    });

    const runModel = async () => {
      const result = await faceAPI
        .detectAllFaces(videoEl.value, initParams.option)
        .withFaceExpressions();
      if (result) {
        const dims = faceAPI.matchDimensions(canvasEl.value, videoEl.value, true);
        const resizeResults = faceAPI.resizeResults(result, dims);
        expressions.value = {}; // Clear previous expressions by assigning an empty object
        resizeResults.forEach((face) => {
          Object.keys(face.expressions).forEach((expression) => {
            if (!expressions.value[expression]) expressions.value[expression] = 0;
            expressions.value[expression] += face.expressions[expression];
          });
        });
        faceAPI.draw.drawDetections(canvasEl.value, resizeResults);

        // Adjusted code to display expression text
        const ctx = canvasEl.value.getContext("2d");
        resizeResults.forEach((face) => {
          const { x, y, width, height } = face.detection.box;
          const expressionText = highestExpression.value; // Assuming highestExpression is reactive and holds the current highest expression
          ctx.fillStyle = "white";
          ctx.font = "32px Arial";
          // Convert face detection box coordinates to canvas coordinates
          const canvasX = x * (canvasEl.value.width / videoEl.value.videoWidth);
          const canvasY = y * (canvasEl.value.height / videoEl.value.videoHeight);
          ctx.fillText(expressionText, canvasX, canvasY - 15); // Adjust position as needed
        });
      }
      setTimeout(() => runModel());
    };

    onMounted(async () => {
      useAuth.getUser();
      await faceAPI.nets.ssdMobilenetv1.loadFromUri(initParams.modelUri);
      await faceAPI.nets.faceExpressionNet.loadFromUri(initParams.modelUri); // Load face expression model
    });

    const toggleVideo = async () => {
      if (isVideoActive.value) {
        // Stop the video
        const stream = videoEl.value.srcObject;
        const tracks = stream.getTracks();
        tracks.forEach((track) => {
          track.stop();
        });
        videoEl.value.srcObject = null;
        isVideoActive.value = false;
      } else {
        // Start the video
        try {
          const stream = await navigator.mediaDevices.getUserMedia(constraints);
          videoEl.value.srcObject = stream;
          isVideoActive.value = true;
        } catch (error) {
          console.error(error.message);
        }
      }
    };

    return {
      videoEl,
      canvasEl,
      runModel,
      expressions,
      highestExpression,
      isVideoActive,
      toggleVideo,
    };
  },
};
</script>
