<?php

error_reporting(1);
ini_set('display_errors', 1);

// Load Config
require_once 'Config/Config.php';

// Load Composer's autoloader
require_once APPROOT . '/vendor/autoload.php';

// Common Shorthand functions
require_once 'Helpers/Functions.php';

ob_start();

date_default_timezone_set(SERVER_TIMEZONE);

session_name(SESSION_NAME);
session_start();

// prevents overwrite of token
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Autoload libraries
spl_autoload_register(function ($className) {
    // Convert namespace to full file path
    $className = str_replace("App\\", "", $className); // Remove base namespace
    $className = str_replace("\\", "/", $className); // Replace backslashes with slashes
    $filePath = APPROOT . "/" . $className . '.php';

    if (file_exists($filePath)) {
        require_once $filePath;
    } else {
        // Error handling code can go here if needed
        die("File not found: " . $filePath);
    }
});
