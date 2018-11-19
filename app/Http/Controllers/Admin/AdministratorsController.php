<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\Admin;
use App\Model\Role;
class AdministratorsController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $user = Admin::get();
        $mun = count($user);
        return view('admin.administrators.admin-list', ['user' => $user, 'mun' => $mun]);
    }

    /**
     * Show the form for creating a new resource.
     * 停用
     * @return \Illuminate\Http\Response
     */
    public function stop(Request $request) {
        $id = $request->input('id');
        $user = Admin::find($id);
        $user->state = 0;
        if($user->save()){
            $request->session()->forget('id');
            return json_encode(['statue' => 1,'msg'=>'停用成功','id'=>$id]);
        }else{
            return json_encode(['statue' => 0,'msg'=>'停用失败','id'=>$id]);
        }
    }

   /**
     * Store a newly created resource in storage.
     * 启用
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function start(Request $request) {
        $id = $request->input('id');
        $user = Admin::find($id);
        $user->state = 1;
        if( $user->save() ){
            return json_encode(['statue' => 1,'msg'=>'启用成功','id'=>$id]);
        }else{
            return json_encode(['statue' => 0,'msg'=>'启用失败','id'=>$id]);
        }
    }
    /**
     * Display the specified resource.
     * 显示修改
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $info = Admin::find($id);
        //角色管理
        $role = Role::get();
        $url=$_SERVER['HTTP_REFERER'];
        return view('admin.administrators.admin-edit', ['info' => $info, 'role' => $role,'url'=>$url]);
    }

   /**
     * Show the form for editing the specified resource.
     * 保存管理员信息
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request) {
        $id = $request->id;
        $admin = Admin::find($id);
        /* if (!password_verify($request->password, $admin->password)) {
            return json_encode(['msg' => '原始密码错误', 'stuate' => '0']);
        } */
        $admin->username = $request->username;
        $admin->password = bcrypt($request->password2);
        $admin->mobile = $request->mobile;
        $admin->email = $request->email;
        $admin->role_id = $request->role_id;
        if ($admin->save()) {
            $request->session()->forget('id');
            return json_encode(['msg' => '编辑成功', 'statue' => '1']);
        } else {
            return json_encode(['msg' => '编辑失败', 'statue' => '0']);
        }
    }

  /*     * *
     * 显示添加管理员页面
     * * */

    public function add() {
     
        $role = Role::get();
        return view('admin.administrators.admin-add', ['role' => $role]);
    }

    /**
     * Show the form for editing the specified resource.
     * 保存管理员信息
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addsave(Request $request) {
        $user=Admin::find(session('id'));
        $admin = new Admin();
        $admin->username = $request->username;
        $admin->password = bcrypt($request->password);
        $admin->mobile = $request->mobile;
        $admin->email = $request->email;
        $admin->role_id = $request->role_id;
        $admin->state = 1;
        $admin->created_name=$user->username;
        if ($admin->save()) {
            return json_encode(['msg' => '添加成功', 'statue' => '1']);
        } else {
            return json_encode(['msg' => '添加失败', 'statue' => '0']);
        }
    }
/***
 *
 *    删除
 * **/
    public function del(Request $request) {
        $id = $request->id;
        $tt = Admin::find($id)->delete();
        if($tt){
            return json_encode(['msg' => '删除成功', 'statue' => '1']);
        }else{
            return json_encode(['msg' => '删除失败', 'statue' => '0']);
        }

    }


}
