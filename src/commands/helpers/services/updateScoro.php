<?php declare(strict_types=1);
  require_once(__DIR__ . '/../../../platform.php');
  require_once(__DIR__ . '/../../../database.php');
  require_once(__DIR__ . '/../../../api/scoro.php');

// creates new task
// TODO update task when already exists
function updateScoro($movement_id,$event_id){

    $movement = getMovement($movement_id);
    $username = $_SERVER["SCORO_USERNAME"];
    $password = $_SERVER["SCORO_PASSWORD"];
    $company_account_id = $_SERVER["SCORO_COMPANY_ID"];
    $base_url  = 'https://'.$company_account_id.'.scoro.com/api/v2/';
    // Fix: What if more than one ':' are included into the data string(stichting:Peppol for :the Masses)
    $to_component = getComponentName($movement["tocomponent"]);
    $event_name = $movement["description"];
    $data = explode(":",$to_component,);
    $project_name = $data[1]; 
    $company_name = $data;
    $amount =  $movement["amount"];
    $start_datetime = $movement["timestamp_"];
    $created_date = date('Y-m-d H:i:s');
    $modified_date = date('Y-m-d H:i:s');
    /* Time (HH:ii:ss) */	
    $billable_hours = gmdate('H:i:s', ($amount * 3600));

    $token_request = [
      'base_url' => $base_url,
      'lang' => 'eng',
      'company_account_id' => $company_account_id,
      'username' => $username,
      'password' => $password,
      'device_type' => 'android',
      'device_name' => 'my device',
      'device_id' => 123456789987654321,
      'request' => []
    ];
    $user_token = getUserToken($token_request);
    if($user_token == null){
      return ["Failed to generate user token"];
    }
    $request =[
      'base_url' => $base_url,
      'lang' => 'eng',
      'user_token' => $user_token,
      'company_account_id' => $company_account_id,
      'event_name' => $event_name,
      'start_datetime' => $start_datetime,
      'project_name' => $project_name,
      'company_name' => $company_name,
      'billable_hours' => $billable_hours,
      'created_date' => $created_date,
      'modified_date' => $modified_date,
      'event_id' => $event_id
    ];
    return createTask($request);
}