<?php declare(strict_types=1);
  require_once(__DIR__ . '/platform.php');
  require_once(__DIR__ . '/run-command.php');


// ...
if (getMode() === 'batch') {
  $handle = getBatchHandle();
  while (($line = fgets($handle)) !== false) {
      $words = explode(" ", trim($line));

      output(runCommand(getContext(), reconcileQuotes($words)));
  }
  fclose($handle);
} else {
  output(runCommand(getContext(), getCommand()));

}