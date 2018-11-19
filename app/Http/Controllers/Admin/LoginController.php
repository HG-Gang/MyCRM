<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Captcha;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
use App\Model\Admin;
use App\Model\Role;
class LoginController extends Abstract_Mt4service_Controller {

    /**
     * Display a listing of the resource.
     * 登录页面
     * @return \Illuminate\Http\Response
     */
    public function index() {
  
      
        return view('admin.login.login');
    }

    /**
     * Display a listing of the resource.
     * 验证码方法
     * @return \Illuminate\Http\Response
     */
    public function captcha() {
     //   ob_clean();
        return Captcha::create('custom_captcha');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     * 判断登录
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logon(Request $request) {
        $loginUid = $request->loginUid;
        $loginPsw = $request->loginPassword;
        $cptcode = $request->cptcode;
        $_verifyType = $request->session()->get('verifyType');
        //验证码检验
        if (!Captcha::check($cptcode)) {
            return json_encode(['msg' => '验证码不正确', 'state' => 0]);
        };
        $admin = Admin::where('username', $loginUid)->first();
        //判断账号
        if (empty($admin)) {
            return json_encode(['msg' => '登录账号错误', 'state' => 0]);
        }
        //判断密码
        if (!password_verify($loginPsw, $admin->password)) {
            return json_encode(['msg' => '密码错误', 'state' => 0]);
        }
        //判断改账户是否有效
        if ($admin->state == 0) {
            return json_encode(['msg' => '账户已经被停用', 'state' => 0]);
        }
        $ip = $_SERVER['REMOTE_ADDR']; //客户端IP
        $login_time = time();
        $login_mnu = $admin->login_mnu + 1;
        session(['ip' => $admin->ip, 'login_time' => $admin->login_time, 'login_mnu' => $admin->login_mnu,'id'=>$admin->id]);
        $request->session()->put('auser', $admin);
        $admin->login_time = $login_time;
        $admin->ip = $ip;
        $admin->login_mnu = $login_mnu;
        $admin->save();
          //用户权限
        $permissions = Role::where(['role_id' => $admin->role_id])->first();
        session(['permissions' => json_decode($permissions->acl)]);
        return json_encode(['msg' => '登录成功', 'state' =>1]);
        
    }

    /**
     * Display the specified resource.
     *退出登录
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request) {
        
         $request->session()->forget('id');
        
        
        return redirect(route_prefix() . '/login');
        
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

}
