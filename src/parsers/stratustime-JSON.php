<?php declare(strict_types=1);

    function parseStratustimeJSON($str) {
        $ret = [];
        $data = json_decode($str);

        for ($i = 0; $i < count($data->Results); $i++) {
            array_push($ret, [
                "worker" =>  $data->Results[$i]->ID,
                "project" => "default-project",
                "start" => strtotime($data->Results[$i]->StartDateTime),
                "seconds" => strtotime($data->Results[$i]->StartDateTime) - strtotime($data->Results[$i]->EndDateTime)
            ]);
        }
        return $ret;
}
