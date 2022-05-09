<?php declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');

function comment($context, $command) {
  return ["Comment: " . $command[1]];
}