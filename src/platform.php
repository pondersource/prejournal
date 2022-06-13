<?php

declare(strict_types=1);
require_once(__DIR__ . '/database.php');

function readDotEnv()
{
    // Can be used when running from CLI
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

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_SERVER)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_SERVER[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    } else {
        // output("Not loading .env file ".$dotEnvPath);
    }
}

function getUser()
{
    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
        // echo 'validating user based on PHP_AUTH_USER / PHP_AUTH_PW';
        return validateUser($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
    }
    if (isset($_SERVER['PREJOURNAL_USERNAME']) && isset($_SERVER['PREJOURNAL_PASSWORD'])) {
        // echo 'validating user based on PREJOURNAL_USERNAME / PREJOURNAL_PASSWORD';
        return validateUser($_SERVER['PREJOURNAL_USERNAME'], $_SERVER['PREJOURNAL_PASSWORD']);
    }
    // echo 'not logged in\n';
    return null;
}

function setUser($username, $password, $employer)
{
    $_SERVER['PREJOURNAL_USERNAME'] = $username;
    $_SERVER['PREJOURNAL_PASSWORD'] = $password;
    $_SERVER["PREJOURNAL_DEFAULT_EMPLOYER"] = $employer;
}

function getMode()
{
    if (isset($_SERVER["REQUEST_URI"])) {
        $parts = explode("/", $_SERVER["REQUEST_URI"]);
        if (count($parts) >=3 && $parts[0] == "") {
            if ($parts[1] == "v1") {
                return 'single';
            }
            if ($parts[1] == "v1-batch") {
                return 'batch';
            }
        }
    }
    return 'unknown';
}

function getBatchHandle($isCli)
{
    if ($isCli) {
        return fopen($_SERVER['argv'][1], 'r');
    } else {
        return fopen('php://input', 'r');
    }
}

function getCommand()
{
    if (isset($_SERVER["REQUEST_URI"])) {
        $parts = explode("/", $_SERVER["REQUEST_URI"]);
        if (count($parts) >=3 && $parts[0] == "" && $parts[1] == "v1") {
            try {
                $postBody = file_get_contents('php://input');
                //  echo "parsing post body!";
                //  var_dump($postBody);
                if (is_string($postBody) && strlen($postBody) > 0) {
                    $arr = json_decode($postBody);
                    if (is_array($arr)) {
                        $parts = array_merge($parts, $arr);
                    }
                }
            } catch (Exception $e) {
                // ...
            }
            return array_slice($parts, 2);
        }
    } else {
        return array_slice($_SERVER["argv"], 1);
    }
    return [];
}

function getContext()
{
    if (!isset($_SERVER["PREJOURNAL_DEFAULT_EMPLOYER"])) {
        throw new Error("Please set env var PREJOURNAL_DEFAULT_EMPLOYER to the component name to be used in the 7-word submit-expense command.");
    }
    return [
    'user' => getUser(),
    'adminParty' => (isset($_SERVER["PREJOURNAL_ADMIN_PARTY"]) && $_SERVER["PREJOURNAL_ADMIN_PARTY"] == "true"),
    'employer' => $_SERVER["PREJOURNAL_DEFAULT_EMPLOYER"]
  ];
}

function output($strArr)
{
    if ($strArr === null) {
        return;
    }
    for ($i = 0; $i < count($strArr); $i++) {
        echo $strArr[$i] . "\n";
    }
}
