<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\Role;

class RoleController extends Controller {

    /**
     * Display a listing of the resource.
     * 角色列表
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $uri_list = config('admin_uri');
        $user = Role::get();
        $mun = count($user);
        return view('admin.role.role-list', ['user' => $user, 'mun' => $mun]);
    }

    /**
     * Show the form for creating a new resource.
     * 添加角色
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $uri_list = config('admin_uri');
        // dd($uri_list);
        return view('admin.role.role-add', ['uri_list' => $uri_list]);
    }

    /**
     * Store a newly created resource in storage.
     * 保存添加角色
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $role = new Role();
        $role->username = $request->input('username');
        $role->desc = $request->input('desc');
        $role->acl = json_encode($request->acl);
        if ($role->save()) {
            return json_encode(['msg' => '添加成功', 'state' => '1']);
        } else {
            return json_encode(['msg' => '添加失败', 'state' => '0']);
        }
    }

    /**
     * Display the specified resource.
     * 显示修改角色页面
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $uri_list = config('admin_uri');
        $info = Role::find($id);
        if (!empty($info->acl)) {
            $permission = json_decode($info->acl);
        } else {
            $permission = [];
        }
       // dd($uri_list);
        $url = $_SERVER['HTTP_REFERER'];
        return view('admin.role.role-edit', ['info' => $info, 'url' => $url, 'uri_list' => $uri_list, 'permission' => $permission]);
    }

    /**
     * Show the form for editing the specified resource.
     * 保存修改角色
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editsave(Request $request) {
        $role_id = $request->input('role_id');
        $username = $request->input('username');
        $desc = $request->input('desc');
        $role = Role::find($role_id);
        $role->username = $username;
        $role->desc = $desc;
        $role->acl = json_encode($request->acl);
        if ($role->save()) {
            return json_encode(['state' => '1', 'msg' => '修改成功']);
        } else {
            return json_encode(['state' => '添加失败', 'msg' => '0']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * 删除角色
     * @return \Illuminate\Http\Response
     */
    public function del(Request $request) {
        $id = $request->id;
        $tt = Role::find($id)->delete();
        if ($tt) {
            return json_encode(['msg' => '删除成功', 'state' => '1']);
        } else {
            return json_encode(['msg' => '删除失败', 'state' => '0']);
        }
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
