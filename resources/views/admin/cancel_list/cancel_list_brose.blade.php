@extends('user.layout.main_right')

@section('public-resources')
@endsection

@section('content')
    <form class="layui-form" action="" id="AdminCancellForm" style="margin-top: 8px;">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">申请账户</label>
                <div class="layui-input-block">
                    <input type="text" name="userId" id="userId" autocomplete="off" placeholder="请输入交易账户" class="layui-input" style="width: 200px;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">申请状态</label>
                <div class="layui-input-inline">
                    <select name="cancel_status" id="cancel_status">
                        <option value="">请选择申请状态</option>
                        <option value="0" selected="selected">等待处理</option>
                        <option value="1">处理成功</option>
                        <option value="-1">处理失败</option>
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">申请时间</label>
                <div class="layui-input-inline" style="width: 200px;">
                    <input type="text" name="startdate" id="startdate" placeholder="请输入开始时间" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid">-</div>
                <div class="layui-input-inline" style="width: 200px;">
                    <input type="text" name="enddate" id="enddate" placeholder="请输入结束时间" autocomplete="off" class="layui-input">
                </div>
            </div>
            <button type="button" class="layui-btn" onclick="createTable()">查找</button>
        </div>
    </form>

    <from class="layui-form"id="not_pass" name="not_pass" style="margin-top: 8px; display: none;">
        <div class="layui-form-item" style="margin-top: 15px;">
            <div class="layui-inline">
                <label class="layui-form-label">拒绝原因</label>
                <div class="layui-input-block">
                    <input type="text" name="cancel_remark" id="cancel_remark" autocomplete="off" placeholder="请输入拒绝原因" class="layui-input" style="width: 200px;">
                    <input type="hidden" id="refuse_cancel_userid" name="refuse_cancel_userid" readonly="readonly">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="button" class="layui-btn" onclick="cancel_not_pass_reason()">提交</button>
            </div>
        </div>
    </from>
    <div style="margin:20px 0px;"></div>
    <table id="cancell_data_list" style="width: 99%;" pagination="true" title="销户列表"></table>
@endsection

@section('custom-resources')
    <script>
        function dataGridConfig() {
            var config = {};
            config.DataColumns = [[
                {field:'cancel_userid' ,title:'{{ trans ('systemlanguage.cancel_apply_id') }}', width:100, align:'center',},
                {field:'cancel_username' ,title:'{{ trans ('systemlanguage.cancel_apply_name') }}', width:100, align:'center',},
                {field:'bal' ,title:'{{ trans ('systemlanguage.cancel_apply_bal') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
                        if(value < 0) {
                            return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
                        }
                        return parseFloatToFixed(value);
                    }},
                {field:'vol' ,title:'{{ trans ('systemlanguage.cancel_apply_vol') }}', width:100, align:'center',},
                {field:'cancel_status' ,title:'{{ trans ('systemlanguage.cancel_apply_status') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
                    var str = "";

                    if(value == '0') {
                        str = "<span style='color: #B8860B;'>"+ '等待处理' +"</span>";
                    } else if (value == '1') {
                        str = "<span style='color: green;'>"+ '处理成功' +"</span>";
                    } else if (value == '-1') {
                        str = "<span style='color: red;'>"+ '处理失败' +"</span>";
                    }

                    return str;
                }},
                {field:'cancel_remark' ,title:'{{ trans ('systemlanguage.cancel_apply_remark') }}', width:100, align:'center',formatter: function (value, rowData, rowIndex) {
                    var str = "";

                    if(rowData.cancel_status== '0') {
                        str = "<span style='color: #000000;'>"+ '------' +"</span>";
                    } else if (rowData.cancel_status== '1') {
                        str = "<span style='color: #000000;'>"+ '------' +"</span>";
                    } else if (rowData.cancel_status== '-1') {
                        str = "<span style='color: red;'>"+ value +"</span>";
                    }

                    return str;
                }},
                {field:'rec_crt_date' ,title:'{{ trans ('systemlanguage.cancel_apply_datetime') }}', width:100, align:'center',},
                {field:'userOptions' ,title:'{{ trans ('systemlanguage.cancel_apply_action') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
                    if (rowData.cancel_status == "0") {
                        return "<span style='color: blue; cursor: pointer;' onclick='apply_change_pass("+ rowData.cancel_userid +")'>"+ '接受' +"</span>" +
                            "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
                            + "<span style='color: blue; cursor: pointer;' onclick='not_pass_box("+ rowData.cancel_userid +")'>"+ '拒绝' +"</span>";
                    } else {
                        return "-------------------";
                    }
                }},
            ]];

            return config;
        }

        function createTable() {
            var config = dataGridConfig(), pagerData;
            pagerData = new $.WidgetPage({
                reqUrl: route_prefix() + "/cancel/userlistSearch",
                tableId: "cancell_data_list",
                formId: "AdminCancellForm",
                method: 'post',
                columns : config.DataColumns,
                //buttons: config.Buttons,
                formToken: "{{ csrf_token() }}",
                idField: 'cancel_userid',
                extraParam: subPuid,
                rownumbers: true,
                singleSelect: true,
                showFooter: true,
            });

            pagerData.GridInit();
        }

        //双击更改直属客户组别信息
        function DbClickEditAccountInfo(rowIndex, rowData) {
            console.log("没有可查看的信息");
        }

        function not_pass_box(cancel_userid) {
            $("#refuse_cancel_userid").val(cancel_userid);
            var layer = layui.layer;
            layer.open({
                title: "拒绝申请",
                type: 1,
                area: ["360px", "180px"],
                skin: "layui-layer-molv",
                shade: [0.6, '#393D49'],
                move: false,
                content: $("#not_pass"),
            });
        }

        function apply_change_pass(cancel_userid) {
            var str = "<div>";
            str += "<p>" + "确定接受" + "【" + "<span style='color: green; font-weight: bold;'>" + cancel_userid + "</span>" + "】" + "的账户注销申请吗?";
            str += "</p></div>";
            var _token = "{{ csrf_token() }}";
            layer.confirm(str, {icon: 3, title:'操作确认提示'}, function(index) {
                var index1 = openLoadShade();
                $.ajax({
                    url: route_prefix() + '/cancel/cancel_apply_pass',
                    data: {
                        _token: _token,
                        cancel_userid: cancel_userid
                    },
                    dateType: "JSON",
                    type: "POST",
                    async: false,
                    success: function (data) {
                        closeLoadShade(index1);
                        if (data.msg == "SUCCESS") {
                            layer.closeAll();
                            layer.msg("操作成功!", {time: 4000});
                            createTable();
                            return;
                        } else if (data.msg == "FAIL") {
                            layer.closeAll();
                            if (data.col == "INVALIDUSER") {
                                layer.msg("无效的账户!", {time: 2000});
                            } else if (data.col == "FATALCANOTCONNECT") {
                                layer.msg("网络故障,请稍后再操作!", {time: 2000});
                            } else if (data.col == "UPDATEFAIL") {
                                layer.msg("更新失败,请稍后再操作!", {time: 2000});
                            }

                            return;
                        }
                    },
                    error: function (data) {
                        closeLoadShade(index1);
                        alert('系统错误，请刷新重新操作')
                    }
                });
            });
        }

        function cancel_not_pass_reason() {
            var cancel_remark = $("#cancel_remark").val();
            var cancel_userid = $("#refuse_cancel_userid").val();
            var _token = "{{ csrf_token() }}";
            if( cancel_remark == "" ) {
                layer.msg("请输入拒绝原因");
                return;
            } else {
                var index1 = openLoadShade();
                $.ajax({
                    url: route_prefix() + '/cancel/cancel_apply_nopass',
                    data: {
                        _token: _token,
                        cancel_userid: cancel_userid,
                        cancel_remark: cancel_remark,
                    },
                    dateType: "JSON",
                    type: "POST",
                    async: false,
                    success: function (data) {
                        closeLoadShade(index1);
                        if (data.msg == "SUCCESS") {
                            layer.closeAll();
                            layer.msg("变更操作成功!", {time: 2000});
                            createTable();
                            return;
                        } else if (data.msg == "FAIL") {
                            layer.closeAll();
                            layer.msg("操作失败,请重新操作!", {time: 2000});
                            return;
                        }
                    },
                    error: function (data) {
                        closeLoadShade(index1);
                        alert('系统错误，请刷新重新操作')
                    }
                });
            }
        }
    </script>
@endsection