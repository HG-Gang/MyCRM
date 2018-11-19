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
        <div class="page-container">
            <div class="mt-20">   
                <table class="table table-border table-bordered table-hover table-bg table-sort"> 
                    <thead>
                        <tr class="text-c"> 
                            <th style="display: none"></th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.account_type")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.account_group")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.account_no")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.account_name")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.account_balance")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.account_net_value")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.account_deposit_moneny")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.account_withdrawal_moneny")}}</th>
                            <th title="净入金 = 入金 - 出金" style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.position_summary_net_deposit")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.position_summary_total_comm")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.position_summary_total_money")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.position_summary_noble")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.position_summary_for_exca")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.position_summary_crud_oil")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.position_summary_index")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.position_summary_total_vol")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.position_summary_interest")}}</th>
                            <th style="background-color: #335b9f; color: #fff;">{{trans("systemlanguageadmin.Registration_time")}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user as $v)
                        <tr class="text-c">
                            <td style="display: none"></td>
                            <td>
                                @if(GroupId($v->mt4_grp)==1)   
                                <span style="color: #1613e6;font-size: 14px">有佣金</span>     
                                @elseif(GroupId($v->mt4_grp)==0)
                                <span style="color:red ;font-size: 14px"> 无佣金</span>
                                @endif
                            </td>
                            <td>{{$v->mt4_grp}}</td>                 
                            <td>{{$v->user_id}}</td>
                            <td>{{$v->user_name}}</td>
                            <td>{{sprintf("%.2f",$v->BALANCE)}}</td>
                            <td>{{sprintf("%.2f",$v->EQUITY)}}</td>                 
                            <td>{{sprintf("%.2f",$v->money->total_rj)}}</td>
                            <td>{{sprintf("%.2f",$v->money->total_qk)}}</td>
                            <td>{{sprintf("%.2f",($v->money->total_rj-$v->money->total_qk))}}</td>
                            <td>{{sprintf("%.2f",$v->category->all_total_comm)}}</td>
                            <td>{{sprintf("%.2f",$v->category->all_total_profit)}}</td>                 
                            <td>{{$v->category->all_total_noble_metal/100}}</td>
                            <td>{{$v->category->all_total_for_exca/100}}</td>
                            <td>{{$v->category->all_total_crud_oil/100}}</td>
                            <td>{{$v->category->all_total_index/100}}</td>                 
                            <td>{{$v->category->all_total_volume/100}}</td>
                            <td>{{sprintf("%.2f",$v->category->all_total_swaps)}}</td>
                            <td>{{$v->REGDATE}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tr  class="text-c">
                        <td colspan="4" style="text-align: left">总计：</td>
                        <td style="color: green">{{$total_balance}}</td>
                        <td style="color: green">{{$total_net_value}}</td>
                        <td style="color: green">{{$total_rj_zong}}</td>
                        <td style="color: green">{{$total_qk_zong}}</td>
                         <td style="color: green">{{$total_rj_zong-$total_qk_zong}}</td>
                        <td style="color: green">{{$all_total_comm_zong}}</td>
                        <td style="color: green">{{$all_total_profit_zong}}</td>
                        <td style="color: green">{{$all_total_noble_metal_zong/100}}</td>
                        <td style="color: green">{{$all_total_for_exca_zong/100}}</td>
                        <td style="color: green">{{$all_total_crud_oil_zong/100}}</td>
                        <td style="color: green">{{$all_total_index_zong/100}}</td>
                        <td style="color: green">{{$all_total_volume_zong/100}}</td>
                        <td style="color: green">{{$all_total_swaps_zong}}</td>
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
function member_add(title, url, w, h) {
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