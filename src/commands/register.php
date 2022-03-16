<?php
  require_once(__DIR__ . '/../platform.php');

function register($command) {
  output(createUser($command[1], $command[2]));
}