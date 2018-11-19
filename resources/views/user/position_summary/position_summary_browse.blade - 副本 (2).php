<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<title>客户中心</title>
	<meta name="renderer" content="webkit">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" href="{{ URL::asset('js/plugins/layui/layadmin/layui/css/layui.css') }}" media="all" />
	<link rel="stylesheet" href="{{ URL::asset('js/plugins/jquery-easyui-1.5.1/themes/default/easyui.css') }}" media="all" />
	<link rel="stylesheet" href="{{ URL::asset('js/plugins/jquery-easyui-1.5.1/themes/icon.css') }}" media="all" />
	<script type="text/javascript" src="{{ URL::asset('js/jquery-1.11.3/jquery-1.11.3.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/plugins/jquery-easyui-1.5.1/jquery.easyui.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/plugins/jquery-easyui-1.5.1/locale/easyui-lang-zh_CN.js') }}"></script>
</head>
	<body>
		<h2>Basic DataGrid</h2>
		<p>The DataGrid is created from markup, no JavaScript code needed.</p>
		<div style="margin:20px 0px;"></div>
		<table id="data_list" style="width: 99%;" pagination="true"></table>
	</body>
<script type="text/javascript">
	function getData(){
		var rows = [];
		for(var i=1; i<=100; i++){
			var amount = Math.floor(Math.random()*1000);
			var price = Math.floor(Math.random()*1000);
			rows.push({
				inv: 'Inv No '+i,
				date: $.fn.datebox.defaults.formatter(new Date()),
				name: 'Name '+i,
				amount: amount,
				price: price,
				cost: amount*price,
				note: 'Note '+i
			});
		}
		
		return rows;
	}
	
	$(function () {
		var dg = $("#data_list");
		dg.datagrid({
			//url: "user/position/positionSummarySearch",//"user/position/positionSummarySearch",
			//method:"post",
			data: getData().slice(1, 20),
			fitColumns:true, // 网格宽度自适应
			resizeHandle:'right', // 调整列位置
			//toolbar:"#action-toolbar", // 表格工具栏
			striped:true, // 数据表格条纹化
			nowrap:true, // 一行里显示
			loadMsg:'正在努力加载...', // 提示消息
			//emptyMsg: '没有找到数据',
			//pagination:true, //分页//
			//pageNumber: 1,
			//pageSize: 20,
			//total: 100,
			rownumbers:true, //显示行号
			singleSelect:true, // 只允许选中一行
			queryParams:{ // 属性， 当请求远程数据时，也发送附加参数。
				qname:'aaaa',//getActionValue
			},
			columns:[[
				{field:'inv',title:'{{ trans("systemlanguage.position_summary_user_id") }}', width:120, sortable:true},
				{field:'date',title:'{{ trans("systemlanguage.position_summary_user_name") }}', width:120,sortable:true},
				{field:'name',title:'{{ trans("systemlanguage.position_summary_deposit_moneny") }}', width:120, align:'right', sortable:true},
				{field:'amount',title:'{{ trans("systemlanguage.position_summary_withdrawal_moneny") }}',width:120, align:'right',sortable:true},
				{field:'price',title:'{{ trans("systemlanguage.position_summary_comm_moneny") }}' ,width:120, align:'right', sortable:true},
				{field:'cost',title:'{{ trans("systemlanguage.position_summary_net_deposit_moneny") }}' ,width:120, align:'right', sortable:true},
				{field:'note',title:'{{ trans("systemlanguage.position_summary_profit_loss") }}' ,width:120, align:'right', sortable:true},
				
				/*{field:'id',title:'ID',width:40,sortable:true},
				 {field:'name',title:'操作名称',width:120,sortable:true},
				 {field:'action_type',title:'操作类型',width:120,sortable:true},
				 {field:'url',title:'操作url',width:120,sortable:true},
				 {field:'description',title:'简介',width:120,sortable:true}
				 {field:'user_id',title:'{{ trans("systemlanguage.position_summary_user_id") }}', width:120, sortable:true},
				 {field:'user_nam',title:'{{ trans("systemlanguage.position_summary_user_name") }}', width:120,sortable:true},
				 {field:'deposit_moneny',title:'{{ trans("systemlanguage.position_summary_deposit_moneny") }}', width:120, align:'right', sortable:true},
				 {field:'withdrawal_moneny',title:'{{ trans("systemlanguage.position_summary_withdrawal_moneny") }}',width:120, align:'right',sortable:true},
				 {field:'comm_moneny',title:'{{ trans("systemlanguage.position_summary_comm_moneny") }}' ,width:120, align:'right', sortable:true},
				 {field:'net_deposit_moneny',title:'{{ trans("systemlanguage.position_summary_net_deposit_moneny") }}' ,width:120, align:'right', sortable:true},
				 {field:'profit_loss',title:'{{ trans("systemlanguage.position_summary_profit_loss") }}' ,width:120, align:'right', sortable:true},
				 {field:'poundage_moneny',title:'{{ trans("systemlanguage.position_summary_poundage_moneny") }}' ,width:120, align:'right', sortable:true},
				 {field:'noble_metal',title:'{{ trans("systemlanguage.position_summary_noble_metal") }}' ,width:120, sortable:true},
				 {field:'foreign_exchange',title:'{{ trans("systemlanguage.position_summary_foreign_exchange") }}' ,width:120, sortable:true},
				 {field:'energy',title:'{{ trans("systemlanguage.position_summary_energy") }}' ,width:120, sortable:true},
				 {field:'index',title:'{{ trans("systemlanguage.position_summary_index") }}' ,width:120, sortable:true},
				 {field:'total_volume',title:'{{ trans("systemlanguage.position_summary_total_volume") }}' ,width:120, align:'right', sortable:true},
				 {field:'interest',title:'{{ trans("systemlanguage.position_summary_interest") }}' ,width:120, align:'right', sortable:true},*/
			]]
		});
		
		var pager = dg.datagrid('getPager'), opts = dg.datagrid('options');
		pager.pagination({
			pageNumber: 1,
			pageSize: 20,
			total: getData().length,
			layout:['list','sep','first','prev','links','next','last','sep','refresh', 'manual'],
			showPageInfo: true,
			showPageList: false,
			showRefresh: true,
			onSelectPage:function(pageNumber, pageSize){
				opts.pageNumber = pageNumber;
				opts.pageSize = pageSize;
				var start = (pageNumber - 1) * pageSize;
				var end = start + pageSize;
				dg.datagrid("loadData", getData().slice(start, end));
				pager.pagination('refresh', {
					total: getData().length,
					pageNumber: pageNumber,
				});
			}
		});
	});
	
	
</script>
</html>