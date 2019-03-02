<?php

namespace App\Http\Controllers;

use App\Http\Models\Device;

class DeviceController extends Controller
{
    public function init()
    {
        if(!empty($_POST['key']))
        {
            $key = htmlspecialchars($_POST['key']);

            $checkDevice = Device::where('bitsky_key', $key)->first();

            if(empty($checkDevice))
            {
                $device = Device::create([
                    'bitsky_ip' => $this->getClientIP(),
                    'bitsky_key' => $key
                ]);

                return $this->success($device);
            } else
            {
                return $this->error('alreadyExists');
            }
        } else
        {
            return $this->error('noKey');
        }
    }

    public function check($key)
    {
        $device = Device::where('bitsky_key', $key)->first();

        if($device)
        {
            return $this->success($device);
        } else
        {
            $this->error('notFound');
        }
    }

    public function checkOrCreate($key)
    {
        $check = json_decode($this->check($key), true);

        if($check['success'])
        {
            return $this->success($check['data']);
        } else if(!$check['success'] && $check['message'] == 'notFound')
        {
            $device = Device::create([
                'bitsky_ip' => $this->getClientIP(),
                'bitsky_key' => $key
            ]);

            return $this->success($device);
        }

        return $this->error('forbidden');
    }
}
