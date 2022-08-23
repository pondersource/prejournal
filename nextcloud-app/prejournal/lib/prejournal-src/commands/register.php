<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');

function isAdmin($context)
{
    if ($context["adminParty"]) {
        return true;
    }
    if (isset($context["user"]) && isset($context["user"]["username"])) {
        return ($context["user"]["username"] == "admin");
    }
}

// For now, only the super-admin can register users.
function register($context, $command)
{
    if (isAdmin($context)) {
        return [ strval(createUser($command[1], $command[2])) ];
    } else {
        return ['Only admins can register new users'];
    }
}
