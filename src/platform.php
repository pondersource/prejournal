<?php
require_once(__DIR__ . '/database.php');

// Can be used when running from CLI
// Not necessary when running on Heroku or as a Nextcloud app
$dotEnvPath = __DIR__ . '/../.env';
if (is_readable($dotEnvPath)) {
    // output("loading .env file");
    $lines = file($dotEnvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
} else {
  // output("Not loading .env file ".$dotEnvPath);
}

function getUser() {
  var_dump($_SERVER);
  if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
    return validateUser($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
  }
  if (isset($_ENV['PREJOURNAL_USERNAME']) && isset($_ENV['PREJOURNAL_PASSWORD'])) {
    return validateUser($_ENV['PREJOURNAL_USERNAME'], $_ENV['PREJOURNAL_PASSWORD']);
  }
  return null;
}

function getCommand() {
   if (isset($_SERVER["REQUEST_URI"])) {
     $parts = explode("/", $_SERVER["REQUEST_URI"]);
     if (count($parts) >=3 && $parts[0] == "" && $parts[1] == "v1") {
       try {
         $parts = array_merge($parts, json_decode(file_get_contents('php://input')));
       } catch (Exception $e) {
         // ...
       }
       var_dump($parts);
       return array_slice($parts, 2);
     }
   } else {
    return array_slice($_SERVER["argv"], 1);
  }
  return [];
}

function output($str) {
  echo "\n$str\n";
}
?>