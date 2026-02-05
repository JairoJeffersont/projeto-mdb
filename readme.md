# PHP Web Application

This is a lightweight PHP web application following a simple MVC-style structure, using Composer for dependency management and Eloquent (Illuminate Database) as the ORM.

## Features

- PHP 8+ application
- MVC-inspired folder structure
- Eloquent ORM (Illuminate Database)
- Environment variable support with Dotenv
- UUID generation
- File upload helper
- Logging utility
- Email sending with PHPMailer

## Project Structure

```
app/
├── public/            # Public entry point and assets
│   ├── index.php      # Application bootstrap
│   ├── css/
│   ├── js/
│   └── img/
├── src/
│   ├── Controllers/   # Application controllers
│   ├── Models/        # Eloquent models
│   ├── Helpers/       # Helper classes
│   └── Views/         # Views and layouts
├── logs/              # Application logs
├── vendor/            # Composer dependencies
├── .env               # Environment configuration
├── composer.json
└── composer.lock
```

## Requirements

- PHP 8.0 or higher
- Composer
- MySQL or compatible database

## Installation

1. Clone the repository:

```bash
git clone https://github.com/JairoJeffersont/framework framework
cd framework
```

2. Install dependencies:

```bash
composer install
```

3. Create the environment file:

```bash
cp .env.example .env
```

4. Configure the `.env` file with your database credentials:

```env
DB_DRIVER=mysql
DB_HOST=localhost
DB_DATABASE=database_name
DB_USERNAME=user
DB_PASSWORD=password
DB_CHARSET=utf8
DB_COLLATION=utf8_unicode_ci
```

## Running the Application

Point your web server document root to the `public/` directory.

Example using PHP built-in server:

```bash
php -S localhost:8000 -t public
```

Then open your browser at:

```
http://localhost:8000
```

## Autoloading

The project uses PSR-4 autoloading:

```json
"JairoJeffersont\\": "src/"
```

## License

This project is licensed under the MIT License.

## Author

Jairo Santos

