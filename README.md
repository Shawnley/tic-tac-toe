# Tic-Tac-Toe Game

This is a simple Tic-Tac-Toe game built using Laravel for the backend and React for the frontend. The game supports two modes: 2-player and player vs. AI. The project uses Tailwind CSS for styling and includes basic animations for enhanced user experience.

## Live Demo
Check out the live demo of the Tic-Tac-Toe game [here](https://tic-tac-toe.shawnley.dk).

[![Laravel Forge Site Deployment Status](https://img.shields.io/endpoint?url=https%3A%2F%2Fforge.laravel.com%2Fsite-badges%2F182bf856-95fd-4a7f-b650-175f2508cdb1%3Flabel%3D1%26commit%3D1&style=for-the-badge)](https://forge.laravel.com/servers/835405/sites/2445897)


## Features

- **Player vs. Player Mode**: Play against another person.
- **Player vs. AI Mode**: Play against a simple AI.
- **Animated Winning Cells**: The winning cells pulse to indicate the winner.
- **Score Tracking**: Keeps track of scores between rounds.
- **Restart and Reset**: Restart the game while keeping the score, or reset the entire game.

## Technologies Used

- **Backend**: Laravel 9, PHP 8.x
- **Frontend**: React, Vite, Tailwind CSS
- **Database**: MySQL (or any database supported by Laravel)
- **Testing**: PHPUnit

## Installation

### Prerequisites

- PHP 8.x
- Composer
- Node.js and npm
- MySQL or any other database supported by Laravel

### Step 1: Clone the Repository

```bash
git clone https://github.com/shawnley/tic-tac-toe.git
cd tic-tac-toe
```

### Step 2: Install Backend Dependencies

```bash
composer install
cp .env.example .env
php artisan key:generate
```

### Step 3: Configure the Database

Edit the .env file to set your database connection details

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Then, migrate the database:

```bash
php artisan migrate
```

### Step 4: Install Frontend Dependencies

```bash
npm install
npm run dev
```

### Step 5: Serve the Application

#### Option 1: Using php artisan serve

```bash
php artisan serve
```
Visit http://localhost:8000 in your browser to play the game.


#### Option 2: Using Laravel Sail (Docker)

If you prefer using Docker, you can serve the application with Laravel Sail. Since Sail is already installed, you can start the application by running:

```bash
./vendor/bin/sail up
```

## Usage
- **Choosing Game Mode:** When you first visit the game, you'll be prompted to choose between "2 Player" mode or "Player vs AI" mode.
- **Making a Move:** Click on a cell in the Tic-Tac-Toe board to make your move. The game will automatically switch turns between players.
- **Winning:** If you complete a row, column, or diagonal, you win! The winning cells will pulse to indicate victory.
- **Restarting:** Click "Restart" to start a new game while keeping the current scores.
- **Resetting:** Click "Reset Game" to clear the scores and restart from scratch.

## Testing

This project includes unit tests for the backend logic. To run the tests, use the following command:

```bash
php artisan test
```

## Test Coverage

- Game Initialization: Ensures the game starts with the correct initial state.
- Making Moves: Tests that moves are correctly applied and validated.
- Victory Detection: Ensures the game correctly identifies a win.
- Game Restart: Tests that the game can restart while keeping scores updated.
- AI Moves: Tests that the AI makes valid moves.

## License

This project is open-source and available under the MIT License.

## Acknowledgments

- **Laravel:** For providing a powerful and elegant PHP framework that makes web development easier and more enjoyable.
- **Laravel Sail:** For simplifying local development using Docker, making it easy to set up and manage development environments.
- **React:** For offering a flexible and efficient JavaScript library to build dynamic user interfaces.
- **Tailwind CSS:** For creating a utility-first CSS framework that allows for rapid UI development with ease.
- **tailwindcss-animated:** For providing pre-built animations that seamlessly integrate with Tailwind CSS, adding dynamic effects to our UI.
