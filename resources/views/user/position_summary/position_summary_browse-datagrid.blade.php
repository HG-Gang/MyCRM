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
	<script type="text/javascript" src="{{ URL::asset('js/plugins/layui/layadmin/layui/layui.all.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/plugins/jquery-easyui-1.5.1/jquery.easyui.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/formevent/esayui-datagrid-pagination.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/plugins/jquery-easyui-1.5.1/locale/easyui-lang-zh_CN.js') }}"></script>
	<style>
		/*.datagrid-view {
			min-height: 60px;
		}*/
	</style>
</head>
	<body>
		<h2 onclick="search()">Basic DataGrid</h2>
		<p>The DataGrid is created from markup, no JavaScript code needed.</p>
		<div style="margin:20px 0px;"></div>
		<table id="data_list" style="width: 99%;" pagination="true"></table>
	</body>
<script type="text/javascript">
	function dataGridConfig() {
		var DataColumns, pagerData, Buttons;
		DataColumns = [[
			{field:'ck', checkbox: true},
			{field:'user_id',title:'user_id',width:100, formatter: function (value, rowData, rowIndex) {
				if (value) {
					return "<font  onclick=accountInfo('" + value + "'); color='blue' title='查看详情'>" + value + "</font>";
				}
			}},
			{field:'user_name',title:'user_name',width:100, halign:'center'},
			{field:'user_money',title:'user_money',width:100,align:'right', formatter: function (value, rowData, rowIndex) {
				if(value < 0) {
					return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
				}
				return parseFloatToFixed(value);
			}},
			{field:'email',title:'email',width:100,align:'right'},
			{field:'useredit',title:'编辑',width:50, align:'right', formatter: function (value, rowData, rowIndex) {
				return '<a href="javascript:;" onclick="aEditAccountInfo('+ rowIndex +', '+ rowData.user_id +')" class="l-btn l-btn-small l-btn-plain" group="" id="" style="color: blue;">' +
							'<span class="l-btn-left l-btn-icon-left">' +
								'<span class="l-btn-text">编辑</span>' +
								'<span class="l-btn-icon icon-edit">&nbsp;</span>' +
							'</span>'+
						'</a>';
			}},
		]];
		
		Buttons = [{
			text: '导出Excel',
			iconCls:'icon-export',
			handler:function(){
				//getcheckedData(rowIndex, rowData);
				var ids = [];
				//获取选中的行
				//var row = $('#data_list').datagrid('getSelected');
				//获取所有选中的行
				var rows = $('#data_list').datagrid('getSelections');
				if (rows.length == 0) {
					$.messager.alert('系统提示','请先勾选需要导出的数据','warning');
					layer.msg('请先勾选需要导出的数据');
					return;
				} else {
					console.log(rows);
					for(var i=0; i<rows.length; i++){
						ids.push(rows[i].user_id);
					}
					console.log(ids.join(','));
				}
			}
		}];
		
		pagerData = new $.WidgetPage({
			reqUrl: '/user/position/positionSummarySearch',
			tableId: 'data_list',
			method: 'post',
			columns : DataColumns,
			buttons: Buttons,
			formToken: "{{ csrf_token() }}",
			idField: 'user_id',
			rownumbers: true,
			singleSelect: true,
			showFooter: true,
		});
		
		pagerData.GridInit();
	}
	
	function search() {
		dataGridConfig();
	}
	
	function accountInfo(rowIndex, rowData) {
	
	}
	
	function DbClickEditAccountInfo(rowIndex, rowData) {
		alert(rowIndex);
		layer.msg('仓位总结function： ' + rowData.user_name);
		/*layer.open({
			 type: 1,
			 title: '提示信息',
			 skin: 'layui-layer-molv',
			 closeBtn: 0,
			 area: ['450px', '240px'],
			 btn: ["知道了"],
			 btnAlign: 'c',
			 content: accountInfo(),
			 yes: function (index, layero) {
				if (errText == 'SUCCESS') {
					top.location = '/user/index';
				} else if (errText == 'FAIL') {
					top.location = '/';
		        }
			 },
		 });*/
	}
	
	function aEditAccountInfo(index, uid) {
		alert('index:' + index + 'Uid: ' + uid);
	}
	
	//合并单元格
	function mergesCellByField(rowData) {
		var merges = [{
			field: 'user_money',
			index:3,
			colspan:2,
		}, {
			field: 'user_id',
			index: 18,
			rowspan: 3,
		}];
		
		for(var i=0; i<merges.length; i++) {
			//data_list
			$("#data_list").datagrid('mergeCells',{
				index: merges[i].index,
				field: merges[i].field,
				rowspan: merges[i].rowspan,
				colspan: merges[i].colspan,
			});
		}
	}
</script>
</html>