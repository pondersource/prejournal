<?php declare(strict_types=1);
  require_once(__DIR__ . '/platform.php');
  require_once(__DIR__ . '/commands/hello.php');
  require_once(__DIR__ . '/commands/register.php');
  require_once(__DIR__ . '/commands/pta-me.php');
  require_once(__DIR__ . '/commands/enter.php');
  require_once(__DIR__ . '/commands/grant.php');
  require_once(__DIR__ . '/commands/list-new.php');
  require_once(__DIR__ . '/commands/import-hours.php');
  require_once(__DIR__ . '/commands/import-bank-statement.php');
  require_once(__DIR__ . '/commands/import-invoice.php');
  require_once(__DIR__ . '/commands/minimal-version.php');
  require_once(__DIR__ . '/commands/worked-hours.php');
  require_once(__DIR__ . '/commands/worked-day.php');
  require_once(__DIR__ . '/commands/worked-week.php');
  require_once(__DIR__ . '/commands/submit-expense.php');
  require_once(__DIR__ . '/commands/who-works-when.php');

function toCamel($str) {
    $parts = explode("-", $str);
    return implode("", array_map(function($x) {
      return ucfirst($x);
    }, $parts));
}

function runCommand($context, $command)
{
    // print("running " . json_encode($command));
    $commands = [
        "register" => 3,
        "hello" => 1,
        "pta-me" => 1,
        "enter" => 7,
        "grant" => 3,
        "list-new" => 1,
        "import-hours" => 4,
        "import-bank-statement" => 4,
        "import-invoice" => 4,
        "minimal-version" => 2,
        "worked-hours" => 5,
        "worked-day" => 4,
        "worked-week" => 4,
        "submit-expense" => 8,
        "who-works-when" => 1
    ];
    if (isset($commands[$command[0]]) && count($command) == $commands[$command[0]]) {
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

function runCommands($context, $commands) {
    foreach ($commands as $command) {
        runCommand($context, $command);
    }
}