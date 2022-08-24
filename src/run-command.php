<?php

declare(strict_types=1);
require_once(__DIR__ . '/platform.php');
require_once(__DIR__ . '/commands/hello.php');
require_once(__DIR__ . '/commands/register.php');
require_once(__DIR__ . '/commands/pta-me.php');
require_once(__DIR__ . '/commands/enter.php');
require_once(__DIR__ . '/commands/grant.php');
require_once(__DIR__ . '/commands/list-new.php');
require_once(__DIR__ . '/commands/list-payments.php');
require_once(__DIR__ . '/commands/import-hours.php');
require_once(__DIR__ . '/commands/import-bank-statement.php');
require_once(__DIR__ . '/commands/import-invoice.php');
require_once(__DIR__ . '/commands/minimal-version.php');
require_once(__DIR__ . '/commands/worked-hours.php');
require_once(__DIR__ . '/commands/worked-day.php');
require_once(__DIR__ . '/commands/worked-week.php');
require_once(__DIR__ . '/commands/submit-expense.php');
require_once(__DIR__ . '/commands/who-works-when.php');
require_once(__DIR__ . '/commands/update-remote-service.php');
require_once(__DIR__ . '/commands/comment.php');
require_once(__DIR__ . '/commands/loan.php');
require_once(__DIR__ . '/commands/what-the-world-owes.php');
require_once(__DIR__ . '/commands/wiki-api-export.php');
require_once(__DIR__ . '/commands/wiki-api-import.php');
require_once(__DIR__ . '/commands/print-timesheet-json.php');
require_once(__DIR__ . '/commands/print-timesheet-csv.php');
require_once(__DIR__ . '/commands/import-timesheet.php');
require_once(__DIR__ . '/commands/remove-entry.php');
require_once(__DIR__ . '/commands/update-entry.php');
require_once(__DIR__ . '/commands/print-timesheet-json.php');
require_once(__DIR__ . '/commands/print-timesheet-csv.php');
require_once(__DIR__ . '/commands/import-timesheet.php');
require_once(__DIR__ . '/commands/generate-implied-purchases.php');
require_once(__DIR__ . '/commands/timeld-api-import.php');
require_once(__DIR__ . '/commands/claim-component.php');

function toCamel($str)
{
    $parts = explode("-", $str);
    return implode("", array_map(function ($x) {
        return ucfirst($x);
    }, $parts));
}

function appendToCommandLog($context, $command) {
    // $context is e.g.:
    // [
    //     "user" => [
    //     "id" => 1,
    //     "username" => "admin"
    //     ],
    //     "adminParty" => false,
    //     "openMode" => false,
    //     "employer" => "stichting"
    // ]
    //
    // $command is e.g. [ "worked-hours", "20 September 2021", "stichting", "Peppol for the Masses", 4]
    $conn = getDbConn();
    $conn->executeStatement("INSERT INTO commandLog (contextJson, commandJson) VALUES (:contextJson, :commandJson)", [
        "contextJson" => json_encode($context),
        "commandJson" => json_encode($command)
    ]);
}

function runCommandWithInlineData($context, $command)
{
    // TODO: support this for more commands - maybe in some more generic way to pass the data?
    // Maybe command implementations shouldn't be doing their own file_get_contents
    // to make them reusable across both runCommand and runCommandWithInlineData
    error_log(var_export($command, true));
    appendToCommandLog($context, $command);
    if ($command[0] == "import-hours") {
        return importHoursInline($context, $command[1], $command[2], "2022-03-31 12:00:00");
    }
    throw new Error("command ${command[0]} does not support inline data yet!");
}

function runCommand($context, $command)
{
    appendToCommandLog($context, $command);
    // print("running " . json_encode($command));
    $commands = [
        "register" => 3,
        "hello" => 1,
        "pta-me" => 1,
        "enter" => 7,
        "grant" => 3,
        "list-new" => 1,
        "list-payments" => 1,
        "import-hours" => 4,
        "import-timesheet" => 4,
        "import-bank-statement" => 4,
        "import-invoice" => 4,
        "minimal-version" => 2,
        "worked-hours" => 5,
        "worked-day" => 4,
        "worked-week" => 4,
        "submit-expense" => 8,
        "who-works-when" => 1,
        "update-remote-service" => 2,
        "wiki-api-export" => 2,
        "wiki-api-import" => 2,
        "print-timesheet-json" => 3,
        "remove-entry" => 2,
        "update-entry" => 2,
        "print-timesheet-csv" => 2,
        "print-timesheet-json" => 2,
        "print-timesheet-csv" => 2,
        "comment" => 1,
        "loan" => 4,
        "what-the-world-owes" => 2,
        "generate-implied-purchases" => 3,
        "timeld-api-import" => 2,
        "claim-component" => 2
    ];
    if (isset($commands[$command[0]]) && count($command) >= $commands[$command[0]]) {
        $function = toCamel($command[0]);
        // print("running $function");
        return $function($context, $command);
    }
    // Support original 1.1 version of submit-expense command,
    // see https://github.com/pondersource/prejournal/issues/53#issuecomment-1107842489
    if ($command[0] == 'submit-expense' && count($command) == 7) {
        // print("legacy expense");
        return submitExpense($context, [
            $command[0], // 'submit-expense'
            $command[1], // '28 August 2021'
            $context['employer'], // 'stichting'
            $command[2], // 'Dutch Railways'
            $command[3], // 'Degrowth Conference train tickets'
            $command[4], // 'transport'
            $command[5], // '100'
            $command[6] // 'michiel'
        ]);
    }
    if ($command[0] == '' && count($command) == 1) {
        // print("blank line");
        return ['Blank link in batch file'];
    }
    // print("unrecognised");
    return ['Unrecognized command ' . var_export($command, true)];
}

function runCommands($context, $commands)
{
    foreach ($commands as $command) {
        runCommand($context, $command);
    }
}
