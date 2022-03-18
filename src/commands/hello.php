<?php
  require_once(__DIR__ . '/../platform.php');

function hello($command) {
  $user = getUser();
  if ($user) {
    $username = $user['username'];
    $userId = $user['id'];
    output("Hello $username, your userId is $userId");
  } else {
    output("User not found or wrong password");
  }
}