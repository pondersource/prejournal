<?php declare(strict_types=1);
require_once(__DIR__ . '/../../platform.php');
require_once(__DIR__ . '/../../utils.php');

function createComponent($context, $command) {
  if (isset($context["user"])) {
    $conn  = getDbConn();
    $query = "INSERT INTO components (name) "
       . "VALUES (:name);";

    $ret = $conn->executeStatement($query, [
      "name" => $command[1],
    ]);
    return [ strval($conn->lastInsertId()) ];
  } else {
    return ["User not found or wrong password"];
  }
}