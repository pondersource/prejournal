<?php

declare(strict_types=1);

function callEndpoint($headers, $data, $url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //echo "Calling endpoint";
    //var_dump($headers);
    //var_dump($data);
    //var_dump($url);
    $response = curl_exec($ch);
    curl_close($ch);
    //var_dump($response);
    return json_decode($response, true);
}
