<?php

declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');
  require_once(__DIR__ . '/helpers/services/getWiki.php');
  require_once(__DIR__ . '/helpers/createSync.php');
  require_once(__DIR__ . '/../database.php');
/*
  E.g.: php src/cli-single.php wiki-api-import wiki
*/
function wikiApiImport($context, $command)
{
    if (isset($context["user"])) {
        $remote_system = $command[1];
     
        $movements = getAllWorkedMovements();
        if ($remote_system == "wiki") {
          $remote_id = importWiki();
            //echo $remote_id;
            foreach ($movements as $movement) {
              $movement_id = $movement["id"];
              $internal_type = 'movement';
              $remote_system = 'wiki';
              $sync = getSync($movement_id, $internal_type, $remote_system);
              if ($sync == null) {
                createSync($context, [
                    "movement",
                    $movement_id,
                    "wiki"
                ])[0];
            }
          }
          echo $remote_id;
        }
    }
}
