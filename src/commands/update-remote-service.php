<?php declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');
  require_once(__DIR__ . '/helpers/services/updateScoro.php');
  require_once(__DIR__ . '/helpers/createSync.php');
  require_once(__DIR__ . '/../database.php');
  /*
  E.g.: php src/cli-single.php  update-remote-service scoro
*/
function updateRemoteService($context, $command) {
  if (isset($context["user"])) {
    $remote_system = $command[1];
    $movements = getDbConn()->executeQuery("SELECT * from movements  WHERE type_='worked'");

    if($remote_system == "scoro"){
      foreach($movements->fetchAllAssociative() as $movement){
        $movement_id = $movement["id"];
        $internal_type = 'movement';
        $remote_system = 'scoro';
        $sync = getSync($movement_id,$internal_type,$remote_system);
        /* Check if there is synchronization between prejournal and remote system */
        if($sync == null ){
          $remote_id = updateScoro($movement_id,null);
          createSync($context, [
            "movement",
            $movement_id,
            $remote_id,
            "scoro"
          ])[0];
        }else{

          $remote_id = updateScoro($movement_id,$sync["remote_id"]);
        }
      }
    }
    return ["Scoro updated"];
  } else {
    return ["User not found or wrong password"];
  }
}