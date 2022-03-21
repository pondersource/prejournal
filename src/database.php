<?php
require_once 'vendor/autoload.php';

use Doctrine\DBAL\DriverManager;

function getDbConn() {
  return DriverManager::getConnection([ 'url' => $_ENV["DATABASE_URL"] ]);
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
    var_dump($conclusion);
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