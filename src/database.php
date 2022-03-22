<?php declare(strict_types=1);
require_once 'vendor/autoload.php';
require_once(__DIR__ . '/../schema.php');

use Doctrine\DBAL\DriverManager;

function getDbConn() {
  global $test_db_connection;
  if (isset($test_db_connection)) {
    return $test_db_connection;
  } else {
    return DriverManager::getConnection([
      'url' => $_SERVER["DATABASE_URL"]
    ]);
  }
}

function setTestDb() {
  global $test_db_connection;
  $tables = getTables();
  $test_db_connection = DriverManager::getConnection([
    'driver' => 'pdo_sqlite', 'memory' => true
    // 'driver' => 'pdo_pgsql' // for debugging, seeing database contents on localhost postgresql server using "psql postgres"
  ]);

  for ($i = 0; $i < count($tables); $i++) {
    $created = $test_db_connection->executeQuery($tables[$i]);
  }
}

function tableDump($tablename) { // for debugging
  $conn  = getDbConn();
  $query = "SELECT * FROM $tablename";
  $result = $conn->executeQuery($query);
  var_dump($result->fetchAllAssociative());
}

function validateUser($username, $passwordGiven) {
  // echo "Validating user $username $passwordGiven";
  $conn  = getDbConn();
  $query = 'SELECT id, passwordhash FROM users WHERE username = ?';
  $result = $conn->executeQuery($query, [ $username ]);
  $arr = $result->fetchAllNumeric();
  if (count($arr) == 1) {
    $id = intval($arr[0][0]);
    $passwordHash = $arr[0][1];
    $conclusion = password_verify($passwordGiven, $passwordHash);
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
  $result = $conn->executeStatement($query, [ $username, $passwordHash ]);
  // tableDump("users");
  return $conn->lastInsertId();
}

function getMovementsForUser($userId) {
  $conn  = getDbConn();
  $query = "SELECT m.* FROM movements m INNER JOIN statements s ON m.id = s.movementId WHERE s.userId = ?";
  $result = $conn->executeQuery($query, [ $userId ]);
  return $result->fetchAllAssociative();
}