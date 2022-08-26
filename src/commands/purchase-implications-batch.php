<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/../utils.php');

// e.g. generate-implied-purchases NL12INGB000123456 "Acme Mortgage" "House"

function PurchaseImplicationsBatch($context, $command)
{
    if ($context['adminParty']) {
        $filename = $command[1];
        $batchLines = explode("\n", file_get_contents($filename));
        echo "Read " . count($batchLines) . " lines\n";
        $implicationsMap = [];
        for ($i = 0; $i < count($batchLines); $i++) {
            $words = reconcileQuotes(explode(" ", trim($batchLines[$i])));
            if ($words[0] == "generate-implied-purchases") {
                $fromAccount = $words[1];
                $searchString = $words[2];
                $impliedBudget = $words[3];
                echo "$fromAccount $searchString $impliedBudget\n";
                if (!isset($implicationsMap[$fromAccount])) {
                    $implicationsMap[$fromAccount] = [];
                }
                $implicationsMap[$fromAccount][$searchString] = $impliedBudget;
            }
        }
        var_dump($implicationsMap);
        $userComponent = getComponentId($context["user"]["username"]);
        foreach ($implicationsMap as $fromAccount => $submap) {
            $movements = getAllMovementsFromId(getComponentId($fromAccount));
            for ($i = 0; $i < count($movements); $i++) {
                $budget = "Other";
                foreach ($submap as $searchString => $impliedBudget) {
                    if (str_contains(getComponentName($movements[$i]["tocomponent"]), $searchString)) {
                        $budget = $impliedBudget;
                        break;
                    }
                }
                echo "Movement " . $movements[$i]["id"] . " budget " . $budget;
                createMovement($context, [
                    "create-movement",
                    $context["user"]["id"],
                    "implied-delivery",
                    $movements[$i]["tocomponent"],
                    getComponentId($budget),
                    dateTimeToTimestamp($movements[$i]["timestamp_"]),
                    $movements[$i]["amount"]
                ]);
                createMovement($context, [
                    "create-movement",
                    $context["user"]["id"],
                    "implied-consumption",
                    getComponentId($budget),
                    $userComponent,
                    dateTimeToTimestamp($movements[$i]["timestamp_"]),
                    $movements[$i]["amount"]
                ]);
            }
        }
    } else {
        return ["This command only works in admin party mode"];
    }
}
