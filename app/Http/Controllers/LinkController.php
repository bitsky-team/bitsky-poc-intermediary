<?php

namespace App\Http\Controllers;

use App\Http\Models\Link;

class LinkController extends Controller
{
    private $deviceController = null;

    public function __construct()
    {
        parent::__construct();
        $this->deviceController = new DeviceController();
    }

    public function create()
    {
        if(!empty($_POST['senderKey']) && !empty($_POST['receiverKey'])) {
            $senderDevice = json_decode($this->deviceController->check(htmlspecialchars($_POST['senderKey'])), true);
            $receiverDevice = json_decode($this->deviceController->check(htmlspecialchars($_POST['receiverKey'])), true);

            if($senderDevice['success'] && $receiverDevice['success'])
            {
                $link = Link::where(function ($query) use ($senderDevice) {
                    $query->where('first_key', $senderDevice['data']['bitsky_key'])
                          ->orWhere('second_key', $senderDevice['data']['bitsky_key']);
                })->where(function ($query) use ($receiverDevice) {
                    $query->where('first_key', $receiverDevice['data']['bitsky_key'])
                          ->orWhere('second_key', $receiverDevice['data']['bitsky_key']);
                })->first();

                if(!empty($link))
                {
                    if($link->first_agreement == 1 && $link->second_agreement == 1)
                    {
                        return $this->success($link);
                    } else
                    {
                        if($link->second_key == $senderDevice['data']['bitsky_key'])
                        {
                            $secondKeyDevice = json_decode($this->deviceController->check($link->second_key), true);

                            if($secondKeyDevice['data']['bitsky_ip'] == $this->getClientIP())
                            {
                                $link->second_agreement = 1;
                                $link->save();
                                return $this->success($link);
                            } else
                            {
                                return $this->error('forbidden');
                            }
                        } else
                        {
                            return $this->success($link);
                        }
                    }
                } else
                {
                    $link = Link::create([
                        'first_key' => $senderDevice['data']['bitsky_key'],
                        'second_key' => $receiverDevice['data']['bitsky_key'],
                        'first_agreement' => 1,
                        'second_agreement' => 0,
                    ]);

                    return $this->success($link);
                }
            } else
            {
                return $this->error('incorrectKey');
            }

        } else {
            return $this->error('noKeys');
        }
    }

    public function check()
    {
        if(!empty($_POST['senderKey']) && !empty($_POST['receiverKey'])) {
            $senderDevice = json_decode($this->deviceController->check(htmlspecialchars($_POST['senderKey'])), true);
            $receiverDevice = json_decode($this->deviceController->check(htmlspecialchars($_POST['receiverKey'])), true);

            if($senderDevice['success'] && $receiverDevice['success'])
            {
                $link = Link::where(function ($query) use ($senderDevice) {
                    $query->where('first_key', $senderDevice['data']['bitsky_key'])
                        ->orWhere('second_key', $senderDevice['data']['bitsky_key']);
                })->where(function ($query) use ($receiverDevice) {
                    $query->where('first_key', $receiverDevice['data']['bitsky_key'])
                        ->orWhere('second_key', $receiverDevice['data']['bitsky_key']);
                })->first();

                if(!empty($link))
                {
                   if($link->first_agreement == 1 && $link->second_agreement == 1)
                   {
                       return $this->success($link);
                   } else
                   {
                       return $this->error('pending');
                   }
                } else
                {
                    return $this->error('notFound');
                }
            } else
            {
                return $this->error('unrecognizedDevice');
            }
        } else
        {
            return $this->error('noKeys');
        }
    }

    public function activeLink()
    {

    }
}
