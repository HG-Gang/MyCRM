<!DOCTYPE HTML>
<html>
    <head>
        @include('admin.include.head')
        <!--[if IE 6]>
        <script type="text/javascript" src="lib/DD_belatedPNG_0.0.8a-min.js" ></script>
        <script>DD_belatedPNG.fix('*');</script>
        <![endif]-->
        <title></title>
    </head>
    <style>
        .pd-5 {
            padding: 0px;
        }

        .mt-20 {
            margin-top: 0px;
        }   
        .text{
            margin-left: 10px;
            float: left;
            margin-bottom: 15px;
            width: 25%;
        }
        .form{
            margin-left: 13%;
        }
        .text_1{
            margin-left: 189px;
        }
        .yes{
            background: #7cc33c;
            width: 37px;
            height: 24px;
            display: inline-block;
            line-height: 24px;
            font-size: 14px;
            color: #fff;
            border-radius: 18%;
            text-align: center;
        }
        .no{
            background: #ccc;
            width: 37px;
            height: 24px;
            display: inline-block;
            line-height: 24px;
            font-size: 14px;
            color: #fff;
            border-radius: 18%;
            text-align: center;
        }
        .yes_1{
            display: inline-block;
            width: 46px;
            height: 24px;
            background: #7cc33c;
            line-height: 24px;
            color: #fff;
            border-radius: 13%;
        }
        .no_1{
            display: inline-block;
            width: 46px;
            height: 24px;
            background: #ccc;
            line-height: 24px;
            color: #fff;
            border-radius: 13%; 
        }
    </style>
    <body>
        <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 代理商管理 <span class="c-gray en">&gt;</span> 代理商列表
            @if(count($date)>0)
            <span class="c-gray en">&gt;</span>我的位置：
            @foreach($date as $key=>$v)
            @if($key!=0)<span class="c-gray en">&gt;</span>@endif <a href="{{url(route_prefix(). '/agent')}}/{{$v->user_id}}">{{$v->user_name}}【{{$v->user_id}}】</a>
            @endforeach 
            @endif
            <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
        <div class="page-container">
            <form action="" method="get" class="form">
                <input type="hidden" name="form" value="01010">
                <div class="text"> 交易账号：    
                    <input type="text" class="input-text" style="width:250px" placeholder="输入交易账号"  name="user_id" value="{{$request->input('user_id')}}">  
                </div>    
                <div class="text"> 账户姓名：    
                    <input type="text" class="input-text" style="width:250px" placeholder="输入账户姓名"  name="user_name" value="{{$request->input('user_name')}}">  
                </div> 
                <div class="text"> 开户时间：
                    <input type="text" onfocus="WdatePicker({maxDate: '#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}'})" id="datemin" class="input-text Wdate" style="width:120px;" name="startdate" value="{{$request->input('startdate')}}" placeholder="开始时间">-
                    <input type="text" onfocus="WdatePicker({minDate: '#F{$dp.$D(\'datemin\')}', maxDate: '%y-%M-%d'})" id="datemax" class="input-text Wdate" style="width:120px;" name="enddate" value="{{$request->input('enddate')}}" placeholder="结束时间">
                </div>
                <div class="text"> 是否认证：    
                    <!--<input type="text" class="input-text" style="width:250px" placeholder="输入会员名称、电话" id="" name="username" value="">-->  
                    <select class="input-text" size="1" name="user_status" style="width:250px">
                        <option >请选择状态</option>
                        <option value="0" @if($request->input('user_status')==='0') selected @endif>未认证</option>
                        <option value="1" @if($request->input('user_status')==1) selected @endif >已认证</option>
                    </select>
                </div> 
                <div class="text"> 账户模式：    
                    <select class="input-text" size="1" name="trans_mode" style="width:250px">
                        <option >请选择账户模式</option>
                        <option value="0"  @if($request->input('trans_mode')==='0') selected @endif>返佣模式</option>
                        <option value="1" @if($request->input('trans_mode')==1) selected @endif>权益模式</option>
                    </select>
                </div> 
                <div class="text text_1">
                    <button type="submit" class="btn btn-success radius" ><i class="Hui-iconfont">&#xe665;</i>查找</button>
                    <button type="reset" class="btn btn-primary radius" ><i class="Hui-iconfont">&#xe66c;</i> 重置</button>
                </div>
            </form>
            <!--<div class="cl pd-5 bg-1 mt-20">  <span class="r">共有数据：<strong></strong> {{$mun_s}}条</span> </div>-->

            <div class="mt-20">   
                <table class="table table-border table-bordered table-hover table-bg table-sort"> 
                    <thead>
                        <tr class="text-c"> 
                            <th style="display: none"></th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.agetns_no")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.agetns_name")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.account_type")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.account_status")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.is_confirm_agents_lvg")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.account_upper_pid")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.agetns_count")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.agetns_account_count")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.agetns_balance")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.agetns_net_valueo")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.agetns_fy_money")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.agetns_rj_money")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.agetns_qk_money")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.agents_rights")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.agetns_rec_crt_date")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.agents_action")}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list as $v)
                        <tr class="text-c">
                            <td style="display: none"></td>
                            <td>{{$v['user_id']}}</td>
                            <td>{{$v['user_name']}}</td>
                            @if($v['group_id']==1)
                            @if($v['is_confirm_agents_lvg']==0) 
                            <td>                  
                                <a title="代理级别未确认" href="javascript:;"  class="ml-5" style="text-decoration:none">   
                                    一级 
                                </a>     
                            </td>
                            @else
                            <td>
                                <a title="代理级别已确认" href="javascript:;"  class="ml-5" style="text-decoration:none">                                  
                                    一级 
                                </a>
                            </td>
                            @endif
                            @elseif($v['group_id']==2)
                            @if($v['is_confirm_agents_lvg']==0) 
                            <td>
                                <a title="代理级别未确认" href="javascript:;"  class="ml-5" style="text-decoration:none">
                                    二级
                                </a>
                            </td>
                            @else
                            <td>
                                <a title="代理级别已确认" href="javascript:;"  class="ml-5" style="text-decoration:none">
                                    二级
                                </a>
                            </td>
                            @endif
                            @elseif($v['group_id']==3)
                            @if($v['is_confirm_agents_lvg']==0) 
                            <td>
                                <a title="代理级别未确认" href="javascript:;"  class="ml-5" style="text-decoration:none">
                                    三级
                                </a>
                            </td>
                            @else
                            <td>
                                <a title="代理级别已确认" href="javascript:;"  class="ml-5" style="text-decoration:none">
                                    三级
                                </a>
                            </td>
                            @endif
                            @elseif($v['group_id']==4)
                            @if($v['is_confirm_agents_lvg']==0) 
                            <td>
                                <a title="代理级别未确认" href="javascript:;"  class="ml-5" style="text-decoration:none">
                                    四级
                                </a>
                            </td>
                            @else
                            <td title="代理级别已确认">
                                <a title="代理级别已确认" href="javascript:;"  class="ml-5" style="text-decoration:none">
                                    四级
                                </a>
                            </td>
                            @endif
                            @endif
                            <td>
                                @if($v['user_status']==0) 
                                <span class="no">否</span>
                                @else
                                <span class="yes">是</span>
                                @endif
                            </td> 
                            <td>
                                @if($v['is_confirm_agents_lvg']==0)    
                                <span class="no_1">未确认</span>
                                @else
                                <span class="yes_1">已确认</span>
                                @endif
                            </td> 
                            <td>{{$v['parent_id']}}</td> 
                            <td>
                                @if($v['mun']==0)
                                {{$v['mun']}}
                                @else
                                <a title="查看直属代理商" href="{{url(route_prefix() . '/agent')}}/{{$v['user_id']}}"  class="ml-5" style="text-decoration:none;color: #8500ff;font-size: 14px">
                                    {{$v['mun']}}
                                </a>
                                @endif
                            </td>
                            <td>
                                @if($v['user_mun']==0)
                                {{$v['user_mun']}}
                                @else
                                <a title="查看直属客户" href="javascript:;"  onclick="member_edit('{{$v['user_id']."的直属客户列表"}}', '{{url(route_prefix() . '/customer').'/'.$v['user_id']}}', '{{$v['user_id']}}')" class="ml-5" style="text-decoration:none;color: #8500ff;font-size: 14px">
                                    {{$v['user_mun']}}
                                </a>
                                @endif
                            </td>                      
                            <td>{{sprintf("%.2f",$v['BALANCE'])}}</td>
                            <td>{{sprintf("%.2f",$v['EQUITY'])}}</td>
                            <td>{{sprintf("%.2f",$v['money']['0']->total_fy)}}</td>
                            <td>{{sprintf("%.2f",$v['money']['0']->total_rj)}}</td>
                            <td>{{sprintf('%.2f',$v['money']['0']->total_qk)}}</td> 
                            <td>{{$v['rights']}}</td>
                            <td>{{$v['REGDATE']}}</td>
                            <td class="td-manage">   
                                @if($state==3)            
                                @if($v['is_confirm_agents_lvg']==1 && $v['trans_mode']==1)         
                                <a title="编辑" href="javascript:;" onclick="member_update('编辑代理商', '{{url(route_prefix(). "/agent/edit")."/".$v['user_id']}}', '800', '600')" class="ml-5" style="text-decoration:none">
                                    <i class="Hui-iconfont">&#xe60c;</i>
                                </a>
                                @else
                                <a title="查看" href="javascript:;" onclick="member_update('查看理商', '{{url(route_prefix(). "/agent/edit")."/".$v['user_id']}}', '800', '600')" class="ml-5" style="text-decoration:none">
                                    <i class="Hui-iconfont">&#xe720;</i>
                                </a>
                                @endif  
                                @else              
                                <a title="编辑" href="javascript:;" onclick="member_update('编辑代理商', '{{url(route_prefix(). "/agent/edit")."/".$v['user_id']}}', '800', '600')" class="ml-5" style="text-decoration:none">
                                    <i class="Hui-iconfont">&#xe60c;</i>
                                </a>         
                                @endif           

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tr  class="text-c">
                        <td colspan="8" style="text-align: left">总计：</td>
                        <td style="color: green">{{$total_balance}}</td>
                        <td style="color: green">{{$total_net_value}}</td>
                        <td style="color: green">{{$total_fy_zong}}</td>
                        <td style="color: green">{{$total_rj_zong}}</td>
                        <td style="color: green">{{$total_qk_zong}}</td>
                        <td colspan="1"></td>
                    </tr>
                </table>
            </div>   
        </div>
        <!--_footer 作为公共模版分离出去-->
        @include('admin.include.footer')
        <!--/_footer 作为公共模版分离出去-->

        <!--请在下方写此页面业务相关的脚本-->
        <script type="text/javascript" src="{{asset('/admin/lib/My97DatePicker/4.8/WdatePicker.js')}}"></script> 
        <script type="text/javascript" src="{{asset('admin/lib/datatables/1.10.0/jquery.dataTables.min.js')}}"></script> 
        <script type="text/javascript" src="{{asset('admin/lib/laypage/1.2/laypage.js')}}"></script>
        <script type="text/javascript">

                           $(function () {
                           $('.table-sort').dataTable({
                           "aaSorting": [[1, "desc"]], //默认第几个排序
                                   "bStateSave": true, //状态保存
                                   "aoColumnDefs": [
                                           //{"bVisible": false, "aTargets": [ 3 ]} //控制列的隐藏显示
                                           {"orderable": false, "aTargets": [0, 8, 9]}// 制定列不参与排序
                                   ]

                           });
                           $('#DataTables_Table_0_filter').hide();
                           });
                           /*用户-添加*/
                           function member_update(title, url, w, h) {
                           layer_show(title, url, w, h);
                           }
                           /*用户-查看*/
                           function member_show(title, url, id, w, h) {
                           layer_show(title, url, w, h);
                           }
                           /*用户-停用*/
                           function member_stop(obj, id) {
                           layer.confirm('确认要停用吗？', function (index) {
                           $.ajax({
                           type: 'POST',
                                   url: '',
                                   dataType: 'json',
                                   success: function (data) {
                                   $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="member_start(this,id)" href="javascript:;" title="启用"><i class="Hui-iconfont">&#xe6e1;</i></a>');
                                   $(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">已停用</span>');
                                   $(obj).remove();
                                   layer.msg('已停用!', {icon: 5, time: 1000});
                                   },
                                   error: function (data) {
                                   console.log(data.msg);
                                   },
                           });
                           });
                           }

                           /*用户-启用*/
                           function member_start(obj, id) {
                           layer.confirm('确认要启用吗？', function (index) {
                           $.ajax({
                           type: 'POST',
                                   url: '',
                                   dataType: 'json',
                                   success: function (data) {
                                   $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="member_stop(this,id)" href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe631;</i></a>');
                                   $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已启用</span>');
                                   $(obj).remove();
                                   layer.msg('已启用!', {icon: 6, time: 1000});
                                   },
                                   error: function (data) {
                                   console.log(data.msg);
                                   },
                           });
                           });
                           }

                           /*密码-修改*/
                           function change_password(title, url, id, w, h) {
                           layer_show(title, url, w, h);
                           }
                           /*用户-删除*/
                           function member_del(obj, id) {
                           layer.confirm('确认要删除吗？', function (index) {
                           $.ajax({
                           type: 'POST',
                                   url: '',
                                   dataType: 'json',
                                   success: function (data) {
                                   $(obj).parents("tr").remove();
                                   if (data.stuate == 1) {
                                   layer.msg('已删除!', {icon: 1, time: 1000});
                                   } else {
                                   layer.msg(data.msg, {icon: 2, time: 2000});
                                   }

                                   },
                                   error: function (data) {
                                   console.log(data.msg);
                                   },
                           });
                           });
                           }
                           /*查看直属客户*/
                           function member_edit(title, url, id, w, h) {
                           var index = layer.open({
                           type: 2,
                                   title: title,
                                   content: url
                           });
                           layer.full(index);
                           }




        </script> 
    </body>
</html>