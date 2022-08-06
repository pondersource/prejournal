<?php

declare(strict_types=1);
  require_once(__DIR__ . '/../../platform.php');
  require_once(__DIR__ . '/../../utils.php');

function createStatement($context, $command)
{
    if (isset($context["user"])) {
        $conn  = getDbConn();
        $query = "INSERT INTO statements " .
          "(userId, movementId, timestamp_, description, sourceDocumentFormat, sourceDocumentFilename) VALUES " .
          "(:userId, :movementId, :timestamp_, :description, :sourceDocumentFormat, :sourceDocumentFilename);";
        $ret = $conn->executeStatement($query, [
      "userId" => $context["user"]["id"],
      "movementId" => intval($command[1]),
      "timestamp_" => timestampToDateTime(intval($command[2])),
      "description" => $command[3] ?? null,
      "sourceDocumentFormat" => $command[4] ?? null,
      "sourceDocumentFilename" => $command[5] ?? null,
    ]);
        return [ strval($conn->lastInsertId()) ];
    } else {
        return ["User not found or wrong password"];
    }
}

function ensureStatement($context, $command)
{
    if (isset($context["user"])) {
        $conn  = getDbConn();
        $params = [
          "userId" => $context["user"]["id"],
          "movementId" => intval($command[1]),
          "timestamp_" => timestampToDateTime(intval($command[2])),
          "description" => $command[3] ?? null,
          "sourceDocumentFormat" => $command[4] ?? null,
          "sourceDocumentFilename" => $command[5] ?? null,
        ];

        $query1 = "SELECT count(*) FROM statements " .
          "WHERE userId = :userId AND movementId = :movementId AND " .
          "timestamp_ = :timestamp_ AND description = :description AND " .
          "sourceDocumentFormat = :sourceDocumentFormat AND " .
          "sourceDocumentFilename = :sourceDocumentFilename;";
        $query2 = "INSERT INTO statements " .
          "(userId, movementId, timestamp_, description, sourceDocumentFormat, sourceDocumentFilename) VALUES " .
          "(:userId, :movementId, :timestamp_, :description, :sourceDocumentFormat, :sourceDocumentFilename);";
        $ret1 = $conn->executeQuery($query1, $params);
        $numExist = $ret1->fetchAllAssociative()[0]["count"];
        if ($numExist == 0) {
            $ret2 = $conn->executeStatement($query2, $params);
            // echo "Statement $created was created about movement " . intval($command[1]) . "\n";
            $created = strval($conn->lastInsertId());
            return [ "created $created" ];
        }
        return [ "already exists" ];
    } else {
        return ["User not found or wrong password"];
    }
}
