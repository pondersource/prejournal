<?php

require_once  '../../vendor/autoload.php';
require_once('../platform.php');

//use PonderSource\GoogleApi\Google;
use PonderSource\HerokuApi\HerokuClient;


//HEROKU
$her = new HerokuClient([
    'apiKey' =>  $_SERVER["HEROKU_API_KEY"],
]);
 
$her->getHerokuTeamInvoices();

?>
