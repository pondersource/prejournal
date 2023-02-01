<?php
use League\Csv\Reader;

// Example of the 2021 format of TRIODOS NL CSV download:
// "03-09-2021","NL36TRIO0320260119","497,79","Credit","TransferWise","TRWIBEB1 BE48967056780227","ET","OPEN SOURCE COLLECTIVE","497,79"
// "01-10-2021","NL36TRIO0320260119","0,15","Debet","","","KN","Kosten van 17-08-2021 tot en met 30-09-2021","497,64"
// "01-10-2021","NL36TRIO0320260119","181,50","Debet","Stichting Blockchain Promotie","BUNQNL2A NL08BUNQ2040937927","PO","Lidmaatschap Michiel, Triantafullenia, Andrej","316,14"

function parseTriodosAmount($str)
{
    // https://stackoverflow.com/questions/4325363/converting-a-number-with-comma-as-decimal-point-to-float
    return floatval(str_replace(',', '.', str_replace('.', '', $str)));
}

function parseTriodosDate($str)
{
    // e.g.  "03-09-2021"
    //        0123456789
    $day = substr($str, 0, 2);
    $month = substr($str, 3, 2);
    $year = substr($str, 6, 4);
    return strtotime(date("Y/m/d 12:00", mktime(0, 0, 0, $month, $day, $year)));
}

function parseTriodosCSV($text, $owner)
{
    $COLUMN_NAMES = [
        "date" ,
        "account",
        "amount",
        "CreditDebet",
        "counterparty",
        "counteraccount",
        "type",
        "description",
        "NewBalance",
    ];
    //load the CSV document from a file path
    $csv = Reader::createFromString($text);
    $csv->setDelimiter(",");
    $records = $csv->getRecords(); //returns all the CSV records as an Iterator object
    $ret = [];
    foreach ($records as $lineNum => $cells) {
        // var_dump($cells[0]);
        // var_dump($cells);
        $obj = [];
        for ($j = 0; $j < count($cells); $j++) {
            $obj[$COLUMN_NAMES[$j]] = $cells[$j];
        }
        // var_dump($obj);
        if ($obj["CreditDebet"] == 'Debet') {
            array_push($ret, [
                "date" => parseTriodosDate($obj["date"]),
                "comment" => $obj["description"],
                "from" => $obj["account"],
                "to" => $obj["counteraccount"] . " " . $obj["counterparty"],
                "amount" => parseTriodosAmount($obj["amount"]),
                "balanceAfter" => parseTriodosAmount($obj["NewBalance"]),
                "insideFrom" => $owner,
                "insideTo" => $obj["account"],
                "lineNum" => $lineNum,
            ]);
        } elseif ($obj["CreditDebet"] == 'Credit') {
            array_push($ret, [
                "date" => parseTriodosDate($obj["date"]),
                "comment" => $obj["description"],
                "from" => $obj["counteraccount"] . " " . $obj["counterparty"],
                "to" => $obj["account"],
                "amount" => parseTriodosAmount($obj["amount"]),
                "balanceAfter" => parseTriodosAmount($obj["NewBalance"]),
                "insideFrom" => $obj["account"],
                "insideTo" => $owner,
                "lineNum" => $lineNum,
            ]);
        } else {
            throw new Error("CreditDebet not parseable! " . $obj["CreditDebet"]);
        }
    }
    // var_dump($ret);
    return $ret;
}
