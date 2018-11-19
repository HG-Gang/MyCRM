<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\Admin;
class PermissionsMiddleware
{
     /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $action = request()->route()->getAction();
        $action = explode('\\', $action['controller']);
       
        //无需权限可直接进入的控制器和方法
        $except_action = [
            'AdminController@index',
            'AdminController@create',
            'LoginController@logout',
            'AdminController@UserInfo',
        ];
       $id = session('id');
        if(empty($id)){
             return redirect()->guest(route_prefix() . '/login');
            
        }
        //当前方法
        $this_action = array_pop($action);
        //用户的权限
        $permissions = session('permissions');
        if (empty($permissions)) {
            $permissions = $except_action;
            session(['permissions'=>$permissions]);
        }
        $admin = Admin::find($id);
        if (RoleName($admin->role_id) == "超级管理员") {
            //超级管理员
        } else {
            //没有权限的时候
            if (!in_array($this_action, $permissions)) {
                if (!in_array($this_action, $except_action)) {
                    if ($request->ajax()) {
                        return ['state' => '0', 'msg' => '没有权限'];
                    }
                    abort(403);
                }
            }
        }

        return $next($request);
    }

}
