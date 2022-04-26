<?php declare(strict_types=1);
  require_once(__DIR__ . '/../../platform.php');
  require_once(__DIR__ . '/../../utils.php');

function createSync($context, $command) {
  if (isset($context["user"])) {
    $conn  = getDbConn();
    $query = "INSERT INTO sync (internal_type, internal_id, remote_id, remote_system) VALUES (:internal_type, :internal_id, :remote_id, :remote_system);";
    $ret = $conn->executeSync($query, [
      "internal_type" => $command[1],
      "internal_id" => intval($command[2]),
      "remote_id" => intval($command[3]),
      "remote_system" => $command[4],
    ]);
    return [ strval($conn->lastInsertId()) ];
  } else {
    return ["User not found or wrong password"];
  }
}