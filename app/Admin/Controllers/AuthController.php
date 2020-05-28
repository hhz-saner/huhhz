<?php

namespace App\Admin\Controllers;

use App\Helper\Helper;
use Encore\Admin\Admin;
use Encore\Admin\Controllers\AuthController as Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controllers
{
    /**
     * Show the login page.
     *
     * @return \Illuminate\Contracts\View\Factory|Redirect|\Illuminate\View\View
     */
    public function getLogin()
    {
        if ($this->guard()->check()) {
            return redirect($this->redirectPath());
        }

        if (request()->has('token')) {
            return $this->tokenLogin();
        }

        if (Helper::isWechat()) {
            return $this->wechatLogin();
        }

        return $this->view();
    }

    public function postLogin(Request $request)
    {
        $this->loginValidator($request->all())->validate();

        $credentials = $request->only([$this->username(), 'password']);
        $remember = $request->get('remember', false);

        if ($this->guard()->attempt($credentials, $remember)) {
            $user = $this->guard()->user();
            if(!$user->open_id){
                $user->open_id = $request->get('openId');
                $user->save();
            };
            return $this->sendLoginResponse($request);
        }

        return back()->withInput()->withErrors([
            $this->username() => $this->getFailedLoginMessage(),
        ]);
    }

    public function getLoginToken(Request $request)
    {
        $token = $request->get('token');
        if (Cache::get('wechatScanLogin:' . $token)) {
            return [
                'status' => 'success',
                'code' => 200
            ];
        }
        return [
            'status' => 'error',
            'code' => 404
        ];
    }

    protected function tokenLogin()
    {
        $token = request()->get('token');
        $user = Cache::get('wechatScanLogin:' . $token);
        if (!empty($user)) {
            $this->guard()->loginUsingId($user->id);
            Cache::delete('wechatScanLogin:' . $token);
            return redirect($this->redirectPath());
        }
    }

    protected function wechatLogin()
    {

        $user = session('wechat.oauth_user.default');
        $user = DB::table('admin_users')->where('open_id', $user['id'])->first();
        if ($user) {
            $this->guard()->loginUsingId($user->id);
            return redirect($this->redirectPath());
        }
        $openId = session('wechat.oauth_user.default')['id'];
        return view('admin.login', compact('openId'));
    }

    protected function view()
    {
        $app = app('wechat.official_account');
        $token = Str::random(32);
        Cache::put('wechatScanLogin:' . $token, 0, 5 * 60);
        $result = $app->qrcode->temporary('wechatScanLogin:' . $token, 5 * 60);
        $url = $app->qrcode->url($result['ticket']);


        return view('admin.login', compact('token', 'url'));
    }

}
