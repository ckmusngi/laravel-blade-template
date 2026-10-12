# Laravel Blade Template

A starter Laravel application with session login for the browser, token authentication for an API, and role-based authorization. The pages are Blade views styled with plain CSS, with no CSS framework.

## Features

- **Login and logout:** a session-based Blade login with "Remember me". Failed logins are rate-limited to 5 attempts per email and IP address.
- **Forgot password:** sends a password reset link by email and lets the user choose a new password. The form gives the same response whether or not the email is registered, so it can't be used to find out who has an account. Resetting a password also revokes all of that user's API tokens.
- **Dashboard:** shows the user's account details and how many API tokens they have. Admins also see user totals.
- **Roles and authorization:** each user is an `admin` or a `user`. Rules are defined in [`UserPolicy`](app/Policies/UserPolicy.php).
- **User management (admins only):** list users, change roles and delete accounts. Admins can't change their own role or delete themselves, so there is always at least one admin.
- **Token API:** a versioned JSON API (`/api/v1`) that uses Laravel Sanctum personal access tokens.
- **Plain CSS:** a single stylesheet using CSS custom properties, with automatic dark mode and layouts that adapt to phone screens.

## Requirements

- PHP 8.3 or newer
- Composer
- Node.js and npm
- SQLite (the default database). You can switch to MySQL or PostgreSQL in `.env`.

## Getting started

```bash
composer run setup      # install dependencies, create .env, generate key, migrate, build assets
php artisan db:seed     # create the demo accounts
composer run dev        # start the server, queue worker, logs and Vite
```

Open <http://localhost:8000>. You'll be taken to the login page.

### Demo accounts

The seeder creates these accounts, plus 8 random regular users. Every account has the password `password`.

| Email               | Role  |
| ------------------- | ----- |
| `admin@example.com` | Admin |
| `test@example.com`  | User  |

Change or remove these accounts before deploying.

### Password reset emails

`MAIL_MAILER` is set to `log` by default, so reset emails are written to `storage/logs/laravel.log` instead of being sent. Copy the reset link from that file to finish the flow locally. To send real email, set the `MAIL_*` variables in `.env`.

## Web routes

| Method | URL                       | Access    | Purpose                            |
| ------ | ------------------------- | --------- | ---------------------------------- |
| GET    | `/login`                  | Guest     | Login form                         |
| POST   | `/login`                  | Guest     | Log in                             |
| GET    | `/forgot-password`        | Guest     | Request a reset link               |
| POST   | `/forgot-password`        | Guest     | Send the reset link (6 per minute) |
| GET    | `/reset-password/{token}` | Guest     | New password form                  |
| POST   | `/reset-password`         | Guest     | Save the new password              |
| GET    | `/dashboard`              | Signed in | Dashboard                          |
| GET    | `/users`                  | Admin     | User list                          |
| PATCH  | `/users/{user}/role`      | Admin     | Change a user's role               |
| DELETE | `/users/{user}`           | Admin     | Delete a user                      |
| POST   | `/logout`                 | Signed in | Log out                            |

## API

All endpoints are under `/api/v1`. Send `Accept: application/json` with every request. Authenticated endpoints need an `Authorization: Bearer <token>` header.

| Method | URL                  | Access                        | Purpose                               |
| ------ | -------------------- | ----------------------------- | ------------------------------------- |
| POST   | `/api/v1/login`      | Public                        | Exchange credentials for a token      |
| GET    | `/api/v1/user`       | Token                         | The authenticated user                |
| POST   | `/api/v1/logout`     | Token                         | Revoke the token used for the request |
| GET    | `/api/v1/users`      | Admin token                   | Paginated list of users               |
| GET    | `/api/v1/users/{id}` | Admin, or the user themselves | A single user                         |

### Example

```bash
# Get a token
curl -X POST http://localhost:8000/api/v1/login \
  -H "Accept: application/json" \
  -d "email=test@example.com" -d "password=password" -d "device_name=my-laptop"

# {"token":"1|abc123...","user":{"id":2,"name":"Test User","email":"test@example.com","role":"user",...}}

# Use the token
curl http://localhost:8000/api/v1/user \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|abc123..."

# Revoke the token
curl -X POST http://localhost:8000/api/v1/logout \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|abc123..."
```

API login uses the same rate limit as the web login. Failed requests return `422` with validation errors. Requests without a valid token return `401`, and requests the user's role doesn't allow return `403`.

## Authorization

Roles are defined in the [`UserRole`](app/Enums/UserRole.php) enum and stored in the `role` column of the `users` table. New users default to `user`. The `role` attribute isn't mass-assignable, so it can only be changed on purpose, as in [`UserController::updateRole`](app/Http/Controllers/UserController.php).

Checks go through [`UserPolicy`](app/Policies/UserPolicy.php), and the web pages and the API use the same policy:

| Ability      | Allowed for                            |
| ------------ | -------------------------------------- |
| `viewAny`    | Admins                                 |
| `view`       | Admins, or the user viewing themselves |
| `updateRole` | Admins, except on their own account    |
| `delete`     | Admins, except on their own account    |

Use the policy in controllers with `Gate::authorize('viewAny', User::class)` and in Blade with `@can('viewAny', App\Models\User::class)`.

To make an existing user an admin from the command line:

```bash
php artisan tinker --execute 'App\Models\User::where("email", "someone@example.com")->first()->forceFill(["role" => "admin"])->save();'
```

## Project structure

```text
app/
├── Enums/UserRole.php                  # admin / user roles
├── Http/
│   ├── Controllers/
│   │   ├── Auth/                       # login, logout, forgot and reset password
│   │   ├── Api/                        # Sanctum token login and user endpoints
│   │   ├── DashboardController.php
│   │   └── UserController.php          # admin user management
│   ├── Requests/                       # LoginRequest (incl. rate limiting), UpdateUserRoleRequest
│   └── Resources/UserResource.php      # API JSON shape for users
├── Models/User.php                     # HasApiTokens, role cast, isAdmin()
└── Policies/UserPolicy.php

resources/
├── css/app.css                         # all styles
└── views/
    ├── components/
    │   ├── layouts/                    # base, guest (auth pages) and app (signed-in) layouts
    │   ├── input.blade.php             # labelled input with validation errors
    │   ├── flash.blade.php             # success messages
    │   └── role-badge.blade.php
    ├── auth/                           # login, forgot-password, reset-password
    ├── users/index.blade.php
    └── dashboard.blade.php

routes/
├── web.php
└── api.php
```

## Styling

All styles are in [`resources/css/app.css`](resources/css/app.css) and are compiled by Vite. Colours, radii and shadows are CSS custom properties on `:root`, with a dark mode set under `prefers-color-scheme: dark`. To retheme the app, change those variables.

Class names follow a light BEM style, for example `.card`, `.card__title`, `.btn--primary` and `.field__error`.

If a style change doesn't appear, run `npm run dev`, or `npm run build` for a production build.

## Testing

```bash
composer run test
```

The feature tests in [`tests/Feature`](tests/Feature) cover login, logout, rate limiting, the password reset flow, role authorization, and API token login and logout. They use an in-memory SQLite database.

Format PHP code with Laravel Pint:

```bash
vendor/bin/pint
```

## License

Open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
