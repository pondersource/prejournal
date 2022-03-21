<?php declare(strict_types=1);
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
    if (createUser($command[1], $command[2])) {
        return ['created user'];
    } else {
      return ['failed to create user'];
    };
  } else {
    return ['Only admins can register new users'];
  }
}