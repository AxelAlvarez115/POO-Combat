<?php

declare(strict_types=1);

// Enable all PHP errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Pretty errors
ini_set('html_errors', '1');
ini_set('error_prepend_string', "<pre style='color:#333;font-family:monospace;white-space:pre-wrap;font-size:17px;color:#880808'>");
ini_set('error_append_string', '</pre>');

// Autoload logic — searches basics/ and managers/ subdirectories
function chargerClasse(string $classname): void
{
    $dirs = [
        __DIR__ . '/../classes/basics/',
        __DIR__ . '/../classes/managers/',
        __DIR__ . '/../classes/',
    ];

    foreach ($dirs as $dir) {
        $path = $dir . $classname . '.php';
        if (file_exists($path)) {
            require $path;
            return;
        }
    }
}

spl_autoload_register('chargerClasse');

// Session
session_start();
