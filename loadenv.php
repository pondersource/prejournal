<?php

require_once(__DIR__ . '/vendor/autoload.php');
if (isset($_SERVER['PREJOURNAL_ENV_FILE_DIR'])) {
    $envFileDir = $_SERVER['PREJOURNAL_ENV_FILE_DIR'];
} else {
    $envFileDir = __DIR__;
}
// echo "Looking for .env in $envFileDir\n";
$dotenv = Dotenv\Dotenv::createImmutable($envFileDir);
$dotenv->load();
