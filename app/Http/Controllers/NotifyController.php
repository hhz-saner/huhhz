<?php

namespace App\Http\Controllers;

use App\Services\WeChat\OfficialHandle;
use Illuminate\Support\Facades\Log;

class NotifyController extends Controller
{
    public function wechatServe()
    {
        $app = app('wechat.official_account');
        $app->server->push(function ($message) {
            if ($message) {
                $method = 'handle' . ucwords($message['MsgType']);
                Log::info($method . ':' . print_r($message, true));
                if (method_exists(OfficialHandle::class, $method)) {
                    return call_user_func_array([OfficialHandle::class, $method], [$message]);
                }
                Log::info('无此处理方法:' . $method);
            }
            return "https://huhhz.me";
        });

        return $app->server->serve();
    }


}
