<?php declare(strict_types=1);
  require_once(__DIR__ . '/platform.php');
  require_once(__DIR__ . '/run-command.php');

if (count($_SERVER["argv"]) != 2) {
  echo "Usage: php src/batch.php example.pj\n";
  exit();
}

function reconcileQuotes($x) {
  // todo: implement;
  return $x;
}

$handle = fopen($_SERVER["argv"][1], "r");
while (($line = fgets($handle)) !== false) {
    $words = explode(" ", trim($line));

    output(runCommand(getContext(), reconcileQuotes($words)));
}
fclose($handle);