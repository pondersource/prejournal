<?php

declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');

function hello($context, $command)
{
    if (isset($context["user"])) {
        $username = $context["user"]["username"];
        $userId = $context["user"]["id"];
        return ["Hello $username, your userId is $userId"];
    } else {
        return ["User not found or wrong password"];
    }
}
