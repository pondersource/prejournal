<?php
  require(__DIR__ . '/platform.php');

  $command = getCommand();
  if ($command[0] == 'src/register.php' && count($command) == 3) {
      $result = createUser($command[1], $command[2]);
  } else {
    $result = 'Unrecognized command ' . var_export($command, true);
  }

  output($result);