<?php declare(strict_types=1);
require_once (__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../schema.php');

use Doctrine\DBAL\DriverManager;

function getDbConn() {
  global $test_db_connection;
  if (isset($test_db_connection)) {
    return $test_db_connection;
  } else {
    return DriverManager::getConnection([
      'url' => $_SERVER["DATABASE_URL"],
      'persistent' => true
    ]);
  }
}

function setTestDb() {
  global $test_db_connection;
  $tables = getTables();
  $test_db_connection = DriverManager::getConnection([
    //'driver' => 'pdo_sqlite', 'memory' => true
     'driver' => 'pdo_pgsql' // for debugging, seeing database contents on localhost postgresql server using "psql postgres"
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
  $query = "SELECT m.* FROM movements m INNER JOIN statements s ON m.id = s.movementId WHERE s.userId = :userid";
  $result = $conn->executeQuery($query, [ "userid" => $userId ]);
  return $result->fetchAllAssociative();
}

function getAllComponents() {
  $conn  = getDbConn();
  $query = "SELECT * FROM components";
  $result = $conn->executeQuery($query);
  return $result->fetchAllAssociative();
}

function getAllComponentNames() {
  $list =getAllComponents();
  $ret = [];
  for ($i = 0; $i < count($list); $i++) {
    $ret[$list[$i]["id"]] = $list[$i]["name"];
  }
  return $ret;
}

function getAllMovements() {
  $conn  = getDbConn();
  $query = "SELECT * FROM movements";
  $result = $conn->executeQuery($query);
  return $result->fetchAllAssociative();
}

function getAllStatements() {
  $conn  = getDbConn();
  $query = "SELECT * FROM statements";
  $result = $conn->executeQuery($query);
  return $result->fetchAllAssociative();
}

function getAllSync() {
  $conn  = getDbConn();
  $query = "SELECT * FROM sync";
  $result = $conn->executeQuery($query);
  return $result->fetchAllAssociative();
}

function getUserId($username) {
  $conn  = getDbConn();
  $query = "SELECT id FROM users WHERE username = :username";
  $result = $conn->executeQuery($query, [ "username" => $username ]);
  $arr = $result->fetchAllAssociative();
  if (count($arr) != 1) {
    return NULL;
  }
  return $arr[0]["id"];
};

function getUserName($id) {
  $result = getDbConn()->executeQuery("SELECT username FROM users WHERE id = :id",
    [ "id" => $id ]
  );
  return $result->fetchAllAssociative()[0]["username"];
}

function getComponentName($id) {
  $result = getDbConn()->executeQuery("SELECT name FROM components WHERE id = :id",
    [ "id" => $id ]
  );
  return $result->fetchAllAssociative()[0]["name"];
}

function getComponentId($name, $atomic = false) {
  $conn  = getDbConn();
  if ($atomic) {
    // See https://dba.stackexchange.com/questions/129522/how-to-get-the-id-of-the-conflicting-row-in-upsert
    // FIXME: This doesn't seem to work as intended
    $result = $conn->executeQuery("INSERT INTO components (name) VALUES (:name) "
      . "ON CONFLICT (id) DO UPDATE SET name = :name RETURNING id;",
      [ "name" => $name ]
    );
    $arr = $result->fetchAllAssociative();
  } else {
    $result = $conn->executeQuery("SELECT id FROM components WHERE name = :name",
      [ "name" => $name ]
    );
    $arr = $result->fetchAllAssociative();
    if (count($arr) == 0) {
      $result = $conn->executeQuery("INSERT INTO components (name) VALUES (:name)",
        [ "name" => $name ]
      );
      $result = $conn->executeQuery("SELECT id FROM components WHERE name = :name",
        [ "name" => $name ]
      );
      $arr = $result->fetchAllAssociative();
    }
  }
  return $arr[0]["id"];
};

function getSyncByInternalID($internal_id) {
  $conn  = getDbConn();
  $query = "SELECT * FROM sync WHERE internal_id = :internal_id  ";
  $result = $conn->executeQuery($query);
  return $result->fetchAllAssociative();
}

