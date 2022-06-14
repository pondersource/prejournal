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
        //var_dump($remote_system);
        //exit;
        $type = "worked";
        $remote_id =  importWiki();
        var_dump($remote_id);
        exit;
    }
}
