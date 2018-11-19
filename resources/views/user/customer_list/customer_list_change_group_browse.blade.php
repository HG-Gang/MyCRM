@extends('user.layout.main_right')

@section('public-resources')
@endsection

@section('content')
	<form class="layui-form" action="" id="ChangeGroupListForm" style="margin-top: 8px;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">交易账户</label>
				<div class="layui-input-block">
					<input type="text" name="userId" id="userId" autocomplete="off" placeholder="请输入交易账户" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">变更类型</label>
				<div class="layui-input-inline">
					<select name="groupId" id="groupId">
						<option value="">请选择类型</option>
						<option value="1">有佣金</option>
						<option value="0">无佣金</option>
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
			<button type="button" class="layui-btn" onclick="searchResult()">查找</button>
		</div>
	</form>
	<div style="margin:20px 0px;"></div>
	<table id="data_list" style="width: 99%;" pagination="true" title="客户变更列表"></table>
@endsection

@section('custom-resources')
	<script type="text/javascript">
		/*$(function () {
		 autoSearchExtraParam();
		 createTable();
		 });*/
		
		function searchResult() {
			createTable();
		}
		
		function dataGridConfig() {
			var config = {};
			config.DataColumns = [[
				{field:'trans_uid' ,title:'{{ trans ('systemlanguage.direct_customer_change_uid') }}', width:100},
				{field:'trans_type_gid' ,title:'{{ trans ('systemlanguage.direct_customer_change_type') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if(value == '1') {
						return "有佣金";
					} else if(value == '0') {
						return "无佣金";
					} else {
						return "未知";
					}
				}},
				{field:'trans_apply_status' ,title:'{{ trans ('systemlanguage.direct_customer_change_status') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if(value == '0') {
						return "<span style='color: #B8860B;'>" + '等待变更' + "</span>";
					} else if(value == '1') {
						return "<span style='color: green;'>" + '已确认变更' + "</span>";
					} else if(value == '-1') {
						return "<span style='color: red;'>" + '变更失败' + "</span>";
					} else {
						return "<span style='color: #000000;'>" + '其他' + "</span>";
					}
				}},
				{field:'trans_apply_reason' ,title:'{{ trans ('systemlanguage.direct_customer_change_reason') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if(rowData.trans_apply_status == '0') {
						return "<span style='color: #000000;'>" + '======' + "</span>";
					} else if(rowData.trans_apply_status == '1') {
						return "<span style='color: #000000;'>" + '======' + "</span>";
					} else if(rowData.trans_apply_status == '-1') {
						return "<span style='color: #000000;'>" + value + "</span>";
					}
				}},
				{field:'rec_crt_date' ,title:'{{ trans ('systemlanguage.direct_customer_change_datetime') }}', width:100, align:'center'},
			]];
			
			return config;
		}
		
		function createTable() {
			var config = dataGridConfig(), pagerData;
			pagerData = new $.WidgetPage({
				//title: ajaxGetTableTitle(),
				reqUrl: '/user/cust/directCustChangeListSearch',
				tableId: 'data_list',
				formId: 'ChangeGroupListForm',
				method: 'post',
				columns : config.DataColumns,
				//buttons: config.Buttons,
				formToken: "{{ csrf_token() }}",
				idField: 'trans_uid',
				extraParam: subPuid,
				rownumbers: true,
				singleSelect: true,
				showFooter: true,
			});
			
			pagerData.GridInit();
		}
		
		//双击更改直属客户组别信息
		function DbClickEditAccountInfo(rowIndex, rowData) {
			console.log("没有可以操作的数据....");
		}
		
		function autoSearchExtraParam() {
			subPuid.searchtype = 'atuoSearch';
		}
	</script>
@endsection