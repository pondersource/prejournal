<?php

declare(strict_types=1);
require_once(__DIR__ . '/../../database.php');

function getFromMovementAndSync($userId, $project, $min_id, $max_id) {
  $conn  = getDbConn();
  $componentId = getComponentId($project);
  echo("executing query 'SELECT fromuser FROM componentgrants WHERE touser = :userId AND componentid = :componentId'");
  var_dump([ 'userId' => $userId, 'componentId' => $componentId ]);
  $grantsResult = $conn->executeQuery("SELECT fromuser FROM componentgrants " .
    "WHERE touser = :userId AND componentid = :componentId",
    [ 'userId' => $userId, 'componentId' => $componentId ]);
  $grants = $grantsResult->fetchAllAssociative();
  $visibleUsers = [ $userId ];

  for ($i = 0; $i < count($grants); $i++) {
    array_push($visibleUsers, $grants[$i]["fromuser"]);
  }

  $query = $conn->executeQuery("SELECT m.id, w.name as worker, p.name as project, m.timestamp_, m.amount,
    m.description FROM movements m INNER JOIN components w ON m.fromComponent = w.id
    INNER JOIN components p ON m.toComponent = p.id 
    WHERE m.type_='worked' AND p.name=:project AND m.id >=:min_id AND m.id <=:max_id
    AND w.id IN (:visibleUsers)",
    [ 'project' => $project, 'min_id' => $min_id, 'max_id' => $max_id, 'visibleUsers' => $visibleUsers ],
    [ 'visibleUsers' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY ]
  );
  //$result = $conn->executeQuery($query);
  $arr = $query->fetchAllAssociative();
  return $arr;
}
