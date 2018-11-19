@extends('user.layout.main_right')

@section('content')
	<form class="layui-form" action="" id="AdminCustChangeListForm" style="margin-top: 8px;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">变更账户</label>
				<div class="layui-input-block">
					<input type="text" name="userId" id="userId" autocomplete="off" placeholder="请输入变更账户" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">变更状态</label>
				<div class="layui-input-inline">
					<select id="trans_apply_status" name="trans_apply_status">
						<option value="">请选择变更状态</option>
						<option value="0" selected="selected">等待变更</option>
						<option value="1">确认变更</option>
						<option value="-1">变更失败</option>
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
					<input type="text" name="trans_apply_reason" id="trans_apply_reason" autocomplete="off" placeholder="请输入拒绝变更原因" class="layui-input" style="width: 200px;">
					<input type="hidden" id="not_pass_id" name="not_pass_id" readonly="readonly">
				</div>
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-input-block">
				<button type="button" class="layui-btn" onclick="account_not_pass_reason()">提交</button>
			</div>
		</div>
	</from>
	<div style="margin:20px 0px;"></div>
	<table id="data_list" style="width: 99%;" pagination="true" title="客户变更列表"></table>
@endsection

@section('custom-resources')
	<script type="text/javascript">
		function dataGridConfig() {
			var config = {};
			config.DataColumns = [[
				{field:'transUid' ,title:'{{ trans ('systemlanguage.account_change_id') }}', width:100, align:'center',},
				{field:'transTypeGid' ,title:'{{ trans ('systemlanguage.account_change_type') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					var comm_type = "";
					if (value == "1") {
						comm_type = "<span data-transTypeGid='"+ value +"' data-trans_type_name='" + rowData.transTypeName +"'>"+ '有佣金' +"</span>"
					} else if (value == "0") {
						comm_type = "<span data-transTypeGid='"+ value +"' data-trans_type_name='" + rowData.transTypeName +"'>"+ '无佣金' +"</span>"
					} else {
						comm_type = "未知";
					}
					
					return comm_type;
				}},
				{field:'bal' ,title:'{{ trans ('systemlanguage.account_change_bal') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if (value < 0) {
						return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
					} else {
						return parseFloatToFixed(value);
					}
				}},
				{field:'vol' ,title:'{{ trans ('systemlanguage.account_change_vol') }}', width:100, align:'center',},
				{field:'transApplyUid' ,title:'{{ trans ('systemlanguage.account_change_apply_id') }}', width:100, align:'center',},
				{field:'transApplyUname',title:'{{ trans ('systemlanguage.account_change_name') }}', width:100, align:'center'},
				{field:'transApplyStatus' ,title:'{{ trans ('systemlanguage.account_change_status') }}', width:100, align:'center',  formatter: function (value, rowData, rowIndex) {
				    var str = "";
					
					if(value == '0') {
						str = "<span style='color: #B8860B;'>"+ '等待变更' +"</span>";
					} else if (value == '1') {
						str = "<span style='color: green;'>"+ '已确认变更' +"</span>";
					} else if (value == '-1') {
						str = "<span style='color: red;'>"+ '变更失败' +"</span>";
					} else {
						str = "<span style='color: #000000;'>"+ '变更失败' +"</span>";
					}
					
					return str;
				}},
				{field:'transApplyReason' ,title:'{{ trans ('systemlanguage.account_change_reason') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					var str = "";
					
					if(rowData.transApplyStatus== '0') {
						str = "<span style='color: #000000;'>"+ '------' +"</span>";
					} else if (rowData.transApplyStatus== '1') {
						str = "<span style='color: #000000;'>"+ '------' +"</span>";
					} else if (rowData.transApplyStatus== '-1') {
						str = "<span style='color: red;'>"+ value +"</span>";
					} else {
						str = "<span style='color: #000000;'>"+ '------' +"</span>";
					}
					
					return str;
				}},
				{field:'rec_crt_date' ,title:'{{ trans ('systemlanguage.account_change_datetime') }}' ,width:100 ,align:'center',},
				{field:'userOptions' ,title:'{{ trans ('systemlanguage.account_change_action') }}' ,width:100 ,align:'center', formatter: function (value, rowData, rowIndex) {
					if (/*rowData.transApplyStatus == "0"*/true) {
						return "<span style='color: blue; cursor: pointer;' onclick='apply_change_pass("+ rowData.transUid +", "+ rowData.transTypeGid +")'>"+ '确认变更' +"</span>" +
							"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
							+ "<span style='color: blue; cursor: pointer;' onclick='not_pass_box("+ rowData.transUid +")'>"+ '拒绝变更' +"</span>";
					} else {
						return "-------------------";
					}
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
				reqUrl: route_prefix() + '/cust/custChangeListSearch',
				tableId: 'data_list',
				formId: 'AdminCustChangeListForm',
				method: 'post',
				columns : config.DataColumns,
				formToken: "{{ csrf_token() }}",
				idField: 'transId',
				extraParam: subPuid,
				rownumbers: true,
				singleSelect: true,
				showFooter: true,
			});
			
			pagerData.GridInit();
		}
		
		function not_pass_box(uid) {
			$("#not_pass_id").val(uid);
			layer.open({
				title: "取消变更",
				type: 1,
				area: ["360px", "180px"],
				skin: 'layui-layer-molv',
				shade: [0.6, '#393D49'],
				move: false,
				content: $("#not_pass"),
			});
		}
		
		function account_not_pass_reason() {
			var trans_apply_reason = $("#trans_apply_reason").val();
			var not_pass_id = $("#not_pass_id").val();
			var _token = "{{ csrf_token() }}";
			if( trans_apply_reason == "" ) {
				layer.msg("请输入取消变更原因");
				return;
			} else {
				var index1 = openLoadShade();
				$.ajax({
					url: route_prefix() + '/cust/cust_apply_nopass',
					data: {
						token: _token,
						uid: not_pass_id,
						trans_apply_reason: trans_apply_reason,
					},
					dateType: "JSON",
					type: "POST",
					async: false,
					success: function (data) {
						closeLoadShade(index1);
						if (data.msg == "SUCCESS") {
							layer.msg("操作成功!", {
								//time: 20000, //20s后自动关闭
								btn: ['知道了'],
								yes: function (index, layero) {
									layer.closeAll();
									createTable();
								}
							});
						} else if (data.msg == "FAIL") {
							layer.msg("操作失败,请重新操作!", {
								//time: 20000, //20s后自动关闭
								btn: ['知道了'],
								yes: function (index, layero) {
									layer.closeAll();
									createTable();
							}
							});
						}
					},
					error: function (data) {
						closeLoadShade(index1);
						alert('系统错误，请刷新重新操作')
					}
				});
			}
		}
		
		function apply_change_pass(uid, op) {
			var op_str="";
			if(op == 1) {
			    op_str = "有佣金";
			} else if(op == 0) {
			    op_str = "无佣金";
			} else {
			    op_str = "未知";
			}
			
			var str = "<div>";
			str += "<p>" + "确认将" + "【" + "<span style='color: green; font-weight: bold;'>" + uid + "</span>" + "】" + "变更为" + "【" + "<span style='color: red; font-weight: bold;'>" + op_str + "</span>" + "】" + "吗?";
			str += "</p></div>";
			var _token = "{{ csrf_token() }}";
			layer.confirm(str, {icon: 3, title:'操作确认提示'}, function(index) {
				var index1 = openLoadShade();
				$.ajax({
					url: route_prefix() + '/cust/cust_apply_pass',
					data: {
						token: _token,
						uid: uid,
					},
					dateType: "JSON",
					type: "POST",
					async: false,
					success: function (data) {
						closeLoadShade(index1);
						if (data.msg == "SUCCESS") {
							layer.msg("操作成功!", {
								//time: 20000, //20s后自动关闭
								btn: ['知道了'],
								yes: function (index, layero) {
									layer.closeAll();
									createTable();
								}
							});
							return;
						} else if (data.msg == "FAIL") {
							layer.msg("操作失败,请重新操作!", {
								//time: 20000, //20s后自动关闭
								btn: ['知道了'],
								yes: function (index, layero) {
									layer.closeAll();
									createTable();
								}
							});
						}
					},
					error: function (data) {
						closeLoadShade(index1);
						alert('系统错误，请刷新重新操作')
					}
				});
			});
		}
	</script>
@endsection