<?php
  require_once(__DIR__ . '/platform.php');
  require_once(__DIR__ . '/commands/hello.php');
  require_once(__DIR__ . '/commands/register.php');

  $command = getCommand();
  if ($command[0] == 'register' && count($command) == 3) {
    register($command);
  } else if ($command[0] == 'hello' && count($command) == 1) {
      hello($command);
  } else {
    output('Unrecognized command ' . var_export($command, true));
  }
