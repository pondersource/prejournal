<?php

function parseYearAndDate($year, $dateStr) {
    if (($year < 2000) || ($year > 3000)) {
        throw new Exception("Unexpected year $year");
    }
    $parts = explode(" ", $dateStr);
    if (count($parts) != 2) {
        throw new Exception("Unexpected date '$dateStr'");
    }
    $day = intval($parts[0]);
    if (($day < 1) || ($day > 31)) {
        throw new Exception("Unexpected day of month " . $parts[0]);
    }
    $month = array_search($parts[1], [
      1 => "januari",
      2 => "februari",
      3 => "maart",
      4 => "april",
      5 => "mei",
      6 => "juni",
      7 => "juli",
      8 => "augustus",
      9 => "september",
      10 => "oktober",
      11 => "november",
      12 => "december"
    ]);
    if ($month === false) {
        throw new Exception("Unexpected month " . $parts[1]);
    }
    return strtotime(date("Y/m/d 12:00", mktime(0, 0, 0, $month, $day, $year)));
}

function parseAmount($str) {
    $parts = explode(",", $str);
    if (count($parts) != 2) {
        throw new Exception("No comma in amount '$str'");
    }

    if ((ord($parts[0][0]) == 226) && (ord($parts[0][1]) == 136) && (ord($parts[0][2]) == 146)) {
        $eurosStr = substr($parts[0], 3);
        return - intval($eurosStr) - intval($parts[1]) / 100;
    } else {
        return intval($parts[0]) + intval($parts[1]) / 100;
    }
}

function parseIngCcScrape($text, $owner)
{
    $ret = [];
    $date = "";

    $paragraphs = explode("\n\n", $text);
    $parts = explode("\n", $paragraphs[0]);
    if (count($parts) != 4) {
        throw new Exception("Was expecting 4 lines in the first paragraph");
    }
    if ($parts[0] != "Af- en bijschrijvingen huidige periode") {
        throw new Exception("Was expecting first line to be 'Af- en bijschrijvingen huidige periode'");
    }
    $year = intval($parts[1]);
    $dateStr = $parts[2];
    $date = parseYearAndDate($year, $dateStr);
    $currency = $parts[3];
    if ($currency != "EUR") {
        throw new Exception("Currency is not euros");
    }

    for ($i = 1; $i < count($paragraphs); $i++) {
        $parts = explode("\n", $paragraphs[$i]);
        if (count($parts) < 2) {
            throw new Exception("Unexpected paragraph with less than two lines " . $paragraphs[$i]);
        }
        // var_dump($date . "   |   " . $parts[0] . "    |   " . parseAmount($parts[1]) . " $currency");
        array_push($ret, [
            "date" => $date,
            "comment" => "",
            "from" => "ING CC",
            "to" => $parts[0],
            "amount" => parseAmount($parts[1]),
            "insideFrom" => $owner,
            "insideTo" => "ING CC",
            "lineNum" => $i + 1
        ]);
        if (count($parts) == 2) {
            // nothing else to do
        } else if (count($parts) == 3) {
            if ($parts[2] == 'Er is geen voorgaande periode.') {
                $date = '00-00-0000';
            } else {
                $dateStr = $parts[2];
                $date = parseYearAndDate($year, $dateStr);
            }
        } else if (count($parts) == 4) {
            $year = $parts[2];
            $dateStr = $parts[3];
            $date = parseYearAndDate($year, $dateStr);
        } else if (count($parts) == 14 ) {
            // TODO: check correctness of monthly summary
            $year = $parts[11];
            $dateStr = $parts[12];
            $date = parseYearAndDate($year, $dateStr);
        } else {
            throw new Exception("Unexpected paragraph with " . count($parts) . " lines " . $paragraphs[$i]);
        }
    }

   return $ret;
}
