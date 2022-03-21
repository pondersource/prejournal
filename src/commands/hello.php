<?php declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');

function hello($command) {
  $user = getUser();
  if ($user) {
    $username = $user['username'];
    $userId = $user['id'];
    return ["Hello $username, your userId is $userId"];
  } else {
    return ["User not found or wrong password"];
  }
}