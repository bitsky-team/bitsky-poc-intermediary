<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\RemoteAddress;

class Controller extends BaseController
{
    protected $remoteAddress = null;

    public function __construct()
    {
        $this->remoteAddress = new RemoteAddress();
    }

    protected function success($data)
    {
        return json_encode(['success' => true, 'data' => $data]);
    }

    protected function error($message)
    {
        return json_encode(['success' => false, 'message' => $message]);
    }

    protected function getClientIP()
    {
        return $this->remoteAddress->getIpAddress();
    }

    protected function CallAPI($method, $url, $data = false)
    {
        $curl = curl_init();

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }
}