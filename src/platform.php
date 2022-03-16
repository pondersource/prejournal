<?php


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
  output("Not loading .env file ".$dotEnvPath);
}

function getUser() {
  if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
    return validateUser($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
  }
  if (isset($_ENV['PREJOURNAL_USERNAME']) && isset($_ENV['PREJOURNAL_PASSWORD'])) {
    return validateUser($_ENV['PREJOURNAL_USERNAME'], $_ENV['PREJOURNAL_PASSWORD']);
  }
  return null;
}

function getDbConn() {
  return pg_connect($_ENV["DATABASE_URL"]);
}

function validateUser($username, $passwordGiven) {
  // output("Validating user $username $passwordGiven");
  $conn  = getDbConn();
  $query = "SELECT id, passwordhash FROM users WHERE username = $1";
  $result = pg_query_params($conn, $query, [ $username ]);
  $arr = pg_fetch_array($result, 0, PGSQL_NUM);
  if (pg_num_rows($result) == 1) {
    $id = intval($arr[0]);
    $passwordHash = $arr[1];
    // var_dump($arr);
    $conclusion = password_verify($passwordGiven, $passwordHash);
    // var_dump($conclusion);
    if ($conclusion) {
      return [
        "id" => $id,
        "username" => $username
      ];
    }
  }
  return null;
}

function createUser($username, $passwordGiven) {
  $conn  = getDbConn();
  $passwordHash = password_hash($passwordGiven, PASSWORD_BCRYPT, [ "cost" => 10 ]);
  $query = "INSERT INTO users (username, passwordhash) VALUES ($1, $2)";
  $result = pg_query_params($conn, $query, [ $username, $passwordHash ]);
  return !!$result;
}

function getCommand() {
   return array_slice($_SERVER["argv"], 1);
}

function output($str) {
  echo "\n$str\n";
}
?>