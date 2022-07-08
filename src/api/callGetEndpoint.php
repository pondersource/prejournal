<?php

declare(strict_types=1);

    function callGetEndpoint($headers, $url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        try {
            $result = json_decode($resp);
        } catch (TypeError $e) {
            throw new Error("Request to $url failed");
        } catch (Exception $e) {
            throw new Error("Response JSON could not be decoded");
        }
        curl_close($curl);
        return $result;
    }
