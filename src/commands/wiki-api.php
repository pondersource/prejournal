<?php declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');
  require_once(__DIR__ . '/helpers/services/getWiki.php');
  require_once(__DIR__ . '/helpers/createSync.php');
  require_once(__DIR__ . '/../database.php');
  /*
  E.g.: php src/cli-single.php wiki-api wiki
*/
function wikiApi($context, $command) {
  if (isset($context["user"])) {
    $remote_system = $command[1];
    //var_dump($remote_system);
    //exit;
    $movements = getDbConn()->executeQuery("SELECT * from movements  WHERE type_='worked'");
    //if($remote_system == "wiki"){
      foreach($movements->fetchAllAssociative() as $movement){
        $movement_id = $movement["id"];
        //var_dump($movement);
        $internal_type = 'movement';
        $remote_system = 'wiki';
        $sync = getSync($movement_id,$internal_type,$remote_system);
        /* Check if there is synchronization between prejournal and remote system */
        if($sync == null ){
          $remote_id = getWiki($movement_id,null);
          createSync($context, [
            "movement",
            $movement_id,
            $remote_id,
            "wiki"
          ])[0];
        }else{

          $remote_id = getWiki($movement_id,$sync["remote_id"]);
        }
      }
    //}
    return ["Wiki updated"];
  } else {
    return ["User not found or wrong password"];
  }
}
