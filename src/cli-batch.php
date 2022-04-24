<?php declare(strict_types=1);
  require_once(__DIR__ . '/platform.php');
  require_once(__DIR__ . '/run-command.php');
  require_once(__DIR__ . '/utils.php');

function runCliBatch() {
  if (count($_SERVER["argv"]) != 2) {
    echo "Usage: php src/batch.php example.pj\n";
    exit();
  }

  readDotEnv();
  $handle = getBatchHandle(true);
  while (($line = fgets($handle)) !== false) {
    $words = explode(" ", trim($line));
    
    output(runCommand(getContext(), reconcileQuotes($words)));
  }
  fclose($handle);
}

// ...
runCliBatch();