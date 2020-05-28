<?php
/**
 * Created by PhpStorm.
 * User: saner
 * Date: 2019/3/21
 * Time: 5:31 PM
 */

namespace App\Services\WeChat;

use GuzzleHttp\Client;

class Notify
{
    static public function toUser($sendKey, $text, $desp)
    {
        $client = new Client();
        $data = [
            'text' => $text,
            'desp' => $desp
        ];
        $response = $client->get('https://sc.ftqq.com/' . $sendKey . '.send?' . http_build_query($data));
        return json_decode($response->getBody()->getContents(), true);
    }
}
