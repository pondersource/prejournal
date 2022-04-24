<?php declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');
  require_once(__DIR__ . '/../utils.php');

  function createComponent($context, $command) {
  if (isset($context["user"])) {
    $conn  = getDbConn();
    $query = "INSERT INTO components (id, name) "
       . "VALUES (:id, :name);";

    $ret = $conn->executeStatement($query, [
      "id" => $command[0],
    ]);
    return [ strval($conn->lastInsertId()) ];
  } else {
    return ["User not found or wrong password"];
  }
}