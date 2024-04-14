<template>
    <section class="relative h-screen w-full flex items-start justify-center mt-4">
        <div class="relative w-1/3 h-1/2"> <!-- Adjusted container size to be narrower -->
            <video ref="videoEl" autoplay="true" playsinline @loadedmetadata="runModel"
                class="absolute w-full h-full aspect-[16/9] mx-auto" /> <!-- Video will be centered and responsive -->
            <div class="absolute w-full h-full"> <!-- New container for the canvas -->
                <canvas ref="canvasEl" class="w-full h-full" /> <!-- Adjusted to match video size -->
            </div>
        </div>
    </section>
</template>

<script>
import * as faceAPI from 'face-api.js'
import { onMounted, reactive, ref, computed } from 'vue'
import { authStore } from '../stores/authstore'

const useAuth = authStore();

export default {
    name: 'Video',
    setup() {
        const initParams = reactive({
            modelUri: '/models',
            option: new faceAPI.SsdMobilenetv1Options({ minConfidence: 0.5 })
        })
        const constraints = reactive({
            video: {
                width: {
                    min: 320,
                    ideal: 1280,
                    max: 1920
                },
                height: {
                    min: 240,
                    ideal: 720,
                    max: 1080
                },
                frameRate: {
                    min: 15,
                    ideal: 30,
                    max: 60
                },
                facingMode: 'environment'
            }
        })
        const videoEl = ref(null)
        const canvasEl = ref(null)
        const expressions = reactive({ value: {} }) // Initialize with an empty object

        const highestExpression = computed(() => {
            let highest = { expression: '', score: 0 };
            if (expressions.value && typeof expressions.value === 'object') {
                for (const [expression, score] of Object.entries(expressions.value)) {
                    if (score > highest.score) {
                        highest = { expression, score };
                    }
                }
            }
            return highest.expression ? `${highest.expression} (${highest.score.toFixed(2)})` : '';
        });

        const runModel = async () => {
            const result = await faceAPI.detectAllFaces(videoEl.value, initParams.option).withFaceExpressions()
            if (result) {
                const dims = faceAPI.matchDimensions(canvasEl.value, videoEl.value, true)
                const resizeResults = faceAPI.resizeResults(result, dims)
                expressions.value = {} // Clear previous expressions by assigning an empty object
                resizeResults.forEach(face => {
                    Object.keys(face.expressions).forEach(expression => {
                        if (!expressions.value[expression]) expressions.value[expression] = 0;
                        expressions.value[expression] += face.expressions[expression];
                    });
                });
                faceAPI.draw.drawDetections(canvasEl.value, resizeResults)

                // Adjusted code to display expression text
                const ctx = canvasEl.value.getContext('2d');
                resizeResults.forEach(face => {
                    const { x, y, width, height } = face.detection.box;
                    const expressionText = highestExpression.value; // Assuming highestExpression is reactive and holds the current highest expression
                    ctx.fillStyle = 'white';
                    ctx.font = '32px Arial';
                    // Convert face detection box coordinates to canvas coordinates
                    const canvasX = x * (canvasEl.value.width / videoEl.value.videoWidth);
                    const canvasY = y * (canvasEl.value.height / videoEl.value.videoHeight);
                    ctx.fillText(expressionText, canvasX, canvasY - 15); // Adjust position as needed
                });
            }
            setTimeout(() => runModel())
        }

        onMounted(async () => {
            useAuth.getUser();
            await faceAPI.nets.ssdMobilenetv1.loadFromUri(initParams.modelUri)
            await faceAPI.nets.faceExpressionNet.loadFromUri(initParams.modelUri) // Load face expression model

            try {
                const stream = await navigator.mediaDevices.getUserMedia(constraints)
                videoEl.value.srcObject = stream
            } catch (error) {
                console.error(error.message)
            }
        })

        return {
            videoEl,
            canvasEl,
            runModel,
            expressions,
            highestExpression
        }
    }
}
</script>