<?php

class Request {
    public $query;
    public $files;
    public $params;
    public $body;
}

class Response {

    public function error($msg = 'Bad Request', $code = 400, $status=false)
    {
        !$status && header("HTTP/1.1 $code $msg");
        die(json_encode(['Code' => $code, 'Success' => $status, 'Report' => $msg, 'Result' => null]));
    }

    public function json($data = false, $msg = 'Successful Response', $status=true, $code = 200)
    {
        $msg = !$status ? 'Successful Response' : $msg;
        !$status && header("HTTP/1.1 $code $msg");
        die(json_encode(['Code' => $code, 'Success' => $status, 'Report' => $msg, 'Result' => $data]));
    }

    public function print($print)
    {
        die($print);
    }

}
