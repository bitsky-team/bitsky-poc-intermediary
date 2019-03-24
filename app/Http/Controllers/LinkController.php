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
        if(!empty($_POST['alreadyActivated']) && !empty($_POST['toActivate']))
        {
            $alreadyActivated = htmlspecialchars($_POST['alreadyActivated']);
            $toActivate = htmlspecialchars($_POST['toActivate']);

            $requestingDevice = json_decode($this->deviceController->check($alreadyActivated), true);
            $toActivate = json_decode($this->deviceController->check($toActivate), true);

            if($requestingDevice['success'] && $toActivate['success'])
            {
                if($requestingDevice['data']['bitsky_ip'] == $this->getClientIP())
                {
                    $response = $this->callAPI(
                        'POST',
                        'http://'.$toActivate['data']['bitsky_ip'].'/active_link',
                        [
                            'bitsky_key' => $requestingDevice['data']['bitsky_key']
                        ]
                    );

                    return $this->success(json_decode($response));
                } else
                {
                    return $this->error('forbidden');
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

    public function getActiveLinks()
    {
        if(!empty($_POST['bitsky_key']))
        {
            $key = htmlspecialchars($_POST['bitsky_key']);
            $device = json_decode($this->deviceController->check($key), true);

            if($device['success'])
            {

                if($device['data']['bitsky_ip'] == $this->getClientIP())
                {
                    $links = Link::where(function ($query) use ($device) {
                        $query->where('first_key', $device['data']['bitsky_key'])
                            ->orWhere('second_key', $device['data']['bitsky_key']);
                    })->get();

                    foreach($links as $link)
                    {
                        $foreignKey = $link->first_key !== $key ? $link->first_key : $link->second_key;
                        $foreignDevice = json_decode($this->deviceController->check($foreignKey), true);

                        if($foreignDevice['success'])
                        {
                            $link['foreign_ip'] = $foreignDevice['data']['bitsky_ip'];
                        }
                    }

                    return $this->success($links);
                } else
                {
                    return $this->error('forbidden');
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

    public function delete()
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
                   $link->delete();

                    $this->callAPI(
                        'POST',
                        'http://'.$receiverDevice['data']['bitsky_ip'].'/delete_link_intermediary',
                        [
                            'bitsky_key' => $senderDevice['data']['bitsky_key']
                        ]
                    );

                   return $this->success(null);
                } else
                {
                    return $this->error('notFound');
                }
            } else
            {
                return $this->error([$senderDevice, $receiverDevice]);
            }
        } else
        {
            return $this->error('noKeys');
        }
    }
}
