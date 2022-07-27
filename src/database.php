<?php

declare(strict_types=1);
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../schema.php');

use Doctrine\DBAL\DriverManager;

function getDbConn()
{
    global $singleton_db_connection;
    if (isset($singleton_db_connection)) {
        return $singleton_db_connection;
    } else {
        $result = db_credentials();
        $singleton_db_connection = DriverManager::getConnection($result);
        return $singleton_db_connection;
    }
}

function db_credentials()
{
    $connectionParams = [
      'dbname' =>  $_SERVER["DB_DATABASE"],
      'user' =>  $_SERVER["DB_USER"],
      'password' => $_SERVER["DB_PASSWORD"],
      'host' => $_SERVER["DB_HOST"],
      'driver' =>  $_SERVER["DB_DRIVER"]
 ];
    return $connectionParams;
}

function setTestDb()
{
    $tables = getTables();

    $conn = getDbConn();

    for ($i = 0; $i < count($tables); $i++) {
        $conn->executeQuery($tables[$i]);
    }
}

function tableDump($tablename)
{ // for debugging
    $conn  = getDbConn();
    $query = "SELECT * FROM $tablename";
    $result = $conn->executeQuery($query);
    var_dump($result->fetchAllAssociative());
}

function validateUser($username, $passwordGiven)
{
    // echo "Validating user $username $passwordGiven";
    $conn  = getDbConn();
    $query = 'SELECT id, uuid,passwordhash FROM users WHERE username = ?';
    $result = $conn->executeQuery($query, [ $username ]);
    $arr = $result->fetchAllNumeric();
    //var_dump($arr);
    //exit;
    if (count($arr) == 1) {
        $id = intval($arr[0][0]);
        $uuid = strval($arr[0][1]);
        $passwordHash = $arr[0][2];
        $conclusion = password_verify($passwordGiven, $passwordHash);
        if ($conclusion) {
            return [
        "id" => $id,
        "username" => $username,
        "uuid" => $uuid,
      
      ];
        }
    }
    return null;
}

function createUser($username, $passwordGiven)
{
    $conn  = getDbConn();
    $passwordHash = password_hash($passwordGiven, PASSWORD_BCRYPT, [ "cost" => 10 ]);
    $query = "INSERT INTO users (username, passwordhash) VALUES (?, ?) RETURNING uuid;";
    $conn->executeStatement($query, [ $username, $passwordHash ]);
    // tableDump("users");

    foreach ($conn->iterateAssociativeIndexed('SELECT id, uuid, username FROM users') as $id => $data) {
        return "Your uuid is " . $data["uuid"] . " and username is " .$data["username"];
    }
    //return $conn->lastInsertId();
}

function getMovementsForUser($userId)
{
    $conn  = getDbConn();
    $query = "SELECT m.* FROM movements m INNER JOIN statements s ON m.id = s.movementId WHERE s.userId = :userid";
    $result = $conn->executeQuery($query, [ "userid" => $userId ]);
    return $result->fetchAllAssociative();
}

function getAllComponents()
{
    $conn  = getDbConn();
    $query = "SELECT * FROM components";
    $result = $conn->executeQuery($query);
    return $result->fetchAllAssociative();
}

function getAllComponentNames()
{
    $list =getAllComponents();
    $ret = [];
    for ($i = 0; $i < count($list); $i++) {
        $ret[$list[$i]["id"]] = $list[$i]["name"];
    }
    return $ret;
}

function getAllMovements()
{
    $conn  = getDbConn();
    $query = "SELECT * FROM movements";
    $result = $conn->executeQuery($query);
    return $result->fetchAllAssociative();
}

function getAllWorkedMovements()
{
    $conn  = getDbConn();
    $query = "SELECT * FROM movements WHERE type_='worked'";
    $result = $conn->executeQuery($query);
    return $result->fetchAllAssociative();
}

function getAllInvoiceMovements()
{
    $conn  = getDbConn();
    $query = "SELECT * FROM movements WHERE type_='invoice'";
    $result = $conn->executeQuery($query);
    return $result->fetchAllAssociative();
}

function getAllPaymentMovements()
{
    $conn  = getDbConn();
    $query = "SELECT * FROM movements WHERE type_='payment'";
    $result = $conn->executeQuery($query);
    return $result->fetchAllAssociative();
}

function getAllMovementsFromId($fromId)
{
    $conn  = getDbConn();
    $query = "SELECT m.*,s.description FROM movements m INNER JOIN statements s ON s.movementId = m.id WHERE fromComponent = :fromId";
    $result = $conn->executeQuery($query, [ "fromId" => $fromId ]);
    return $result->fetchAllAssociative();
}

function getAllStatements()
{
    $conn  = getDbConn();
    $query = "SELECT * FROM statements";
    $result = $conn->executeQuery($query);
    return $result->fetchAllAssociative();
}

function getAllStatementsWiki($remote_system)
{
    $conn  = getDbConn();
    $query = "SELECT * FROM statements WHERE remote_system=:remote_system";
    $result = $conn->executeQuery($query, ['remote_system' => $remote_system]);
    return $result->fetchAllAssociative();
}

function getUserId($username)
{
    $conn  = getDbConn();
    $query = "SELECT id FROM users WHERE username = :username";
    $result = $conn->executeQuery($query, [ "username" => $username ]);
    $arr = $result->fetchAllAssociative();
    if (count($arr) != 1) {
        return null;
    }
    return $arr[0]["id"];
};

function getUserName($id)
{
    $result = getDbConn()->executeQuery(
        "SELECT username FROM users WHERE id = :id",
        [ "id" => $id ]
    );
    return $result->fetchAllAssociative()[0]["username"];
}

function getComponentName($id)
{
    $result = getDbConn()->executeQuery(
        "SELECT name FROM components WHERE id = :id",
        [ "id" => $id ]
    );
    return $result->fetchAllAssociative()[0]["name"];
}

function getMovement($id)
{
    $result = getDbConn()->executeQuery(
        "SELECT * FROM movements WHERE id = :id",
        [ "id" => $id ]
    );
    return $result->fetchAllAssociative()[0];
}

function getSync($movementId, $internal_type, $remote_system)
{
    $result = getDbConn()->executeQuery(
        "SELECT * FROM statements WHERE movementId = :movementId OR internal_type = :internal_type OR remote_system = :remote_system",
        [ "movementId" => $movementId , 'internal_type' => $internal_type , 'remote_system' => $remote_system ]
    );
    $arr = $result->fetchAllAssociative();
    if (empty($arr)) {
        return null;
    }
    return  $arr[0];
}

function deleteDataFromMovement($type_, $id)
{
    $conn  = getDbConn();
    $qb = $conn
        ->delete('movements', ['type_' => $type_, 'id' => $id])
    ;

    if ($qb === 1) {
        return ["Delete data from movement"];
    }
}

function getComponentId($name, $atomic = false)
{
    $conn  = getDbConn();
    if ($name == '') {
        throw new Error('getting component id for empty name?');
    }
    if ($atomic) {
        // See https://dba.stackexchange.com/questions/129522/how-to-get-the-id-of-the-conflicting-row-in-upsert
        // FIXME: This doesn't seem to work as intended
        $result = $conn->executeQuery(
            "INSERT INTO components (name) VALUES (:name) "
      . "ON CONFLICT (id) DO UPDATE SET name = :name RETURNING id;",
            [ "name" => $name ]
        );
        $arr = $result->fetchAllAssociative();
    } else {
        $result = $conn->executeQuery(
            "SELECT id FROM components WHERE name = :name",
            [ "name" => $name ]
        );
        $arr = $result->fetchAllAssociative();
        if (count($arr) == 0) {
            $result = $conn->executeQuery(
                "INSERT INTO components (name) VALUES (:name)",
                [ "name" => $name ]
            );
            $result = $conn->executeQuery(
                "SELECT id FROM components WHERE name = :name",
                [ "name" => $name ]
            );
            $arr = $result->fetchAllAssociative();
        }
    }
    return $arr[0]["id"];
};

// FIXME: methods like this belong in some intermediate layer,
// they're not purely about database access, they also contain
// a lot of business logic
function getCycles($componentId, $cycleLength)
{
    if ($cycleLength < 2) {
        throw new Error("cycleLength should be at least 2");
    }
    $part1 = 'SELECT m1.amount AS amount, m1.id AS id1, m1.fromcomponent AS fc1';
    $part2 = ' FROM movements m1 ';
    $part3 = "WHERE m1.fromcomponent = :pivotId AND m$cycleLength.tocomponent = :pivotId";
    for ($i = 2; $i <= $cycleLength; $i++) {
        $part1 .= ", m$i.id AS id$i, m$i.fromcomponent AS fc$i";
        $part2 .= "INNER JOIN movements m$i ON m" . ($i-1) . ".tocomponent = m$i.fromcomponent ";
        $part3 .= " AND m" . ($i-1) . ".amount = m$i.amount AND m" . ($i-1) . ".timestamp_ = m$i.timestamp_";
    }
    $query = $part1 . $part2 . $part3;
    // echo $query;
    $conn = getDbConn();
    $res = $conn->executeQuery($query, [ 'pivotId' => $componentId ]);
    $ass = $res->fetchAllAssociative();
    $res = [];
    for ($i = 0; $i < count($ass); $i++) {
        for ($j = 1; $j <= $cycleLength; $j++) {
            array_push($res, $ass[$i]["id$j"]);
        }
    }
    return $res;
}

function getDescriptionFromStatement($movementId)
{
    $query = "SELECT description FROM statements WHERE movementid = :movementid";
    $res = getDbConn()->executeQuery($query, [ "movementid" => $movementId ]);
    $ass = $res->fetchAllAssociative();
    if (count($ass) > 0) {
        return $ass[0]["description"];
    }
    return "N/A";
}
