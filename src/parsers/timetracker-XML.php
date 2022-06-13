<?php

declare(strict_types=1);

function parseTimeTrackerXML($str)
{
    $ret = [];
    //$lines = explode("\n",$str);
    $xml = simplexml_load_string($str, 'SimpleXMLElement', LIBXML_NOCDATA);
    $xmlJson = json_encode($xml);
    $xmlArr = json_decode($xmlJson); // Returns associative array
    //var_dump($xmlArr);

    array_push($ret, [
            "worker" => $xmlArr->row->user,
            "project" => "test",
            "start" => strtotime($xmlArr->row->date),
            "seconds" => $xmlArr->row->finish,
        ]);
    return $ret;
}
