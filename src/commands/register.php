<?php
  require_once(__DIR__ . '/../platform.php');

function isAdmin() {
  if (isset($_ENV["PREJOURNAL_ADMIN_PARTY"]) && $_ENV["PREJOURNAL_ADMIN_PARTY"] == "true") {
    return true;
  }
  $user = getUser();
  return ($user["username"] == "admin");
}

// For now, only the super-admin can register users.
function register($command) {
  if (isAdmin()) {
    output(createUser($command[1], $command[2]));
  } else {
    output('Only admins can register new users');
  }
}