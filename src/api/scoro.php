<?php declare(strict_types=1);
    require_once(__DIR__ . '/callEndpoint.php');

    function getUserToken($request){
        $url = $request['base_url'].'userAuth/modify';
        $data = [
            'lang' => $request['lang'],
            'company_account_id' => $request['company_account_id'],
            'username' => $request['username'],
            'password' => $request['password'],
            'device_type' => $request['device_type'],
            'device_name' => $request['device_name'],
            'device_id' => $request['device_id'],
            'request' => []
        ];
        $headers = array(
            'Accept: application/json',
        );
        $response = callEndpoint($headers,json_encode($data),$url);
        $user_token = $response['data']['token'];
        return $user_token;
    }

    function createTask(array $request){
        
        $url = $request['base_url'].'tasks/modify/';
        $data = [
            'lang' => $request['lang'],
            'user_token' => $request['user_token'],
            'company_account_id' => $request['company_account_id'],
            'return_data' => 1,
            'request' => [
                'event_name' => $request['event_name'],
                'start_datetime' => $request['start_datetime'],
                'project_name' => $request['project_name'],
                'company_name' =>  $request['company_name'],
                'billable_hours' => $request['billable_hours'],
                'created_date' => $request['created_date'],
                'modified_date' => $request['modified_date'],
                'datetime_completed' => null,
                'is_completed' => null,
                'assigned_to' => null,
                'related_users' => null,
                'related_users_email' => null,
                'status' => null,
                'status_name' => null,
                'time_entries' => null,
                'sortorder' => null,
                'ete_id' => null,
                'activity_id' => null,
                'activity_type' => null,
                'event_id' => null,
                'description' => null,
                'is_personal' => null,
                'project_id' => null,
                'project_phase_id' => null,
                'company_id' => null,
                'person_id' => null,
                'person_name' => null,
                'invoice_id' => null,
                'purchase_order_id' => null,
                'order_id' => null,
                'quote_id' => null,
                'rent_order_id' => null,
                'bill_id' => null,
                'duration_planned' => null,
                'owner_id' => null,
                'owner_email' => null,
                'permissions' => null,
                'id_deleted' => null,
                'deleted_date' => null,
            ]
        ];
        $headers = array(
            'Accept: application/json',
        );
        $response = callEndpoint($headers,json_encode($data),$url);
        echo 'Status: '.strval($response['statusCode']).PHP_EOL;
        return $response['statusCode'];
    }
?>