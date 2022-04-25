<?php declare(strict_types=1);
  require_once(__DIR__ . '/../../platform.php');
  require_once(__DIR__ . '/../../database.php');
  require_once(__DIR__ . '/../../api/scoro.php');

function updateTimesheets($movement_id) {
    $last_movement = getMovementByID($movement_id);
    updateScoro($last_movement);    

}
// creates new task
// TODO update task when already exists
function updateScoro($movement){

    $username = $_SERVER["SCORO_USERNAME"];
    $password = $_SERVER["SCORO_PASSWORD"];
    $company_account_id = $_SERVER["SCORO_company_account_id"];
    $base_url  = 'https://'.$company_account_id.'.scoro.com/api/v2/';
    
    // Fix: What if more than one ':' are included into the data string(stichting:Peppol for :the Masses)
    $data = explode($movement["fromComponen"],":");
    $project_name = $data[1]; 
    $company_name = $data[0];
    $event_name = 'new task';

    $amount =  $movement["amount"];
    $start_datetime = date('Y/m/d H:i:s', $movement["timestamp_"]);
    $endtime = $start_datetime + ($amount*3600);
    $datetime_completed = date('Y/m/d H:i:s', $endtime);

    $user_token = getUserToken('eng',$company_account_id,$username,$password,$base_url,'android','my device',123456789987654321);
    if($user_token == null){
      return ["Failed to generate user token"];
    }
    $response = addTask('eng',$user_token,$base_url,$company_account_id,$event_name,$start_datetime,$project_name,$company_name,$datetime_completed);
    if($response != 200){
      return ["Failed to create new task"];
    }
}