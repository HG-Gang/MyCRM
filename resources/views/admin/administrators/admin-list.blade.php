<!DOCTYPE HTML>
<html>
    <head>
        @include('admin.include.head')
        <!--[if IE 6]>
        <script type="text/javascript" src="lib/DD_belatedPNG_0.0.8a-min.js" ></script>
        <script>DD_belatedPNG.fix('*');</script>
        <![endif]-->
        <title>管理员列表</title>
    </head>
    <body>
        <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 管理员管理 <span class="c-gray en">&gt;</span> 管理员列表 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
        <div class="page-container">
            <!--            <div class="text-c"> 日期范围：
                            <input type="text" onfocus="WdatePicker({ maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}' })" id="datemin" class="input-text Wdate" style="width:120px;">
                            -
                            <input type="text" onfocus="WdatePicker({ minDate:'#F{$dp.$D(\'datemin\')}', maxDate:'%y-%M-%d' })" id="datemax" class="input-text Wdate" style="width:120px;">
                            <input type="text" class="input-text" style="width:250px" placeholder="输入管理员名称" id="" name="">
                            <button type="submit" class="btn btn-success" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜用户</button>
                        </div>-->
            <div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l">
                    <!--<a href="javascript:;" onclick="datadel()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a>-->
                    <a href="javascript:;" onclick="admin_add('添加管理员', '{{url(route_prefix() . '/Administrators/add')}}', '800', '500')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加管理员</a></span> <span class="r">共有数据：<strong>{{$mun}}</strong> 条</span> </div>
            <div class="mt-20">
                <table class="table table-border table-bordered table-hover table-bg table-sort">
                    <thead>
<!--                        <tr>
                            <th scope="col" colspan="9">管理员列表</th>
                        </tr>-->
                        <tr class="text-c">
                            <th width="25"><input type="checkbox" name="" value=""></th>
                            <th width="40">ID</th>
                            <th width="150">登录名</th>
                            <th width="90">手机</th>
                            <th width="150">邮箱</th>
                            <th width="250">角色</th>
                            <th width="150">创建人</th>
                            <th width="130">加入时间</th>
                            <th width="100">是否已启用</th>
                            <th width="100">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user as $v)
                        <tr class="text-c">
                            <td><input type="checkbox" value="{{$v->id}}" name=""></td>
                            <td>{{$v->id}}</td>
                            <td>{{$v->username}}</td>
                            <td>{{$v->mobile}}</td>
                            <td>{{$v->email}}</td>
                            <td>{{RoleName($v->role_id)}}</td>
                            <td>{{$v->created_name}}</td>
                            <td>{{$v->created_at}}</td>
                            @if($v->state==1)
                            <td class="td-status"><span class="label label-success radius">已启用</span></td>
                            @else
                            <td class="td-status"><span class="label radius">已停用</span></td>
                            @endif
                            <td class="td-manage">
                                {{--@if($v->username != 'admin')--}}
                                    @if($v->state==1)
                                        <a style="text-decoration:none" onClick="admin_stop(this,'{{$v->id}}')" href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe631;</i></a>
                                    @else
                                        <a style="text-decoration:none" onClick="admin_start(this,'{{$v->id}}')" href="javascript:;" title="启用"><i class="Hui-iconfont">&#xe615;</i></a>
                                    @endif
                                    <a title="编辑" href="javascript:;" onclick="admin_edit('管理员编辑', '{{url(route_prefix() . "/Administrators/edit/$v->id")}}', '{{$v->id}}', '800', '500')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a> <a title="删除" href="javascript:;" onclick="admin_del(this, '{{$v->id}}')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
                                {{--@else
                                    =======
                                @endif--}}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!--_footer 作为公共模版分离出去-->
        @include('admin.include.footer')
        <!--/_footer 作为公共模版分离出去-->

        <!--请在下方写此页面业务相关的脚本-->
        <script type="text/javascript" src="{{asset('/admin/lib/My97DatePicker/4.8/WdatePicker.js')}}?ver={{ resource_version_number() }}"></script>
        <script type="text/javascript" src="{{asset('/admin/lib/datatables/1.10.0/jquery.dataTables.min.js')}}?ver={{ resource_version_number() }}"></script>
        <script type="text/javascript" src="{{asset('/admin/lib/laypage/1.2/laypage.js')}}?ver={{ resource_version_number() }}"></script>
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
                                    });
                                    /*
                                     参数解释：
                                     title	标题
                                     url		请求的url
                                     id		需要操作的数据id
                                     w		弹出层宽度（缺省调默认值）
                                     h		弹出层高度（缺省调默认值）
                                     */
                                    /*管理员-增加*/
                                    function admin_add(title, url, w, h){
                                    layer_show(title, url, w, h);
                                    }
                                    /*管理员-删除*/
                                    function admin_del(obj, id){
                                    layer.confirm('确认要删除吗？', function(index){
                                    $.ajax({
                                    type: 'get',
                                            url: "{{url('/Administrators/del')}}",
                                            dataType: 'json',
                                            data:{'id':id},
                                            success: function(data){
                                            if (data.statue == 1){
                                            $(obj).parents("tr").remove();
                                            layer.msg('已删除!', {icon:1, time:1000});
                                            } else{
                                            layer.msg(data.msg, {icon:2, time:1000});
                                            }

                                            },
                                            error:function(data) {
                                            console.log(data.msg);
                                            },
                                    });
                                    });
                                    }

                                    /*管理员-编辑*/
                                    function admin_edit(title, url, id, w, h){
                                    layer_show(title, url, w, h);
                                    }
                                    /*管理员-停用*/
                                    function admin_stop(obj, id){

                                    layer.confirm('确认要停用吗？', function(index){
                                    //此处请求后台程序，下方是成功后的前台处理……
                                    $.ajax({
                                    url:"{{url(route_prefix() . '/Administrators/stop')}}",
                                            type:"get",
                                            data:{'id':id},
                                            dataType:'json',
                                            success:function(d){
                                            if (d.statue == 1){
                                            var id = d.id;
                                            $(obj).parents("tr").find(".td-manage").prepend('<a onClick="admin_start(this,' + id + ')" href="javascript:;" title="启用" style="text-decoration:none"><i class="Hui-iconfont">&#xe615;</i></a>');
                                            $(obj).parents("tr").find(".td-status").html('<span class="label label-default radius">已禁用</span>');
                                            $(obj).remove();
                                            layer.msg('已停用!', {icon: 5, time:1000});
                                            } else{
                                            layer.msg(data.msg, {icon: 5, time:1000});
                                            }

                                            }
                                    });
                                    //  location.href="{{url(route_prefix() . '/Administrators/list')}}"
                                    });
                                    }

                                    /*管理员-启用*/
                                    function admin_start(obj, id){
                                    layer.confirm('确认要启用吗？', function(index){
                                    //此处请求后台程序，下方是成功后的前台处理……
                                    $.ajax({
                                    url:"{{url(route_prefix() . '/Administrators/start')}}",
                                            type:"get",
                                            data:{'id':id},
                                            dataType:'json',
                                            success:function(d){
                                            if (d.statue == 1){
                                            var id = d.id;
                                            $(obj).parents("tr").find(".td-manage").prepend('<a onClick="admin_stop(this,' + id + ')" href="javascript:;" title="停用" style="text-decoration:none"><i class="Hui-iconfont">&#xe631;</i></a>');
                                            $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已启用</span>');
                                            $(obj).remove();
                                            layer.msg('已启用!', {icon: 6, time:1000});
                                            } else{
                                            layer.msg(data.msg, {icon: 6, time:1000});
                                            }

                                            }
                                    });
                                    });
                                    }
        </script>
    </body>
</html>