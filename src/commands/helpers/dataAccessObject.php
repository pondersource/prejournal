<?php

declare(strict_types=1);
require_once(__DIR__ . '/../../database.php');

function getFromMovementAndSync($userId, $project, $min_id, $max_id)
{
    $conn  = getDbConn();
    $componentId = getComponentId($project);
    // echo("executing query 'SELECT fromuser FROM componentgrants WHERE touser = :userId AND componentid = :componentId'");
    // var_dump([ 'userId' => $userId, 'componentId' => $componentId ]);
    $grantsResult = $conn->executeQuery(
        "SELECT fromuser FROM componentgrants " .
    "WHERE touser = :userId AND componentid = :componentId",
        [ 'userId' => $userId, 'componentId' => $componentId ]
    );
    $grants = $grantsResult->fetchAllAssociative();
    $visibleWorkerUsers = [ $userId ];

    for ($i = 0; $i < count($grants); $i++) {
        array_push($visibleWorkerUsers, $grants[$i]["fromuser"]);
    }
    error_log('visible worker users: ' . var_export($visibleWorkerUsers, true));
    $visibleWorkerComponentsResult = $conn->executeQuery(
        "SELECT c.id, c.name FROM components c INNER JOIN users u ON c.name = u.username WHERE u.id in (:visibleWorkerUsers)",
        [ 'visibleWorkerUsers' => $visibleWorkerUsers ],
        [ 'visibleWorkerUsers' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY ]
    );
    $visibleWorkerComponentsAssoc = $visibleWorkerComponentsResult->fetchAllAssociative();
    $visibleWorkerComponents = [];
    for ($i = 0; $i < count($visibleWorkerComponentsAssoc); $i++) {
        array_push($visibleWorkerComponents, $visibleWorkerComponentsAssoc[$i]["id"]);
    }
    error_log('visible worker components: ' . var_export($visibleWorkerComponents, true));
    $queryStr = "SELECT m.id, w.name as worker, p.name as project, m.timestamp_, m.amount FROM movements m INNER JOIN components w ON m.fromComponent = w.id
  INNER JOIN components p ON m.toComponent = p.id 
  WHERE m.type_='worked' AND m.userId IN (:visibleWorkerUsers) AND p.name=:project AND m.id >=:min_id AND m.id <=:max_id
  AND w.id IN (:visibleWorkerComponents)";
    $params = [
        'userId' => $userId,
        'project' => $project,
        'min_id' => $min_id,
        'max_id' => $max_id,
        'visibleWorkerUsers' => $visibleWorkerUsers,
        'visibleWorkerComponents' => $visibleWorkerComponents
    ];
    // var_dump($queryStr);
    // var_dump($params);
    $query = $conn->executeQuery(
        $queryStr,
        $params,
        [
            'visibleWorkerUsers' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY,
            'visibleWorkerComponents' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY
        ]
    );
    //$result = $conn->executeQuery($query);
    $arr = $query->fetchAllAssociative();
    return $arr;
}
