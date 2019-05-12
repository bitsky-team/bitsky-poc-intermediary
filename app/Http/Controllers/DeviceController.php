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

    public function getKey()
    {
        if(!empty($_POST['bitsky_ip']))
        {
            $bitsky_ip = htmlspecialchars($_POST['bitsky_ip']);

            $device = Device::where('bitsky_ip', $bitsky_ip)->first();

            if(!empty($device))
            {
                return $this->success($device->bitsky_key);
            }else
            {
                return $this->error('notFound');
            }
        }else
        {
            return $this->error('forbidden');
        }
    }

    public function getIp()
    {
        if(!empty($_POST['bitsky_key']))
        {
            $bitsky_key = htmlspecialchars($_POST['bitsky_key']);

            $device = Device::where('bitsky_key', $bitsky_key)->first();

            if(!empty($device))
            {
                return $this->success($device->bitsky_ip);
            }else
            {
                return $this->error('notFound');
            }
        }else
        {
            return $this->error('forbidden');
        }
    }
}
