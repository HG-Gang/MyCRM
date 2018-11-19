@extends('user.layout.main_right')

@section('public-resources')

@endsection

@section('content')
	<form class="layui-form" action="" id="AuthUserForm" style="margin-top: 8px;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">交易账户</label>
				<div class="layui-input-block">
					<input type="text" name="userId" id="userId" autocomplete="off" placeholder="请输入交易账户" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">账户姓名</label>
				<div class="layui-input-block">
					<input type="text" name="username" id="username" autocomplete="off" placeholder="请输入订单号" class="layui-input" style="width: 200px;">
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
			<button type="button" class="layui-btn" onclick="createTable()">查找</button>
		</div>
	</form>
	<div style="margin:20px 0px;"></div>
	<table id="data_list" style="width: 99%;" pagination="true" title="待审核列表"></table>
@endsection

@section('custom-resources')
	<script type="text/javascript">
		function dataGridConfig() {
			var config = {};
			config.DataColumns = [
				[
					{field:'user_type' ,title:'{{ trans ('systemlanguageadmin.user_examine_usertype') }}', width:100, align:'center', rowspan:2, formatter: function (value, rowData, rowIndex) {
						if (rowData.user_id < 1000001) {
							return "<font color='purple'>" + '代理商' + "</font>";
						} else {
							return "普通客户";
						}
					}},
					{field:'user_id' ,title:'{{ trans ('systemlanguageadmin.user_examine_userid') }}', width:80, align:'center', rowspan:2},
					{field:'user_name' ,title:'{{ trans ('systemlanguageadmin.user_examine_username') }}', width:80, align:'center', rowspan:2},
					{title:'{{ trans ('systemlanguageadmin.user_examine_authtype') }}',width:120, colspan: 2},
					{field:'parent_id' ,title:'{{ trans ('systemlanguageadmin.user_examine_parentid') }}', width:80, align:'center', rowspan:2},
					{field:'rec_crt_date' ,title:'{{ trans ('systemlanguageadmin.user_examine_rec_crt_date') }}', width:80, align:'center', rowspan:2},
					{field:'authfun' ,title:'{{ trans ('systemlanguageadmin.user_examine_auth_shenhe') }}', width:60, align:'center', rowspan:2, formatter: function (value, rowData, rowIndex) {
						if (rowData.user_id && (rowData.IDcard_status == '1' || rowData.bank_status == '1') ) {
							return '<a href="javascript:;" onclick="user_auth_shenhe('+ rowData.user_id +')" class="l-btn l-btn-small l-btn-plain" style="color: blue;">' +
								'<span class="l-btn-left l-btn-icon-left">' +
								'<span class="l-btn-text">审核TA</span>' +
								'<span class="l-btn-icon icon-search">&nbsp;</span>' +
								'</span>'+
								'</a>';
						} else {
							return "======";
						}
					}},
				],
				[
					{field:'IDcard_status' ,title:'{{ trans ('systemlanguageadmin.user_examine_authidcard') }}', width:60, align:'center', formatter: function (value, rowData, rowIndex) {
						if (value == '0') {
							return "<font color='madiumaquamarine'>" + '等待上传' + "</font>";
						} else if (value == '1') {
							return "<font color='mediumpurple'>" + '等待审核' + "</font>";
						} else if (value == '4') {
							return "<font color='mediumorchid'>" + '审核不通过' + "</font>";
						} else if (value == '2') {
							return "<font color='darkviolet'>" + '审核通过' + "</font>";
						}
					}},
					{field:'bank_status' ,title:'{{ trans ('systemlanguageadmin.user_examine_authbank') }}', width:60, align:'center', formatter: function (value, rowData, rowIndex) {
						if (value == '0') {
							return "<font color='madiumaquamarine'>" + '等待上传' + "</font>";
						} else if (value == '1') {
							return "<font color='mediumpurple'>" + '等待审核' + "</font>";
						} else if (value == '4') {
							return "<font color='mediumorchid'>" + '审核不通过' + "</font>";
						} else if (value == '2') {
							return "<font color='darkviolet'>" + '审核通过' + "</font>";
						}
					}},
				],
			];
			
			return config;
		}
		
		function DbClickEditAccountInfo(rowIndex, rowData) {
			console.log(rowData.user_name)
		}
		
		function createTable() {
			var config = dataGridConfig(), pagerData;
			pagerData = new $.WidgetPage({
				reqUrl: route_prefix() + '/auth/userExaminSearch',
				tableId: 'data_list',
				formId: 'AuthUserForm',
				method: 'post',
				columns : config.DataColumns,
				formToken: "{{ csrf_token() }}",
				idField: 'user_id',
				extraParam: subPuid,
				rownumbers: true,
				mergeHeader: true, //用于复杂表头，在无数据时重新设定datagrid-view 样式
				singleSelect: true,
				showFooter: true,
			});
			
			pagerData.GridInit();
		}
		
		function user_auth_shenhe(uid) {
			var index = layer.open({
				type: 2,
				title: '审核 ' + uid +' 的账户信息',
				skin: 'layui-layer-molv',
				move:false,
                area: ['700px', '680px'],
				content: route_prefix() + '/auth/user_examine/detail/'+ uid,
			});
		}
	</script>
@endsection