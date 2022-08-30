<?php

// Example of the 2022 format of ING NL CSV download:
// "Datum";"Naam / Omschrijving";"Rekening";"Tegenrekening";"Code";"Af Bij";"Bedrag (EUR)";"Mutatiesoort";"Mededelingen";"Saldo na mutatie"; 
// "20220713";"Kosten OranjePakket";"NL08INGB0006130373";"";"DV";"Af";"2,35";"Diversen";"1 jun t/m 30 jun 2022 ING BANK N.V. Valutadatum: 13-07-2022";"256,31";""

function checkHeaders($line, $COLUMN_NAMES)
{
    // var_dump($line);
    $cells = explode(";", $line);
    if (count($cells) != count($COLUMN_NAMES)) {
        throw new Error("Found " . count($cells) . " columns in header line instead of " . count($COLUMN_NAMES));
    }
    for ($i = 0; $i < count($COLUMN_NAMES); $i++) {
        if ($cells[$i][0] != '"') {
            throw new Error("Header cell $i does not start with quote! " . $cells[$i]);
        }
        if ($cells[$i][strlen($cells[$i]) - 1] != '"') {
            throw new Error("Header cell $i does not end with quote! " . $cells[$i]);
        }
        $stripped = substr($cells[$i], 1, strlen($cells[$i]) - 2);

        // echo "Checking " . $cells[$i] . "\n";
        if ($stripped != $COLUMN_NAMES[$i]) {
            throw new Error("Column $i is " . $stripped . " instead of " . $COLUMN_NAMES[$i]);
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

function parseIngDescription($obj)
{ // description:
    // "Code" | "Mutatiesoort" | "Naam / Omschrijving" | "Mededelingen" | "Tag"
    
    return $obj["Code"]
        . ": "
        . $obj["Mutatiesoort"]
        . ": "
        . $obj["Naam / Omschrijving"]
        . ": "
        . $obj["Mededelingen"]
        . ": "
        . $obj["Tag"];
}

function parseIngAccount2($obj)
{
    if ($obj["Mutatiesoort"] == 'Diversen') {
        return "ING Bank Services";
    }
    return $obj["Tegenrekening"];
}

function parseIngAmount($str)
{
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
                if ($cells[$j][0] != '"') {
                    throw new Error("Line $i cell $j does not start with quote! " . $cells[$j]);
                }
                if ($cells[$j][strlen($cells[$j]) - 1] != '"') {
                    throw new Error("Line $i cell $j does not end with quote! " . $cells[$j]);
                }
                $obj[$COLUMN_NAMES[$j]] = substr($cells[$j], 1, strlen($cells[$j]) - 2);
            }
            // var_dump($obj);
            if ($obj["Af Bij"] == 'Af') {
                $amount = -parseIngAmount($obj["Bedrag (EUR)"]);
            } elseif ($obj["Af Bij"] == 'Bij') {
                $amount = parseIngAmount($obj["Bedrag (EUR)"]);
            } else {
                throw new Error("Af Bij not parseable! " . $obj["Af Bij"]);
            }
                
            array_push($ret, [
                "otherComponent" => parseAccount2($obj),
                "bankAccountComponent" => $obj["Rekening"],
                "date" => parseIngDate($obj["Datum"]),
                "comment" => parseIngDescription($obj),
                "amount" => $amount, // may be pos or neg!
                "balanceAfter" => parseIngAmount($obj["Saldo na mutatie"]),
                "lineNum" => $i + 1
            ]);
        }
    }
    // var_dump($ret);
    return $ret;
}
