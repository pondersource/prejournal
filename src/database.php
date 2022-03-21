<?php declare(strict_types=1);
require_once 'vendor/autoload.php';
require_once(__DIR__ . '/../schema.php');

use Doctrine\DBAL\DriverManager;

function getDbConn() {
  // var_dump($_SERVER);
  if (isset($_SERVER['TESTING'])) {
    // echo "Loading test database";
    $tables = getTables();
    $conn = DriverManager::getConnection([
      'driver' => 'pdo_sqlite',
      'memory' => true
    ]);

    for ($i = 0; $i < count($tables); $i++) {
      // echo "Testing environment, creating table $i";
      $created = $conn->executeQuery($tables[$i]);
      // var_dump($created->fetchAll());
    }
    return $conn;
  } else {
    return DriverManager::getConnection([
      'driver' => 'pdo_sqlite',
      'url' => $_ENV["DATABASE_URL"]
    ]);
  }
}

function validateUser($username, $passwordGiven) {
  // output("Validating user $username $passwordGiven");
  $conn  = getDbConn();
  $query = 'SELECT id, passwordhash FROM users WHERE username = ?';
  $result = $conn->executeQuery($query, [ $username ]);
  $arr = $result->fetchAllNumeric();
  if (count($arr) == 1) {
    $id = intval($arr[0][0]);
    $passwordHash = $arr[0][1];
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
  $query = "INSERT INTO users (username, passwordhash) VALUES (?, ?)";
  $result = $conn->executeQuery($query, [ $username, $passwordHash ]);
  return !!$result;
}

function getMovementsFromComponent($componentName) {
  $conn  = getDbConn();
  $query = "SELECT * FROM movements INNER JOIN components ON movements.fromcomponent = components.id WHERE components.name = ?";
  $result = $conn->executeQuery($query, [ $componentName ]);
  $ret = [];
  while ($row = $result->fetchAssociative() !== false) {
      array_push($ret, $row);
  }
  return $ret;
}

function getMovementsToComponent($componentName) {
  $conn  = getDbConn();
  $query = "SELECT * FROM movements INNER JOIN components ON movements.tocomponent = components.id WHERE components.name = ?";
  $result = $conn->executeQuery($query, [ $componentName ]);
  $ret = [];
  while ($row = $result->fetchAssociative() !== false) {
      array_push($ret, $row);
  }
  return $ret;
}