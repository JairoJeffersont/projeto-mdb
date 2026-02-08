<?php

// Start output buffering to control when data is sent to the browser
ob_start();

// Define the folder where log files will be stored
define('LOG_FOLDER', dirname(__DIR__, 1) . '/logs');

// Define the public folder, typically used for assets like CSS, JS, and images
define('PUBLIC_FOLDER', dirname(__DIR__) . '/public');

// Define the base URL of the project, useful for creating relative links
define('BASE_URL', dirname(__DIR__));

// Import the Capsule manager from Eloquent to handle database operations
use Illuminate\Database\Capsule\Manager as Capsule;

// Import the Dotenv library to load environment variables from the .env file
use Dotenv\Dotenv;

// Load Composer's autoloader to automatically include all dependencies
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from the .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Create a new instance of Capsule (Eloquent) and configure the database connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => $_ENV['DB_DRIVER'],    // Database driver (mysql, pgsql, sqlite, etc.)
    'host'      => $_ENV['DB_HOST'],      // Database host
    'database'  => $_ENV['DB_DATABASE'],  // Database name
    'username'  => $_ENV['DB_USERNAME'],  // Database username
    'password'  => $_ENV['DB_PASSWORD'],  // Database password
    'charset'   => $_ENV['DB_CHARSET'],   // Character set for the connection
    'collation' => $_ENV['DB_COLLATION']  // Collation for the database
]);

// Make Capsule globally accessible so it can be used anywhere in the project
$capsule->setAsGlobal();

// Boot the Eloquent ORM
$capsule->bootEloquent();

// Include the base layout of the system, which likely loads header, footer, and other views
include('../src/Views/base/base_layout.php');
