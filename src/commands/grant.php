<?php declare(strict_types=1);
  require_once(__DIR__ . '/../database.php');

function grant($context, $command) {
  if (isset($context["user"])) {
    // $componentId = getComponentId($command[2]);
    // echo "got componentId $componentId for command ".$command[2];
    $grantId = getDbConn()->executeQuery("INSERT INTO componentGrants (fromUser, toUser, componentId)"
      . "VALUES (:fromUser, :toUser, :componentId) RETURNING id;", [
        "fromUser" => $context["user"]["id"],
        "toUser" => getUserId($command[1]),
        "componentId" =>  getComponentId($command[2])
      ])->fetchAllAssociative()[0]["id"];
    return [strval($grantId)];
  } else {
    return ["User not found or wrong password"];
  }
}