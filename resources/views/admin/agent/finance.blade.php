  
@if($user->is_confirm_agents_lvg==1 && $user->trans_mode==1)
<form class="form form-horizontal" id="form-admin-add">
    {{ csrf_field() }} 
    <div class="form-child-div">
        <label class="find-font">交易账号：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->user_id}}" placeholder="交易账号" id="user_id" name="user_id"  disabled>
    </div>
    <div class="form-child-div">
        <label class="find-font">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;姓名：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->user_name}}" placeholder="姓名" id="user_name" name="user_name" disabled>
    </div>
    <div class="form-child-div">
        <label class="find-font">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;密码：</label>                  
        <input type="password" class="input-text" autocomplete="off" value="{{$user->password}}" placeholder="密码" id="password" name="password" disabled>
    </div>
    <div class="form-child-div">
        <label class="find-font">身份证号：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->IDcard_no}}" placeholder="身份证号" id="IDcard_no" name="IDcard_no" disabled>
    </div>
    <div class="form-child-div">
        <label class="find-font">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;邮箱：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->email}}" placeholder="邮箱" id="email" name="email" disabled>
    </div>
    <div class="form-child-div">
        <label class="find-font">&nbsp;&nbsp;&nbsp;手机号：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{substr($user->phone,3)}}" placeholder="手机号" id="phone" name="phone" disabled>
    </div>
    <div class="form-child-div">
        <label class="find-font">&nbsp;&nbsp;&nbsp;用户组：</label>                  
        <select class="input-text" name="mt4_grp" size="1" disabled  id="mt4_grp">
            <option value="" >请选择用户组</option>
            @foreach($group as $v)
            <option value="{{$v->user_group_name}}" @if($user->mt4_grp==$v->user_group_name) selected @endif>{{$v->user_group_name}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-child-div">
        <label class="find-font">账户模式：</label>                  
        <select class="input-text" name="trans_mode" size="1" id="trans_mode">
            <option value="" >请选择账户模式</option>
            <option value="0" @if($user->trans_mode==0) selected @endif>返佣模式</option>
            <option value="1" @if($user->trans_mode==1) selected @endif>权益模式</option>
        </select>
    </div>
    <div class="form-child-div">
        <label class="find-font">账户权益：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->rights}}" placeholder="账户权益" id="rights" name="rights">
    </div>
    <div class="form-child-div">
        <label class="find-font">结算周期：</label>                  
        <select class="input-text" name="cycle" size="1" id="cycle">
             <option value="" >请选择账户模式</option>
            <option value="1" @if($user->cycle==1)selected @endif >周结算</option>
            <option value="2" @if($user->cycle==2)selected @endif>半月结算</option>
            <option value="3" @if($user->cycle==3)selected @endif>月结算</option>
        </select>
    </div>
    <div class="form-child-div">
        <label class="find-font">交易杠杆：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->cust_lvg}}" placeholder="交易杠杆" id="cust_lvg" name="cust_lvg" disabled>
    </div>
    <div class="form-child-div">
        <label class="find-font">上级代理：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->parent_id}}" placeholder="上级代理" id="parent_id" name="parent_id" disabled>
    </div>
    <div class="form-child-div">
        <label class="find-font">开户时间：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->rec_crt_date}}" placeholder="开户时间" id="rec_crt_date" name="rec_crt_date" disabled>
    </div>
    <div class="form-child-div">
        <label class="find-font">代理级别：</label>                  
        <select class="input-text" name="group_id" size="1" disabled id="group_id">              
            @if($user->is_confirm_agents_lvg==1)
            @if($user->group_id==1)
            <option value="1" @if($user->group_id==1) selected @endif>一级代理</option>
            @elseif($user->group_id==2)
            <option value="1" @if($user->group_id==1) selected @endif>一级代理</option>
            <option value="2" @if($user->group_id==2) selected @endif>二级代理</option>
            @elseif($user->group_id==3)
            <option value="1" @if($user->group_id==1) selected @endif>一级代理</option>
            <option value="2" @if($user->group_id==2) selected @endif>二级代理</option>
            <option value="3" @if($user->group_id==3) selected @endif>三级代理</option>
            @elseif($user->group_id==4)
            <option value="1" @if($user->group_id==1) selected @endif>一级代理</option>
            <option value="2" @if($user->group_id==2) selected @endif>二级代理</option>
            <option value="3" @if($user->group_id==3) selected @endif>三级代理</option>
            <option value="4" @if($user->group_id==4) selected @endif>四级代理</option>
            @endif
            @else
            <option value="1" @if($user->group_id==1) selected @endif>一级代理</option>
            <option value="2" @if($user->group_id==2) selected @endif>二级代理</option>
            <option value="3" @if($user->group_id==3) selected @endif>三级代理</option>
            <option value="4" @if($user->group_id==4) selected @endif>四级代理</option>
            @endif
        </select>
    </div>
    <div class="form-child-div">
        <label class="find-font">返佣比例：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->comm_prop}}" placeholder="返佣比例" id="comm_prop" name="comm_prop" disabled>
    </div>
    <div class="form-child-div" style="height: 31px;line-height: 31px;">
        <label class="find-font" for="enable">账户启用：</label>  
        <input type="checkbox" disabled name="enable" id="enable"  class="check1" @if($user->enable==1) checked @endif>                 
               <label class="find-font" for="enable_readonly">只读账户：</label> 
        <input type="checkbox" disabled  name="enable_readonly" id="enable_readonly"  class="check1" @if($user->enable_readonly==0) checked @endif> 
               <label class="find-font" for="is_out_money">出金锁定：</label>  
        <input type="checkbox" disabled  name="is_out_money" id="is_out_money"  class="check1" @if($user->is_out_money==1) checked @endif> 
    </div>
    <div class="form-child-div">
        <label class="find-font">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;备注：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->remark}}" placeholder="备注" id="remark" name="remark" disabled>
    </div>
    <div class="form-child-div">
        <label class="find-font">层级关系：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->country}}" placeholder="开户时间" id="country" name="country" disabled>
    </div>
    <div class="row cl">
        <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
            <input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input class="btn layui-btn-primary radius" type="reset" value="&nbsp;&nbsp;重置&nbsp;&nbsp;">
        </div>
    </div>
</form>
@else
<form class="form form-horizontal" id="form-admin-add">
    {{ csrf_field() }} 
    <div class="form-child-div">
        <label class="find-font">交易账号：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->user_id}}" placeholder="交易账号" id="user_id" name="user_id"  disabled>
    </div>
    <div class="form-child-div">
        <label class="find-font">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;姓名：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->user_name}}" placeholder="姓名" id="user_name" name="user_name" disabled>
    </div>
    <div class="form-child-div">
        <label class="find-font">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;密码：</label>                  
        <input type="password" class="input-text" autocomplete="off" value="{{$user->password}}" placeholder="密码" id="password" name="password">
    </div>
    <div class="form-child-div">
        <label class="find-font">身份证号：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->IDcard_no}}" placeholder="身份证号" id="IDcard_no" name="IDcard_no" disabled>
    </div>
    <div class="form-child-div">
        <label class="find-font">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;邮箱：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->email}}" placeholder="邮箱" id="email" name="email" disabled>
    </div>
    <div class="form-child-div">
        <label class="find-font">&nbsp;&nbsp;&nbsp;手机号：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{substr($user->phone,3)}}" placeholder="手机号" id="phone" name="phone" disabled>
    </div>
    <div class="form-child-div">
        <label class="find-font">&nbsp;&nbsp;&nbsp;用户组：</label>                  
        <select class="input-text" name="mt4_grp" size="1" disabled>
            <option value="" >请选择用户组</option>
            @foreach($group as $v)
            <option value="{{$v->user_group_name}}" @if($user->mt4_grp==$v->user_group_name) selected @endif>{{$v->user_group_name}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-child-div">
        <label class="find-font">账户模式：</label>                  
        <select class="input-text" name="trans_mode" size="1" disabled>
            <option value="" >请选择账户模式</option>
            <option value="0" @if($user->trans_mode==0) selected @endif>返佣模式</option>
            <option value="1" @if($user->trans_mode==1) selected @endif>权益模式</option>
        </select>
    </div>
    <div class="form-child-div">
        <label class="find-font">账户权益：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->rights}}" placeholder="账户权益" id="rights" name="rights" disabled>
    </div>
    <div class="form-child-div">
        <label class="find-font">结算周期：</label>                  
        <select class="input-text" name="cycle" size="1" disabled>

            <option value="1" @if($user->cycle==1)selected @endif >周结算</option>
            <option value="2" @if($user->cycle==2)selected @endif>半月结算</option>
            <option value="3" @if($user->cycle==3)selected @endif>月结算</option>
        </select>
    </div>
    <div class="form-child-div">
        <label class="find-font">交易杠杆：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->cust_lvg}}" placeholder="交易杠杆" id="cust_lvg" name="cust_lvg" disabled>
    </div>
    <div class="form-child-div">
        <label class="find-font">上级代理：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->parent_id}}" placeholder="上级代理" id="parent_id" name="parent_id" disabled>
    </div>
    <div class="form-child-div">
        <label class="find-font">开户时间：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->rec_crt_date}}" placeholder="开户时间" id="rec_crt_date" name="rec_crt_date" disabled>
    </div>
    <div class="form-child-div">
        <label class="find-font">代理级别：</label>                  
        <select class="input-text" name="group_id" size="1" disabled>              
            @if($user->is_confirm_agents_lvg==1)
            @if($user->group_id==1)
            <option value="1" @if($user->group_id==1) selected @endif>一级代理</option>
            @elseif($user->group_id==2)
            <option value="1" @if($user->group_id==1) selected @endif>一级代理</option>
            <option value="2" @if($user->group_id==2) selected @endif>二级代理</option>
            @elseif($user->group_id==3)
            <option value="1" @if($user->group_id==1) selected @endif>一级代理</option>
            <option value="2" @if($user->group_id==2) selected @endif>二级代理</option>
            <option value="3" @if($user->group_id==3) selected @endif>三级代理</option>
            @elseif($user->group_id==4)
            <option value="1" @if($user->group_id==1) selected @endif>一级代理</option>
            <option value="2" @if($user->group_id==2) selected @endif>二级代理</option>
            <option value="3" @if($user->group_id==3) selected @endif>三级代理</option>
            <option value="4" @if($user->group_id==4) selected @endif>四级代理</option>
            @endif
            @else
            <option value="1" @if($user->group_id==1) selected @endif>一级代理</option>
            <option value="2" @if($user->group_id==2) selected @endif>二级代理</option>
            <option value="3" @if($user->group_id==3) selected @endif>三级代理</option>
            <option value="4" @if($user->group_id==4) selected @endif>四级代理</option>
            @endif
        </select>
    </div>
    <div class="form-child-div">
        <label class="find-font">返佣比例：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->comm_prop}}" placeholder="返佣比例" id="comm_prop" name="comm_prop" disabled>
    </div>
    <div class="form-child-div" style="height: 31px;line-height: 31px;">
        <label class="find-font" for="enable">账户启用：</label>  <input type="checkbox" disabled  name="enable" id="enable"  class="check1" @if($user->enable==1) checked @endif>                 
                                                                    <label class="find-font" for="enable_readonly">只读账户：</label>  <input type="checkbox" disabled  name="enable_readonly" id="enable_readonly"  class="check1" @if($user->enable_readonly==0) checked @endif> 
                                                                    <label class="find-font" for="is_out_money">出金锁定：</label>  <input type="checkbox" disabled  name="is_out_money" id="is_out_money"  class="check1" @if($user->is_out_money==1) checked @endif> 
    </div>
    <div class="form-child-div">
        <label class="find-font">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;备注：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->remark}}" placeholder="备注" id="remark" name="remark" disabled>
    </div>
    <div class="form-child-div">
        <label class="find-font">层级关系：</label>                  
        <input type="text" class="input-text" autocomplete="off" value="{{$user->country}}" placeholder="开户时间" id="country" name="country" disabled>
    </div>
    <div class="row cl">
        <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
            <input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input class="btn layui-btn-primary radius" type="reset" value="&nbsp;&nbsp;重置&nbsp;&nbsp;">
        </div>
    </div>
</form>
@endif