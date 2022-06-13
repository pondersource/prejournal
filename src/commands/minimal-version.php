<?php

declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');

function minimalVersion($context, $command)
{
    $currentVersion = explode(".", json_decode(file_get_contents(__DIR__ . '/../../composer.json'))->version);
    $required = explode(".", $command[1]);

    for ($i = 0; $i < count($required); $i++) {
        if ($currentVersion[$i] > $required[$i]) {
            return [ $currentVersion[$i] . ">" . $required[$i] . " in position $i"];
        }
        if ($currentVersion[$i] < $required[$i]) {
            throw new Error("Version " . implode(".", $currentVersion) . "<" . implode(".", $required) . " in position $i");
        }
    }
    return ["exact match"];
}
