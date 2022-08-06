<?php

declare(strict_types=1);
require_once(__DIR__ . '/../../../platform.php');
require_once(__DIR__ . '/../../../database.php');
require_once(__DIR__ . '/../../../api/wiki.php');

function getWiki()
{
    //$movement = getMovement($movement_id);
    //var_dump($movement);

    return exportWikiFile();
}

function importWiki()
{
    //$movement = getMovement($movement_id);
    //var_dump($movement);

    return importWikiFile();
}
