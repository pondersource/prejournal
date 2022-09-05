<?php

declare(strict_types=1);
require_once(__DIR__ . '/../../database.php');

// returns all movements from any user to a given project
// ATTENTION! Only expose this if PREJOURNAL_OPEN_MODE or PREJOURNAL_ADMIN_PARTY is set!
function getFromMovementAndSync($project, $min_id, $max_id)
{
    $conn  = getDbConn();
    
    if($project === null) {
        $queryStr = "SELECT m.id, w.name as worker, m.timestamp_, m.amount, s.description FROM movements m INNER JOIN components w ON m.fromComponent = w.id
        INNER JOIN statements s ON m.id = s.movementId
        WHERE m.type_='worked' AND m.id >=:min_id AND m.id <=:max_id";
    $params = [ 'min_id' => $min_id, 'max_id' => $max_id ];
    // var_dump($queryStr);
    // var_dump($params);
    $query = $conn->executeQuery(
        $queryStr,
        $params,
    );
    } else {
        //$componentId = strval(getComponentId($project));
        //var_dump($componentId);
        $queryStr = "SELECT m.id, w.name as worker, p.name as project,  m.timestamp_, m.amount, s.description FROM movements m INNER JOIN components w ON m.fromComponent = w.id
            INNER JOIN components p ON m.toComponent = p.id 
            INNER JOIN statements s ON m.id = s.movementId
            WHERE m.type_='worked' AND p.name = :project AND m.id >=:min_id AND m.id <=:max_id";
        $params = [ 'project' => $project, 'min_id' => $min_id, 'max_id' => $max_id ];
        // var_dump($queryStr);
        // var_dump($params);
        $query = $conn->executeQuery(
            $queryStr,
            $params,
        );
    }
    //$result = $conn->executeQuery($query);
    $arr = $query->fetchAllAssociative();
    return $arr;
}

// SELECT m.id, w.name as worker, m.timestamp_, m.amount, s.description FROM movements m INNER JOIN components w ON m.fromComponent = w.id INNER JOIN statements s ON m.id = s.movementId WHERE m.type_='worked' AND m.tocomponent = 3 AND m.id >= 0 AND m.id <= 1000000;
