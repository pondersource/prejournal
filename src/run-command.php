<?php declare(strict_types=1);
  require_once(__DIR__ . '/platform.php');
  require_once(__DIR__ . '/commands/hello.php');
  require_once(__DIR__ . '/commands/register.php');
  require_once(__DIR__ . '/commands/pta-me.php');
  require_once(__DIR__ . '/commands/enter.php');
  require_once(__DIR__ . '/commands/grant.php');
  require_once(__DIR__ . '/commands/list-new.php');
  require_once(__DIR__ . '/commands/import-hours.php');
  require_once(__DIR__ . '/commands/import-bank-statement.php');
  require_once(__DIR__ . '/commands/import-timetip.php');
  require_once(__DIR__ . '/commands/minimal-version.php');

function toCamel($str) {
    $parts = explode("-", $str);
    return implode("", array_map(function($x) {
      return ucfirst($x);
    }, $parts));
}

function runCommand($context, $command)
{
    $commands = [
        "register" => 3,
        "hello" => 1,
        "pta-me" => 1,
        "enter" => 7,
        "grant" => 3,
        "list-new" => 1,
        "import-hours" => 4,
        "import-bank-statement" => 4,
        "import-timetip" => 4,
        "minimal-version" => 2,
    ];
    if (isset($commands[$command[0]]) && count($command) == $commands[$command[0]]) {
        $function = toCamel($command[0]);
        return $function($context, $command);
    }
    return ['Unrecognized command ' . var_export($command, true)];
}

function runCommands($context, $commands) {
    foreach ($commands as $command) {
        runCommand($context, $command);
    }
}