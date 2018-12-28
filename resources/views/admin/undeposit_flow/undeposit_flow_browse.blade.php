@extends('user.layout.main_right')

@section('public-resources')
@endsection

@section('content')
    <form class="layui-form" action="" id="AdminUnDepositFlowForm" style="margin-top: 8px;">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">交易账户</label>
                <div class="layui-input-block">
                    <input type="text" name="userId" id="userId" autocomplete="off" placeholder="请输入交易账户" class="layui-input" style="width: 200px;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">订单号</label>
                <div class="layui-input-block">
                    <input type="text" name="undeposit_id" id="undeposit_id" autocomplete="off" placeholder="请输入订单号" class="layui-input" style="width: 200px;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">未付时间</label>
                <div class="layui-input-inline" style="width: 200px;">
                    <input type="text" name="deposit_startdate" id="deposit_startdate" placeholder="请输入开始时间" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid">-</div>
                <div class="layui-input-inline" style="width: 200px;">
                    <input type="text" name="deposit_enddate" id="deposit_enddate" placeholder="请输入结束时间" autocomplete="off" class="layui-input">
                </div>
            </div>
            <button type="button" class="layui-btn" onclick="createTable()">查找</button>
        </div>
    </form>
    <div style="margin:20px 0px;"></div>
    <table id="undeposit_data_list" style="width: 99%;" pagination="true" title="未付流水列表"></table>
@endsection

@section('custom-resources')
    <script>
        function dataGridConfig() {
            var config = {};
            config.DataColumns = [[
                {field:'rec_crt_user' ,title:'{{ trans ('systemlanguage.account_undeposit_uid') }}', width:100, align:'center',},
                {field:'dep_outChannelNo' ,title:'{{ trans ('systemlanguage.account_undeposit_orderno_otc') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
                    if (value != "undefined") {
                        return value;
                    } else {
                        return "";
                    }
                }},
                {field:'dep_outTrande' ,title:'{{ trans ('systemlanguage.account_undeposit_orderno_loc') }}', width:100, align:'center',},
                {field:'dep_act_amount' ,title:'{{ trans ('systemlanguage.account_undeposit_moneny') }}', width:100, align:'center',formatter: function (value, rowData, rowIndex) {
                    return parseFloatToFixed(value);
                }},
                {field:'dep_amount' ,title:'{{ trans ('systemlanguage.account_undeposit_depamount') }}', width:100, align:'center',formatter: function (value, rowData, rowIndex) {
                    return parseFloatToFixed(value);
                }},
                {field:'dep_body' ,title:'{{ trans ('systemlanguage.account_undeposit_comment') }}', width:100, align:'center',},
                {field:'dep_amt_rate' ,title:'{{ trans ('systemlanguage.account_undeposit_rate') }}', width:100, align:'center',},
                {field:'rec_crt_date' ,title:'{{ trans ('systemlanguage.account_undeposit_datetme') }}', width:100, align:'center'},
            ]];

            return config;
        }

        function createTable() {
            var config = dataGridConfig(), pagerData;
            pagerData = new $.WidgetPage({
                reqUrl: route_prefix() + "/amount/undepositFlowSearch",
                tableId: "undeposit_data_list",
                formId: "AdminUnDepositFlowForm",
                method: 'post',
                columns : config.DataColumns,
                //buttons: config.Buttons,
                formToken: "{{ csrf_token() }}",
                idField: 'deo_id',
                //extraParam: subPuid,
                rownumbers: true,
                singleSelect: true,
                showFooter: false,
            });

            pagerData.GridInit();
        }

        //双击更改直属客户组别信息
        function DbClickEditAccountInfo(rowIndex, rowData) {
            console.log("没有可查看的信息");
        }
    </script>
@endsection