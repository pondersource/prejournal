<?php

declare(strict_types=1);
require_once(__DIR__ . '/../loadenv.php');
require_once(__DIR__ . '/platform.php');
require_once(__DIR__ . '/run-command.php');

function runCliSingle()
{
    readDotEnv();
    //var_dump(getContext());
    output(runCommand(getContext(), getCommand()));
}

// ...
runCliSingle();
