<?php declare(strict_types=1);
  require_once(__DIR__ . '/../../platform.php');
  require_once(__DIR__ . '/../../database.php');
  require_once(__DIR__ . '/../../api/scoro.php');

function updateTimesheets($movement_id) {
    $last_movement = getMovementByID($movement_id);
    updateScoro($last_movement);    

}
// TODO update task when already exists
function updateScoro($movement){

    $username = $_SERVER["SCORO_USERNAME"];
    $password = $_SERVER["SCORO_PASSWORD"];
    $company_account_id = $_SERVER["SCORO_COMPANY_ID"];
    $base_url  = 'https://'.$company_account_id.'.scoro.com/api/v2/';

    // Fix: What if more than one ':' are included into the data string(stichting:Peppol for :the Masses)
    $to_component = getComponentName($movement[0]["tocomponent"]);
    $amount =  $movement[0]["amount"];
    $start_datetime = $movement[0]["timestamp_"];
    $datetime_completed = date($start_datetime, strtotime('+'.$amount.'hours'));
    $event_name = $movement[0]['description'];
    $data = explode(":",$to_component,);
    $project_name = $data[1]; 
    $company_name = $data[0];
    
    /* Generate User Token */
    $user_token = getUserToken('eng',$company_account_id,$username,$password,$base_url,'android','my device',123456789987654321);
    if($user_token == null){
      return ["Failed to generate user token"];
    }

    /* Add new Task */
    $response = addTask('eng',$user_token,$base_url,$company_account_id,$event_name,$start_datetime,$project_name,$company_name,$datetime_completed);
    if($response != 200){
      return ["Failed to create new task"];
    }
}