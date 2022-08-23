<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/../utils.php');

// e.g. generate-implied-purchases NL12INGB000123456 "Acme Mortgage" "House"

function generateImpliedPurchases($context, $command)
{
    if ($context['adminParty']) {
        $fromAccount = $command[1];
        $filterString = $command[2];
        $budgetName = $command[3];
        echo "\nGenerating implied purchases!\n";
        var_dump($command);
        if (strlen($command[2]) == 0) {
            throw new Error("Filter string cannot be zero length!");
        }
        $userComponent = getComponentId($context["user"]["username"]);
        $movements = getAllMovementsFromId(getComponentId($fromAccount));
        for ($i = 0; $i < count($movements); $i++) {
            if (str_contains($movements[$i]["description"], $filterString) || str_contains(getComponentName($movements[$i]["tocomponent"]), $filterString)) {
                echo "Filter string match! " . $movements[$i]["description"]
                . " - " . getComponentName($movements[$i]["tocomponent"])
                . " - " . $filterString . "\n";
                // var_dump($movements[$i]);
                createMovement($context, [
                    "create-movement",
                    $context["user"]["id"],
                    "implied-delivery",
                    $movements[$i]["tocomponent"],
                    getComponentId($budgetName),
                    dateTimeToTimestamp($movements[$i]["timestamp_"]),
                    $movements[$i]["amount"],
                    "implied purchase: " . $movements[$i]["description"]
                ]);
                // TODO: implement depreciation here
                createMovement($context, [
                    "create-movement",
                    $context["user"]["id"],
                    "implied-consumption",
                    getComponentId($budgetName),
                    $userComponent,
                    dateTimeToTimestamp($movements[$i]["timestamp_"]),
                    $movements[$i]["amount"],
                    "implied purchase: " . $movements[$i]["description"]
                ]);
                //  var_dump($movements[$i]);
            }
        }
    } else {
        return ["This command only works in admin party mode"];
    }
}
