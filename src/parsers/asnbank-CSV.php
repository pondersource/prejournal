<?php

function parseDate($obj)
{
    $parts = explode("-", $obj["journaaldatum"]);
    $day = $parts[0];
    $month = $parts[1];
    $year = $parts[2];

    return strtotime(date("Y/m/d 12:00", mktime(0, 0, 0, $month, $day, $year)));
}

function normalizeAccountName($str)
{
    return preg_replace('/\s+/', ' ', str_replace("*", " ", trim($str)));
}
function parseAccount2($obj)
{
    if (strlen($obj["tegenrekeningnummer"]) > 0) {
        return $obj["tegenrekeningnummer"] . " " . $obj["naamTegenrekening"];
    }
    if ($obj["globaleTransactiecode"] == "BEA") {
        return normalizeAccountName(substr($obj["omschrijving"], 1, 22));
    }
    if ($obj["globaleTransactiecode"] == "COR" || $obj["globaleTransactiecode"] == "RTI") {
        return normalizeAccountName(substr($obj["omschrijving"], 1, 22));
    }
    if ($obj["globaleTransactiecode"] == "RNT") {
        return "ASN Bank Rente";
    }
    if ($obj["globaleTransactiecode"] == "BTL") {
        $omschrijvingParts = explode(" ", substr($obj["omschrijving"], 1, strlen($obj["omschrijving"]) - 2));
        if (($omschrijvingParts[0] == "EUR") &&
      ($omschrijvingParts[2] == "van") &&
      ($omschrijvingParts[4] == "van")) {
            return $omschrijvingParts[3];
        }
    }
    if ($obj["globaleTransactiecode"] == "GEA") {
        return normalizeAccountName("Geldautomaat " . normalizeAccountName(substr($obj["omschrijving"], 1, 22)));
    }
    if ($obj["globaleTransactiecode"] == "KST" || $obj["globaleTransactiecode"] == "MSC" || $obj["globaleTransactiecode"] == "AFB") {
        return normalizeAccountName("Kosten " . substr($obj["omschrijving"], 1, strlen($obj["omschrijving"]) - 2));
    }
    if ($obj["globaleTransactiecode"] == "DIV" || $obj["globaleTransactiecode"] == "NUL" || $obj["globaleTransactiecode"] == "BIJ") {
        return normalizeAccountName("Diversen " . substr($obj["omschrijving"], 1, strlen($obj["omschrijving"]) - 2));
    }
    echo "Cannot parse account2!";
    var_dump($obj);
    exit();
    return "UNKNOWN " . $obj["globaleTransactiecode"];
}

function parseDescription($obj)
{
    return str_replace("*", " ", 
        $obj["interneTransactiecode"] . " " .
        $obj["globaleTransactiecode"] . " " .
        $obj["betalingskenmerk"] . "  " .
        $obj["omschrijving"]);
}

function parseAsnBankCSV($text)
{
    // This list uses the exact names as documented in Dutch
    // at https://www.asnbank.nl/web/file?uuid=fc28db9c-d91e-4a2c-bd3a-30cffb057e8b&owner=6916ad14-918d-4ea8-80ac-f71f0ff1928e&contentid=852
    $ASN_BANK_CSV_COLUMNS = [
    'boekingsdatum', // dd-mm-jjjj Dit veld geeft de datum weer waarop de transactie daadwerkelijk heeft plaatsgevonden. Voorbeeld: 3-­4-­2000
    'opdrachtgeversrekening', // X (18) Uw ASN­Rekening (IBAN). Voorbeeld: NL01ASNB0123456789
    'tegenrekeningnummer', // X (34) Dit veld bevat het rekeningnummer (IBAN) naar of waarvan de transactie afkomstig is. Het IBAN telt maximaal 34 alfanumerieke tekens en heeft een vaste lengte per land. Het IBAN bestaat uit een landcode (twee letters), een controlegetal (twee cijfers) en een (voor bepaalde landen aangevuld) nationaal rekeningnummer. Voorbeeld: NL01BANK0123456789
    'naamTegenrekening', // X (70) Hier wordt de naam van de tegenrekening vermeld. De naam is maximaal 70 posities lang en wordt in kleine letters weergegeven. Voorbeeld: jansen
    'adres', // niet gebruikt
    'postcode', // niet gebruikt
    'plaats', // niet gebruikt
    'valutasoortRekening', // XXX Dit veld geeft de ISO valutasoort van de rekening weer. Een bestand kan verschillende valutasoorten bevatten. Voorbeeld: EUR
    'saldoRekeningVoorMutatie', // ­999999999.99 Geeft het saldo weer van de rekening voordat de mutatie is verwerkt. Als decimaal scheidingsteken wordt een punt gebruikt. Er wordt geen duizend separator gebruikt. In het geval van een negatieve waarde wordt het bedrag voorafgegaan van een – (min) teken. Voorbeeld: 122800.83 of ­123.30
    'valutasoortMutatie', // XXX Dit veld geeft de ISO valutasoort van de mutatie weer. Een bestand kan verschillende valutasoorten bevatten. Voorbeeld: EUR
    'transactiebedrag', // ­999999999.99 Geeft het transactiebedrag weer. Als decimaal scheidingsteken wordt een punt gebruikt. Een negatief bedrag wordt voorafgegaan door een – (min) teken. Voorbeeld: 238.45 of ­43.90
    'journaaldatum', // dd­-mm-­jjjj De journaaldatum is de datum waarop een transactie in de systemen van ASN Bank wordt geboekt. Dit hoeft niet noodzakelijkerwijs gelijk te zijn aan de boekingsdatum. Voorbeeld: 21­-01-­2000
    'valutadatum', // dd­-mm-­jjjj Dit veld geeft de valutadatum weer. De valutadatum is de datum waarop een bedrag rentedragend wordt. Voorbeeld: 01­-04-­2001
    'interneTransactiecode', // 9999 Dit is een interne transactiecode zoals die door de ASN Bank wordt gebruikt. Deze transactiecodes kunnen gebruikt worden om heel verfijnd betaalde transacties te herkennen. Zoals een bijboeking van een geldautomaat opname. Er kan geen garantie worden gegeven dat deze codes in de toekomst hetzelfde blijven en/of dat er codes vervallen en/of toegevoegd zullen worden. Voorbeeld: 8810 of 9820
    'globaleTransactiecode', // XXX De globale transactiecode is een vertaling van de interne transactiecode. Gebruikte afkortingen zijn bijvoorbeeld BEA voor een betaalautomaat opname of GEA voor een geldautomaat opname. In de bijlage wordt een overzicht gegeven van alle gebruikte afkortingen. Voorbeeld: GEA of BEA of VV. Zie ook Bijlage 1: Gebruikte boekingscodes
    'volgnummerTransactie', // N (8) Geeft het transactievolgnummer van de transactie weer. Dit volgnummer vormt samen met de journaaldatum een uniek transactie id. Voorbeeld: 90043054
    'betalingskenmerk', // X (16) Het betalingskenmerk bevat de meest relevante gegevens zoals die door de betaler zijn opgegeven. Zoals debiteuren nummer en/of factuurnummer. Het betalingskenmerk wordt tussen enkele quotes (’) geplaatst. Voorbeeld: ’factuur 9234820’
    'omschrijving', // X (140) De omschrijving zoals die bij de overboeking is opgegeven. De omschrijving kan maximaal 140 posities beslaan. Voorbeeld ’02438000140032extra trekking werelddierendag 4info’
    'afschriftnummer', // N (3) Het nummer van het afschrift waar de betreffende boeking op staat vermeld. Voorbeeld: 42
  ];
    $lines = explode("\n", $text);
    $ret = [];
    for ($i = 0; $i < count($lines); $i++) {
        if (strlen($lines[$i]) > 0) {
            $cells = explode(",", $lines[$i]);
            $obj = [];
            for ($j = 0; $j < count($cells); $j++) {
                $obj[$ASN_BANK_CSV_COLUMNS[$j]] = trim($cells[$j]);
                // if ($first) {
        //   printOpeningBalance([
        //     "date" => parseDate($obj),
        //     "account1" => $obj["opdrachtgeversrekening"],
        //     "balance" => floatval($obj["saldoRekeningVoorMutatie"])
        //   ]);
        //   $first = false;
        // }
            }

            array_push($ret, [
                "otherComponent" => parseAccount2($obj),
                "bankAccountComponent" => $obj["opdrachtgeversrekening"],
                "date" => parseDate($obj),
                "comment" => parseDescription($obj),
                "amount" => floatval($obj["transactiebedrag"]), // may be pos or neg!
                "unit" => "EUR",
                "balanceAfter" => floatval($obj["saldoRekeningVoorMutatie"]) + floatval($obj["transactiebedrag"]),
                "lineNum" => $i + 1,
                "remoteId" => $obj["journaaldatum"] . " " . $obj["volgnummerTransactie"]
            ]);
        }
    }
    // var_dump($ret);
    return $ret;
}
