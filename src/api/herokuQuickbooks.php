<?php

require_once(__DIR__ . '/../../vendor/autoload.php');
//require_once(__DIR__. '/../../loadenv.php');
require_once(__DIR__ . '/callEndpoint.php');

//use PonderSource\GoogleApi\Google;
use PonderSource\HerokuApi\HerokuClient;


function last($array) {
    if (!is_array($array)) return $array;
    if (!count($array)) return null;
    end($array);
    return $array[key($array)];
    } 

function createQuickBooksBill() {
        $her = new HerokuClient([
            'apiKey' =>  $_SERVER["HEROKU_API_KEY"],
        ]);
    

        $result = (array)$her->getHerokuTeamInvoices();

        usort($result, function($a, $b) {
            return strtotime($a->period_start) < strtotime($b->period_end) ? -1 : 1;
        });
        
        $json  = json_encode(last($result));
        $array = json_decode($json, true);

    
        $url = $_SERVER["QUICKBOOK_API_URL"] . '/bill';
        $quickAccount = createQuickBooksAccount();
        //var_dump($quickAccount);
    
        $quickVendor = createQuickBooksVendor();
    
        $headers = array(
            "accept:application/json",
            "content-type: application/json",
            "authorization:Bearer " .$_SERVER['QUICKBOOK_API_TOKEN']
         );
        
         $data = [
                "DueDate" => $array["period_end"],
                "TxnDate" => $array["period_start"],
                "Line" => 
                 [ 
                    [
                    "DetailType" => "AccountBasedExpenseLineDetail", 
                    "Amount" => $array["charges_total"], 
                    "AccountBasedExpenseLineDetail" => [
                      "AccountRef" => [
                        "value" => $quickAccount
                       ]
                    ]
                ]
                ], 
                "VendorRef" => [
                  "value" => $quickVendor
                ]
         ];
         $result = json_encode($data);
         //var_dump($result);
         //exit;
        
        $resp = callEndpoint($headers, $result, $url);

        
        if(isset($resp["fault"]["error"][0]["code"])) {
            return $resp["fault"]["error"][0]["detail"];
        } else {
            return $resp;
        }
        
        //return $resp;
}


function createQuickBooksAccount() {
    $url = $_SERVER["QUICKBOOK_API_URL"] . '/account';


    $headers = array(
        "accept:application/json",
        "content-type: application/json",
        "authorization:Bearer " .$_SERVER['QUICKBOOK_API_TOKEN']
     );
    
     $data = [
        "Name" => "PonderSource", 
        "AccountType" => "Expense"
     ];
     $result = json_encode($data);
    
    $resp = callEndpoint($headers, $result, $url);
    foreach($resp as $vendor) {
        //var_dump($vendor["Error"]);
        //var_dump($vendor["Error"][0]);
        //exit;
       
        if(isset($vendor["Error"][0]["code"])) {
            $id = (int) filter_var($vendor["Error"][0]["Detail"], FILTER_SANITIZE_NUMBER_INT);
            return strval($id);
        } else if(isset($vendor["Id"])) {
            return strval($vendor["Id"]);
        }
    }
}

function createQuickBooksVendor() {
    $url = $_SERVER["QUICKBOOK_API_URL"] . '/vendor';


    $headers = array(
        "accept:application/json",
        "content-type: application/json",
        "authorization:Bearer " .$_SERVER['QUICKBOOK_API_TOKEN']
     );
     $data = [ 
            "CompanyName" => "Heroku", 
            "GivenName" => "Heroku", 
     ];
     $result = json_encode($data);
    
    $resp = callEndpoint($headers, $result, $url);
    
    
    foreach($resp as $vendor) {
        //var_dump($vendor["Error"]);
        //var_dump($vendor["Error"][0]);
        //exit;
       
        if(isset($vendor["Error"][0]["code"])) {
            $id = (int) filter_var($vendor["Error"][0]["Detail"], FILTER_SANITIZE_NUMBER_INT);
            return strval($id);
        } else if(isset($vendor["Id"])) {
            return strval($vendor["Id"]);
        }
    }

}





