# Pokemon Laravel Backend

A RESTful + GraphQL API backend for a Pokémon management platform, built with Laravel 10. Supports Pokémon CRUD, a trade system, skill management, user auth, and AI-generated Pokémon descriptions.

---

## Features

- **Pokémon CRUD** — create, read, update, and soft-delete Pokémon with race, nature, ability, and skills
- **Skill System** — teach skills to Pokémon; query available and learned skills per Pokémon
- **Trade System** — propose, accept, reject, and review trade history; real-time notifications via WebSockets
- **Like / Ranking** — like or unlike Pokémon; view top-liked Pokémon rankings
- **AI Descriptions** — generate AI-written Pokémon descriptions based on stats and moves
- **User Auth** — register, login, logout, email verification, password reset, Google OAuth (Socialite)
- **GraphQL** — Lighthouse-powered GraphQL endpoints for Pokémon, abilities, natures, and skills
- **Laravel Sanctum** — Bearer token authentication for protected routes

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 10 |
| Database | MySQL |
| Authentication | Laravel Sanctum + Google OAuth (Socialite) |
| Real-time | WebSockets (Ratchet / Laravel Broadcasting) |
| Cache | Redis (Predis) |
| GraphQL | Lighthouse |
| API | RESTful + GraphQL |

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

3. Copy and configure the environment file:

```bash
cp .env.example .env
```

Edit `.env` with your database credentials, mail settings, Redis config, and Google OAuth keys.

4. Generate the application key:

```bash
php artisan key:generate
```

5. Run database migrations:

```bash
php artisan migrate
```

6. Import seed data for abilities and natures:

```bash
php artisan import:ability allAbilities.txt
php artisan import:nature allNatures.txt
```

---

## API Reference

All endpoints are prefixed with `/api`.

### Authentication

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| POST | `/user/register` | No | Register a new user |
| POST | `/user/login` | No | Login and receive a Bearer token |
| DELETE | `/user/logout` | Yes | Logout and invalidate token |
| POST | `/send_verify` | Yes | Resend email verification |
| GET | `/verify/{token}` | No | Verify email address |
| POST | `/forget_password` | No | Send password reset email |
| POST | `/reset_password/{token}` | No | Reset password with token |
| GET | `/auth/google` | No | Initiate Google OAuth login |
| GET | `/auth/google/callback` | No | Google OAuth callback |
| POST | `/oauth/exchange-token` | No | Exchange OAuth token for user info |

### Pokémon

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| GET | `/pokemon` | No | List all Pokémon (supports `?query=` search) |
| POST | `/pokemon` | Yes | Create a new Pokémon |
| GET | `/pokemon/{id}` | No | Get a specific Pokémon |
| PUT | `/pokemon/{id}` | Yes | Update a Pokémon |
| DELETE | `/pokemon/{id}` | Yes | Delete a Pokémon |
| POST | `/pokemon/describe` | No | Generate an AI description for a Pokémon |

### Skills

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| GET | `/pokemon/{id}/enableSkill` | Yes | Get skills available to learn |
| GET | `/pokemon/{id}/skill` | Yes | Get skills the Pokémon has learned |
| POST | `/pokemon/{id}/skill` | Yes | Teach a skill to a Pokémon |

### Trades

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| POST | `/trade` | Yes | Propose a trade |
| GET | `/trade` | Yes | List active trades |
| PUT | `/trade/{id}/accept` | Yes | Accept a trade |
| PUT | `/trade/{id}/reject` | Yes | Reject a trade |
| GET | `/trade/history` | Yes | View trade history |
| GET | `/trade/unread-notifications` | Yes | Get unread trade notifications |
| POST | `/trade/{trade}/mark-as-read` | Yes | Mark a notification as read |

### Likes & Ranking

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| POST | `/like` | Yes | Like a Pokémon |
| POST | `/unlike` | Yes | Unlike a Pokémon |
| GET | `/ranking/top-liked` | No | Get top-liked Pokémon |

### Natures & Abilities

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| POST | `/nature` | Yes | Create a nature |
| PUT | `/nature/{id}` | Yes | Update a nature |
| POST | `/ability` | Yes | Create an ability |
| PUT | `/ability/{id}` | Yes | Update an ability |

---

## GraphQL

GraphQL is available via the Lighthouse package. Queries and mutations are available for Pokémon, abilities, natures, and skills.

Endpoint: `POST /graphql`

---

## Authentication

Protected routes require a Bearer token in the `Authorization` header:

```
Authorization: Bearer {your_token}
```

Tokens are issued on login and invalidated on logout.
