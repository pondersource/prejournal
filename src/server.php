<?php

declare(strict_types=1);
//   require_once(__DIR__ . '/../loadenv.php');
  require_once(__DIR__ . '/platform.php');
  require_once(__DIR__ . '/run-command.php');


// ...
if (getMode() === 'batch') {
    $handle = getBatchHandle(false);
    error_log("Executing batch file");
    while (($line = fgets($handle)) !== false) {
        $words = explode(" ", trim($line));
        error_log("Command from batch file " . var_export($words, true));
        output(runCommand(getContext(), reconcileQuotes($words)));
    }
    fclose($handle);
} elseif (getMode() === 'upload') {
    output(runCommandWithInlineData(getContext(), getUploadCommand()));
} else {
    output(runCommand(getContext(), getCommand()));
}
