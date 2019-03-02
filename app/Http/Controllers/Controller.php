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
}
