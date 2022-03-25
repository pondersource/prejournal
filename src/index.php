<?php declare(strict_types=1);
  require_once(__DIR__ . '/platform.php');
  require_once(__DIR__ . '/commands/hello.php');
  require_once(__DIR__ . '/commands/register.php');
  require_once(__DIR__ . '/commands/pta-me.php');
  require_once(__DIR__ . '/commands/enter.php');
  require_once(__DIR__ . '/commands/grant.php');
  require_once(__DIR__ . '/commands/list-new.php');

  $command = getCommand();
  $context = getContext();
// var_dump($command);

// publically accessible commands:
if (count($command) == 3 && $command[0] == 'register') {
  output(register($context, $command));
// commands requiring a logged-in user
} else  if (count($command) == 1 && $command[0] == 'hello') {
  output(hello($context, $command));
} else if (count($command) == 1 && $command[0] == 'pta-me') {
  output(ptaMe($context, $command));
} else if (count($command) == 7 && $command[0] == 'enter') {
  output(enter($context, $command));
} else if (count($command) == 3 && $command[0] == 'grant') {
  output(grant($context, $command));
} else if (count($command) == 1 && $command[0] == 'list-new') {
  output(listNew($context, $command));
} else {
  output(['Unrecognized command ' . var_export($command, true)]);
}
