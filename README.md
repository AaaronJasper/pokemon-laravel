# Pokémon Laravel Backend

A Pokémon management backend built with Laravel, providing APIs for Pokémon data retrieval, user management, and trade system. This backend serves as the API for the Pokémon frontend applications.

---

## Features

-   **Pokémon Data Management**: Fetch and manage Pokémon data from the database and PokeAPI.
-   **User Authentication**: Register, login, email verification, and password reset.
-   **Pokémon Trade System**: Allow users to trade Pokémon with validation rules.
-   **Real-time Notifications**: Supports trade updates via WebSockets.
-   **RESTful API**: Provides endpoints for frontend applications to interact with Pokémon data.

---

## Tech Stack

-   **Backend**: Laravel 10.0.3
-   **Database**: MySQL
-   **Authentication**: Laravel Sanctum (Bearer tokens)
-   **API**: Custom RESTful endpoints, GraphQL endpoints

---

## Installation

1. Clone the repository:

```bash
git clone https://github.com/AaaronJasper/pokemon-laravel.git
cd pokemon-laravel
```

2. Install dependencies:

```bash
composer install
npm install
```

3. Copy the environment configuration and set up .env:

```bash
cp .env.example .env
```

4. Configure your database credentials in .env file.

5. Run database migrations:

```bash
php artisan migrate
```

6. Import initial data for abilities and natures:

```bash
php artisan import:ability allAbilities.txt
php artisan import:nature allNatures.txt
```
