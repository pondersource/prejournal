<?php declare(strict_types=1);
    require_once(__DIR__ . '/callEndpoint.php');

    function getUserToken($lang,$company_account_id,$username,$password,$base_url,$device_type,$device_name,$device_id){
        $url = $base_url.'userAuth/modify';
        $data = [
            'lang' => $lang,
            'company_account_id' => $company_account_id,
            'username' => $username,
            'password' => $password,
            'device_type' => $device_type,
            'device_name' => $device_name,
            'device_id' => $device_id,
            'request' => []
        ];
        $headers = array(
            'Accept: application/json',
        );
        $response = callEndpoint($headers,json_encode($data),$url);
        $user_token = $response['data']['token'];
        return $user_token;
    }

    function addTask($lang,$user_token,$base_url,$company_account_id,$event_name,$start_datetime,$project_name,$company_name,$datetime_completed){
        $url = $base_url.'tasks/modify/';
        $data = [
            'lang' => $lang,
            'user_token' => $user_token,
            'company_account_id' => $company_account_id,
            'return_data' => 1,
            'request' => [
                'event_name' => $event_name,
                'start_datetime' => $start_datetime,
                'project_name' => $project_name,
                'company_name' => $company_name,
                'datetime_completed' => $datetime_completed
            ]
        ];
        $headers = array(
            'Accept: application/json',
        );
        $response = callEndpoint($headers,json_encode($data),$url);
        return $response['statusCode'];
    }
?>