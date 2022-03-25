<?php declare(strict_types=1);
  require_once(__DIR__ . '/../database.php');

function listNew($context, $command) {
  if (isset($context["user"])) {
    $movements = getDbConn()->executeQuery("SELECT m.*,s.userId from movements m INNER JOIN statements s ON s.movementId = m.id "
      . "INNER JOIN componentGrants g ON g.fromUser = s.userId "
      . "WHERE ((g.componentId = m.fromComponent) OR (g.componentId = m.fromComponent))"
      . "AND g.toUser = :userId;", [ "userId" => $context["user"]["id"] ]);
    return array_map(function($row) {
      // var_dump($row);
      foreach(["fromComponent", "toComponent", "userId"] as $columnName) {
        // sqlite preserves case in column names but
        // pg returns lower case column name
        if (!isset($row[$columnName])) {
          $row[$columnName] = $row[strtolower($columnName)];
        }
      }
      $timestamp_ = strtotime($row['timestamp_']);
      $fromComponentName = getComponentName($row['fromComponent']);
      $toComponentName = getComponentName($row['toComponent']);
      $amount = $row['amount'];
      $observer = getUserName($row['userId']);
      return "$timestamp_, $fromComponentName, $toComponentName, $amount, $observer";
    }, $movements->fetchAllAssociative());
    // var_dump($ret);
    // return $ret;
    // $recipientUserId = getUserId($command[1]);
    // $componentId = getComponentId($command[2]);
    // $grantId = getDbConn();
    // return [strval($grantId)];
  } else {
    return ["User not found or wrong password"];
  }
}