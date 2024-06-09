# FRContent
Movie recommendations based on user's detected facial emotions WEB App.

### Setting up
Install neccesary packages for backend, navigate to project's 'Backend' directory and run in terminal:
> composer install

Install neccesary packages for frontend, navigate to project's 'Frontend' directory and run in terminal:
> npm install

Run migrations, to setup the database:
> php artisan migrate

_Also put your TMDB API key in the 'Frontend' directory's .env file_

### Running the project
Start the backend from the project's 'Backend' directory:
> php artisan serve

Start the frontend from the project's 'Frontend' directory:
> npm run dev

### Technologies used in this project
> Laravel v11.x, Vue 3.x, Vite 5.x, MySQL, Tailwind CSS, @vladmandic face-api.js library.

### Project's technical documentation (In Latvian)
> https://docs.google.com/document/d/156ahZM04Cs7I-NQ0HCRHlvOhK54RjPEBVUHJrf9zwFM/edit?usp=sharing

### What have i learned when creating this project?
- I've moderately learned Laravel, setting it up, creating APIs in it, use of it's features, like Laravel Breeze, for convenient authentication system setup.
- Also i've learned to use Vue along with Vite, setting them up, integrating Vue's routing system with Laravel, creating a SPA website, components and such.
- Fulfilled my skills with Tailwind CSS, responsive website, various design element making.
- Technical documentation's development about the project, it's features and such.
