@extends('user.layout.main_right')

@section('content')

    <form class="layui-form" action="" id="AdminAgentsListForm" style="margin-top: 8px;">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">交易账户</label>
                <div class="layui-input-block">
                    <input type="text" name="userId" id="userId" autocomplete="off" placeholder="请输入交易账户或身份证号或账户名" class="layui-input" style="width: 250px;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">是否认证</label>
                <div class="layui-input-inline">
                    <select name="userstatus" id="userstatus">
                        <option value="">请选择认证状态</option>
                        <option value="0">未认证</option>
                        <option value="1">已认证</option>
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">账户模式</label>
                <div class="layui-input-inline">
                    <select name="transmode" id="transmode">
                        <option value="">请选择账户模式</option>
                        <option value="0">返佣模式</option>
                        <option value="1">权益模式</option>
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">开户时间</label>
                <div class="layui-input-inline" style="width: 200px;">
                    <input type="text" name="startdate" id="startdate" placeholder="请输入开始时间" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid">-</div>
                <div class="layui-input-inline" style="width: 200px;">
                    <input type="text" name="enddate" id="enddate" placeholder="请输入结束时间" autocomplete="off" class="layui-input">
                </div>
            </div>
            <button type="button" class="layui-btn" onclick="searchResult()">查找</button>
        </div>
    </form>
    <div style="margin:20px 0px;"></div>
    <div id="real" style="margin-left: 20px;"></div>
    <table id="data_list" style="width: 99%;" pagination="true" title="代理商列表"></table>
@endsection

@section('custom-resources')
    <script type="text/javascript">
        $(function () {
            autoSearchExtraParam();
            createTable();
        });
        function dataGridConfig() {
            var config = {};
            config.DataColumns = [[
                {field:'user_id' ,title:'{{ trans ('systemlanguage.proxy_user_id') }}', width:100, align:'center',formatter: function (value, rowData, rowIndex) {
                    if (rowData.userstatus == '1') {
                        return "<span class='l-btn-left l-btn-icon-right' title='已认证'>" +
                            "<span class='l-btn-text'>" + value + "</span>" +
                            "<span class='l-btn-icon icon-auth-man'>&nbsp;</span>" +
                            "</span>";
                    }

                    return "<span class='l-btn-left l-btn-icon-right'><span class='l-btn-text'>" + value + "</span></span>";
                }},
                {field:'username' ,title:'{{ trans ('systemlanguage.proxy_user_name') }}', width:100, align:'center',},
                {field:'groupId' ,title:'{{ trans ('systemlanguage.proxy_agents_lvg') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
                    return getAgentsLevel(value);
                }},
                {field:'isconfirmagtlvg' ,title:'{{ trans ('systemlanguage.proxy_agents_confirm') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
                    return IsconfirmLevel(value);
                }},
                {field:'parentId' ,title:'{{ trans ('systemlanguage.proxy_agents_parentId') }}', width:100, align:'center',},
                {field:'agentsTotal',title:'{{ trans ('systemlanguage.proxy_direct_count') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
                    if (value > 0) {
                        return "<span style='cursor:pointer; color:blue;' onclick='DirectSubAgentsDetail(" + rowData.user_id + ")' title='直属代理商总数'>" + value + "</span>";
                    }

                    return value;
                }},
                {field:'accountTotal' ,title:'{{ trans ('systemlanguage.proxy_cust_count') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
                    if (value > 0) {
                        return "<span style='cursor:pointer; color:blue;' onclick='DirectSubCustDetail(" + rowData.user_id + ")' title='直属客户总数'>" + value + "</span>"
                    }

                    return value;
                }},
                {field:'usermoney' ,title:'{{ trans ('systemlanguage.proxy_user_money') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
                    if (value < 0) {
                        return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
                    } else {
                        return parseFloatToFixed(value);
                    }
                }},
                {field:'custeqy' ,title:'{{ trans ('systemlanguage.proxy_cust_eqy') }}' ,width:100 ,align:'center', formatter: function (value, rowData, rowIndex) {
                    if (value < 0) {
                        return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
                    } else {
                        return parseFloatToFixed(value);
                    }
                }},
                {field:'fy_money' ,title:'{{ trans ('systemlanguage.proxy_fy_money') }}' ,width:100 ,align:'center', formatter: function (value, rowData, rowIndex) {
                    return "<span>"+ parseFloatToFixed(value) +"</span>";
                }},
                {field:'rj_money' ,title:'{{ trans ('systemlanguage.proxy_rj_money') }}' ,width:100 ,align:'center', formatter: function (value, rowData, rowIndex) {
                    return "<span>"+ parseFloatToFixed(value) +"</span>";
                }},
                {field:'qk_money' ,title:'{{ trans ('systemlanguage.proxy_qk_money') }}' ,width:100 ,align:'center', formatter: function (value, rowData, rowIndex) {
                    return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
                }},
                {field:'rights' ,title:'{{ trans ('systemlanguage.proxy_agents_commp_rights') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
                    if (rowData.username) {
                        return rowData.commprop + ' / ' + value;
                    } else {
                        return '';
                    }
                }},
				{field:'settlementmodel' ,title:'{{ trans ('systemlanguage.proxy_agents_settlementmodel') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					return getAgentssettlementmodel(value, rowData.username);
				}},
                {field:'rec_crt_date' ,title:'{{ trans ('systemlanguage.proxy_rec_crt_date') }}' ,width:100 ,align:'center'},
                @if($role != 3)
	            {field:'options' ,title:'{{ trans ('systemlanguage.proxy_user_options') }}' ,width:110, align:'center',formatter: function (value, rowData, rowIndex) {
                    if (rowData.user_id && rowData.username) {
                        return '<a href="javascript:;" onclick="agentsEditInfo('+ rowData.user_id +')" class="l-btn l-btn-small l-btn-plain" style="color: blue;">' +
                            '<span class="l-btn-left l-btn-icon-left">' +
                            '<span class="l-btn-text">编辑</span>' +
                            '<span class="l-btn-icon icon-edit">&nbsp;</span>' +
                            '</span>'+
                            '</a>';
                    }
                }},
	            @endif
            ]];

            return config;
        }

        function searchResult() {
            subPuid = {};//清空对象之前的值
            $("#real").html(""); //重置我的位置
            clickSearchExtraParam();
            createTable();
        }

        //双击查看直属代理商信息
        function DbClickEditAccountInfo(rowIndex, rowData) {
            show_direct_cust_info(rowData.user_id, "admin");
        }

        //查看直属下级代理商
        function DirectSubAgentsDetail(uid) {
            subPuid = {};//清空对象之前的值
            SubSearchExtraParam(uid);
            getUserRelationShip(uid, 'admin', 'DirectSubAgentsDetail', "{{ csrf_token() }}");
            createTable();
        }

        //查看直属下级客户
        function DirectSubCustDetail(puid) {
            show_proxy_direct_cust_detail(puid);
        }

        function autoSearchExtraParam() {
            subPuid.searchtype = 'autoSearch';
        }

        function clickSearchExtraParam() {
            subPuid.searchtype = 'clickSearch';
        }

        function SubSearchExtraParam(uid) {
            subPuid.searchtype = 'showSubAgents';
            subPuid.userPid = uid;
        }

        function createTable() {
            var config = dataGridConfig(), pagerData;
            pagerData = new $.WidgetPage({
                //title: ajaxGetTableTitle(),
                reqUrl: route_prefix() + '/agents/agentsListSearch',
                tableId: 'data_list',
                formId: 'AdminAgentsListForm',
                method: 'post',
                columns : config.DataColumns,
                //buttons: config.Buttons,
                formToken: "{{ csrf_token() }}",
                idField: 'user_id',
                extraParam: subPuid,
                rownumbers: true,
                singleSelect: true,
                showFooter: true,
                footerMsg: "双击行显示详情",
            });

            pagerData.GridInit();
        }
        
        function agentsEditInfo(uid) {
            agents_edit_info(uid);
        }
    </script>
@endsection