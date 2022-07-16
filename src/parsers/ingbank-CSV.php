<?php

// Example of the 2022 format of ING NL CSV download:
// "Datum";"Naam / Omschrijving";"Rekening";"Tegenrekening";"Code";"Af Bij";"Bedrag (EUR)";"Mutatiesoort";"Mededelingen";"Saldo na mutatie";"Tag"
// "20220713";"Kosten OranjePakket";"NL08INGB0006130373";"";"DV";"Af";"2,35";"Diversen";"1 jun t/m 30 jun 2022 ING BANK N.V. Valutadatum: 13-07-2022";"256,31";""

function checkHeaders($line, $COLUMN_NAMES)
{
    // var_dump($line);
    $cells = explode(";", $line);
    if (count($cells) != count($COLUMN_NAMES)) {
        throw new Error("Found " . count($cells) . " columns in header line instead of " . count($COLUMN_NAMES));
    }
    for ( $i = 0; $i < count($COLUMN_NAMES); $i++) {
        // echo "Checking " . $cells[$i] . "\n";
        if ($cells[$i] != '"' . $COLUMN_NAMES[$i] . '"') {
            throw new Error ("Column $i is " . $cells[$i] . " instead of " . $COLUMN_NAMES[$i]);
        }
    }
}

function parseIngDate($str)
{
    // e.g. 20220713
    //      01234567
    $year = substr($str, 0, 4);
    $month = substr($str, 4, 2);
    $day = substr($str, 6, 2);
    return strtotime(date("Y/m/d 12:00", mktime(0, 0, 0, $month, $day, $year)));
}

function parseIngDescription($obj) {
    return str_replace('"', "", $obj["Mutatiesoort"])
        . ": "
        . str_replace('"', "", $obj["Naam / Omschrijving"]);
}

function parseIngAccount2($obj) {
    if ($obj["Mutatiesoort"] == '"Diversen"') {
        return "ING Bank Services";
    }
    return str_replace('"', "", $obj["Tegenrekening"]);
}

function parseIngAmount($str) {
    // https://stackoverflow.com/questions/4325363/converting-a-number-with-comma-as-decimal-point-to-float
    return floatval(str_replace(',', '.', str_replace('.', '', $str)));
}

function parseIngBankCSV($text, $owner)
{
    $lines = explode("\r\n", $text);
    $ret = [];
    $COLUMN_NAMES = [
        "Datum",
        "Naam / Omschrijving",
        "Rekening",
        "Tegenrekening",
        "Code",
        "Af Bij",
        "Bedrag (EUR)",
        "Mutatiesoort",
        "Mededelingen",
        "Saldo na mutatie",
        "Tag"
    ];
    checkHeaders($lines[0], $COLUMN_NAMES);
    
    for ($i = 1; $i < count($lines); $i++) {
        if (strlen($lines[$i]) > 0) {
            $cells = explode(";", $lines[$i]);
            if (count($cells) != count($COLUMN_NAMES)) {
                throw new Error("Line $i has " . count($cells) . " columns instead of " . count($COLUMN_NAMES));
            }
            for ($j = 0; $j < count($cells); $j++) {
                $obj[$COLUMN_NAMES[$j]] = trim($cells[$j]);
            }
            if ($obj["Af Bij"] == '"Af"') {
                array_push($ret, [
                    "date" => parseIngDate(str_replace('"', "", $obj["Datum"])),
                    "comment" => parseIngDescription($obj),
                    "from" => $obj["Rekening"],
                    "to" => parseIngAccount2($obj),
                    "amount" => parseIngAmount($obj["Bedrag (EUR)"]),
                    "balanceAfter" => parseIngAmount($obj["Saldo na mutatie"]),
                    "insideFrom" => $owner,
                    "insideTo" => $obj["Rekening"]
                ]);
            } else if ($obj["Af Bij"] == '"Bij"') {
                array_push($ret, [
                    "date" => parseIngDate(str_replace('"', "", $obj["Datum"])),
                    "comment" => parseIngDescription($obj),
                    "from" => parseIngAccount2($obj),
                    "to" => $obj["Rekening"],
                    "amount" => parseIngAmount($obj["Bedrag (EUR)"]),
                    "balanceAfter" => parseIngAmount($obj["Saldo na mutatie"]),
                    "insideFrom" => $obj["Rekening"],
                    "insideTo" => $owner
                ]);
            } else throw new Error("Af Bij not parseable! " . $obj["Af Bij"]);
        }
    }
    // var_dump($ret);
    return $ret;
}
