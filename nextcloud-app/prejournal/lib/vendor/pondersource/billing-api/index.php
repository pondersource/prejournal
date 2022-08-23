<?php

require_once __DIR__ . '/vendor/autoload.php';

use PonderSource\GoogleApi\Google;
use PonderSource\HerokuApi\HerokuClient;
use PonderSource\GitHubApi\GitHubClient;
use PonderSource\AWSApi\AWSClient;
use PonderSource\Library\DotEnv;

(new DotEnv(__DIR__ . '/.env'))->load();

// $uri = $_SERVER['REQUEST_URI'];

//GOOGLE
$google = new Google([
    'apiKey' => putenv('GOOGLE_APPLICATION_CREDENTIALS='.realpath("application_default_credentials.json"))
]);
$google->getCloudbillingSkus();

// GITHUB
$token = getenv('GITHUB_ACCESS_TOKEN');
$github = new GitHubClient($token);
$user_billing = $github->getUserSharedStorageBilling("ishifoev");
//$org_billing = $github->getOrgSharedStorageBilling("testORGbilling");

//HEROKU
$her = new HerokuClient([
    'apiKey' =>  getenv('HEROKU_API_KEY'),
]);
 
//var_dump($her->getUrlAccount($uri));
$her->getHerokuInvoice();
$her->getHerokuTeamInvoices();
//
// // AWS
// $key = getenv('AWS_ACCESS_KEY_ID');
// $secret = getenv('AWS_SECRET_ACCESS_KEY');
//
// $aws = new AWSClient([
//     'region'  => 'us-east-1',
//     'version' => 'latest',
//     'credentials' => [
//       'key' => $key,
//       'secret' => $secret
//     ],
//     'endpoint' => 'https://ce.us-east-1.amazonaws.com'
// ]);
// $aws->getCostAndUsage([
// 'Granularity' => 'DAILY', // REQUIRED
// 'Metrics' => ['BlendedCost'], // REQUIRED
// 'TimePeriod' => [ // REQUIRED
// 		'Start' => '2022-01-03', // REQUIRED
//     'End' => '2022-02-03', // REQUIRED
// 	],
// ],'aws_cost_and_usage');

?>
