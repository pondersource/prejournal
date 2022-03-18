<?php
  require_once(__DIR__ . '/platform.php');
  require_once(__DIR__ . '/commands/hello.php');
  require_once(__DIR__ . '/commands/register.php');

  $command = getCommand();
  
  if (count($command) == 3 && $command[0] == 'register') {
    register($command);
  } else if (count($command) == 1 && $command[0] == 'hello') {
      hello($command);
  } else {
    output('Unrecognized command ' . var_export($command, true));
  }
