<?php
/**
 * Created by PhpStorm.
 * User: saner
 * Date: 2019/4/16
 * Time: 10:37 AM
 */

namespace App\Services\WeChat;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OfficialHandle
{
    public static function handleEvent($message)
    {
        if ($message['Event'] == 'SCAN') {
            $data = explode(':', $message['EventKey']);
            if ($data[0] == 'wechatScanLogin') {
                $user = DB::table('admin_users')->where('open_id', $message['FromUserName'])->first();
                if ($user) {
                    Cache::put($message['EventKey'], $user);
                    return '登录成功！';
                } else {
                    return '请登录 https://huhhz.me/admin/auth/login 来绑定账号。';
                }
            }
        }
        return "https://huhhz.me";
    }

    public static function handleText($message)
    {

    }
}
