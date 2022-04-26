<?php declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');
  require_once(__DIR__ . '/helpers/services/updateScoro.php');
  require_once(__DIR__ . '/../database.php');
  /*
  E.g.: php src/cli-single.php  update-remote-service scoro
  E.g.: php src/cli-single.php update-remote-service -a
*/
function updateRemoteService($context, $command) {
  if (isset($context["user"])) {
    $remote_system = $command[1];
    $movements = getDbConn()->executeQuery("SELECT * from movements  WHERE type_='worked'");

    if($remote_system == "scoro"){
      
      foreach($movements as $movement){
        $movement_id = $movement["id"];
        $sync = getSyncByInternalID($movement_id);

        /* Check if there is synchronization between prejournal and remote system */
        if($sync == []){
          $remote_id = updateScoro($movement_id,null);
          intval(createSync($context, [
            "movement",
            $movement_id,
            $remote_id,
            "scoro"
          ])[0]);
        }
      }
    }
  } else {
    return ["User not found or wrong password"];
  }
}