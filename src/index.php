<?php declare(strict_types=1);
  require_once(__DIR__ . '/platform.php');
  require_once(__DIR__ . '/run-command.php');

  // ...
  $result = runCommand(getContext(), getCommand());
  if($result != null ){
    output($result);
  }