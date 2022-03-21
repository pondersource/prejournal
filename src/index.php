<?php declare(strict_types=1);
  require_once(__DIR__ . '/platform.php');
  require_once(__DIR__ . '/commands/hello.php');
  require_once(__DIR__ . '/commands/register.php');
  require_once(__DIR__ . '/commands/pta-me.php');

  $command = getCommand();
  
  if (count($command) == 3 && $command[0] == 'register') {
    output(register($command));
  } else if (count($command) == 1 && $command[0] == 'hello') {
    output(hello($command));
  } else if (count($command) == 1 && $command[0] == 'pta-me') {
    output(ptaMe($command));
  } else {
    output('Unrecognized command ' . var_export($command, true));
  }
