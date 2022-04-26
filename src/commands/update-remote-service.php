<?php declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');
  require_once(__DIR__ . '/helpers/updateScoro.php');
  /*
  E.g.: php src/cli-single update-remote-service scoro
  E.g.: php src/cli-single update-remote-service -a
*/
function updateRemoteService($context, $command) {
  if (isset($context["user"])) {
    $remote_service = $command[1];

    if($remote_service == "scoro"){
      updateScoro();
    }
    else if($remote_service == "-a"){
      updateAll();
    }
  } else {
    return ["User not found or wrong password"];
  }
}