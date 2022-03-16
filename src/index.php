<?php
  require(__DIR__ . '/platform.php');

  $command = getCommand();
  if ($command[0] == 'register' && count($command) == 3) {
    output(createUser($command[1], $command[2]));
  } else if ($command[0] == 'hello' && count($command) == 1) {
      $user = getUser();
      if ($user) {
        $username = $user['username'];
        $userId = $user['id'];
        output("Hello $username, your userId is $userId");
      } else {
        output("User not found");
      }
  } else {
    output('Unrecognized command ' . var_export($command, true));
  }
