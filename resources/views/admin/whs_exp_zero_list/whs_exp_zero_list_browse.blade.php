@extends('user.layout.main_right')

@section('public-resources')

@endsection

@section('content')
	<form class="layui-form" action="" id="AdminWhsExpZeroForm" style="margin-top: 8px;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">爆仓账号</label>
				<div class="layui-input-block">
					<input type="text" name="wez_userid" id="wez_userid" autocomplete="off" placeholder="请输入爆仓账号" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">爆仓姓名</label>
				<div class="layui-input-block">
					<input type="text" name="wez_username" id="wez_username" autocomplete="off" placeholder="请输入爆仓姓名" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">清零状态</label>
				<div class="layui-input-inline">
					<select name="wez_status" id="wez_status">
						<option value="">请选择清零状态</option>
						<option value="1">待清零</option>
						<option value="2">已清零</option>
					</select>
				</div>
			</div>
			<button type="button" class="layui-btn" onclick="createTable()">查找</button>
			<button type="button" class="layui-btn" onclick="onekeySearch()">一键查找并创建记录</button>
		</div>
	</form>
	<div style="margin:20px 0px;"></div>
	<table id="data_list" style="width: 99%;" pagination="false" title="爆仓列表"></table>
@endsection

@section('custom-resources')
	<script type="text/javascript">
		function dataGridConfig() {
			var config = {};
			config.DataColumns = [[
				{field:'wezuserid' ,title:'{{ trans ('systemlanguage.whs_exp_acc_no') }}', width:100, align:'center',},
				{field:'wezusername' ,title:'{{ trans ('systemlanguage.whs_exp_acc_name') }}', width:100, align:'center',},
				{field:'wezuserbal' ,title:'{{ trans ('systemlanguage.whs_exp_bal') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
				    return parseFloatToFixed(value);
				}},
				{field:'wezstatus' ,title:'{{ trans ('systemlanguage.whs_exp_status') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					return wezstatus(value);
				}},
				{field:'options' ,title:'{{ trans ('systemlanguage.proxy_user_options') }}' ,width:110, align:'center',formatter: function (value, rowData, rowIndex) {
					return '<a href="javascript:;" onclick="oneKeyZero('+ rowData.wezuserid +', '+ "'"+ rowData.wezusername +"'"+', '+ rowData.wezuserbal +', '+ rowData.wezusercrt +')" class="l-btn l-btn-small l-btn-plain" style="color: blue;">' +
						'<span class="l-btn-left l-btn-icon-left">' +
						'<span class="l-btn-text">一键清零</span>' +
						'<span class="l-btn-icon icon-redo">&nbsp;</span>' +
						'</span>'+
						'</a>';
				}},
			]];
			
			return config;
		}
		
		function DbClickEditAccountInfo(rowIndex, rowData) {
			console.log("没有可查看的信息!");
		}
		
		function createTable() {
			var config = dataGridConfig(), pagerData;
			pagerData = new $.WidgetPage({
				reqUrl: route_prefix() + '/order/whsExpZeroListSearch',
				tableId: 'data_list',
				formId: 'AdminWhsExpZeroForm',
				method: 'post',
				columns : config.DataColumns,
				formToken: "{{ csrf_token() }}",
				idField: 'wezuserid',
				extraParam: subPuid,
				rownumbers: true,
				singleSelect: true,
				showFooter: true,
				});
			
			pagerData.GridInit();
		}
		
		function oneKeyZero(userId, userName, balance, crdt)
		{
			var index1 = openLoadShade();
			
			$.ajax({
				url: route_prefix() + "/order/oneKeyZero",
				data: {
					userId:                 userId,
					userName:               userName,
					balance:                balance,
					crdt:                   crdt,
					_token:					"{{ csrf_token() }}",
				},
				dateType: "JSON",
				type: "POST",
				async: false,
				success: function(data) {
					if (data.msg == "FAIL") {
						closeLoadShade(index1);
						if (data.err == "crtfail") {
							errorTips("清零失败!", "msg", data.col);
						} else if (data.err == "zerofail") {
							errorTips("服务器网络异常, 请联系技术人员!", "msg", data.col);
						}
					} else if (data.msg == "SUC") {
						layer.msg("清零成功", {
							time: 20000, //20s后自动关闭
							btn: ['知道了'],
							yes: function (index, layero) {
								parent.layer.closeAll();
								createTable();
							}
						});
					}
				},
				error:function(data) {
					closeLoadShade(index1);
					alert("系统错误");
				}
			});
		}
		
		function onekeySearch()
		{
			var index1 = openLoadShade();
			
			$.ajax({
				url: route_prefix() + "/order/oneKeySearch",
				data: {
					_token:					"{{ csrf_token() }}",
				},
				dateType: "JSON",
				type: "POST",
				async: false,
				success: function(data) {
					closeLoadShade(index1);
					if (data.msg == "FAIL") {
						if (data.err == "zerofail") {
							console.log(data.err);
							layer.msg("一键查询成功, 新增 " + "<span style='color: red'>" + data.col + "</span>" + " 条数据!", {icon: 6,});
						}
					} else if (data.msg == "SUC") {
						console.log(data.err);
						layer.msg("一键查询成功, 新增" + "<span style='color: red; font-weight: 600;'>"+ data.col +"</span>" + "条数据!", {icon: 6,});
					}
				},
				error:function(data) {
					closeLoadShade(index1);
					alert("系统错误");
				}
			});
		}
	</script>
@endsection