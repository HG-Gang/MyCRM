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
</head>
	<body>
		<h2>Basic DataGrid</h2>
		<p>The DataGrid is created from markup, no JavaScript code needed.</p>
		<div style="margin:20px 0px;"></div>
		<table id="position_summary_data_list" title="仓位总结" style="width: 99%;">
		</table>
			{{--<div id="position_summary_page" class="easyui-pagination" style="background:#efefef;border:1px solid #ccc;"></div>--}}
		<div class="easyui-panel">
			<div id="position_summary_page" class="easyui-pagination"></div>
		</div>
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
	
	/*$(function(){
		$('#position_summary_data_list').datagrid({data:getData()}).datagrid('clientPaging');
	});*/
	$(function () {
		var dg = $("#position_summary_data_list");
		dg.datagrid({
			//url: '/GetAllActions', //"user/position/positionSummarySearch",
			//method:"get",
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
		
		$.fn.datagrid('getPager').pagination({
			pageNumber: 1,
			pageSize: 20,
			total: 100,
			layout:['list','sep','first','prev','links','next','last','sep','refresh'],
			//displayMsg: '共100条数据',
			showPageList: false,
			showRefresh: false,
		});
		/*var dgp = $("#position_summary_page1");
		dgp.datagrid('getPager').pagination({
			//pageNumber: 1,
			//pageSize: 20,
			total: 100,
			layout:['list','sep','first','prev','links','next','last','sep','refresh'],
			//displayMsg: '共100条数据',
			showPageList: false,
			showRefresh: false,
			onSelectPage:function(pageNumber, pageSize){
				//$(this).pagination('loading');
				alert('onSelectPage pageNumber:' + pageNumber + ',pageSize:' + pageSize);
				var start = (pageNo - 1) * pageSize;
				var end = start + pageSize;
				/!*dg.datagrid("loadData", data.slice(start, end));
				//getData(pageNumber, pageSize);
				dg.pagination('refresh', {
					total:getData().length,
					pageNumber: pageNumber,
					pageSize: pageSize,
				});*!/
				
				//dg.datagrid('loadData', getData());
				//alert('pageNumber:'+pageNumber+',pageSize:'+pageSize);
				//$(this).pagination('loaded');
			}
		
		
		});*/
	});
	
	/*function pagerFilter(data) {
		/!*if (typeof data.length == 'number' && typeof data.splice == 'function'){// is array
			data = {
				total: data.length,
				rows: data
			}
		}*!/
		
		var dg = $(this), opts = dg.datagrid('options'),
			pager = dg.datagrid('getPager');
		pager.pagination({
			pageNumber:1,
			pageSize:20,
			total:200,
			layout:['list','sep','first','prev','links','next','last','sep','refresh'],
			showPageList: false,
			showRefresh: false,
			onSelectPage: function (pageNum, pageSize) {
				opts.pageNumber = pageNum;
				opts.pageSize = pageSize;
				pager.pagination('refresh', {
					pageNumber:pageNum,
					pageSize: pageSize
				});
				dg.datagrid('loadData', data)
			}
		});
		
		if (!data.originalRows){
			data.originalRows = (data.rows);
		}
		var start = (opts.pageNumber - 1) * parseInt(opts.pageSize);
		var end = start + parseInt(opts.pageSize);
		data.rows = (data.originalRows.slice(start, end))
		
		return data;
	}
	
	$(function () {
		$("#dg").datagrid({loadFilter: pagerFilter()}).datagrid('loadData', getData())
	});*/
	
	/*function queryActions() {
		$("#action-dg").datagrid("load");
	}
	function getActionValue(){
		return $('#action-qname').val();
	}
	// 打开新增窗口
	function newActionItem(){
		$('#action-dlg').dialog('open').dialog('center').dialog('setTitle','新增操作');
		// 清空显示的错误
		$('#action-messagebox').html("");
		$('#action-addForm').form('reset');
	}
	// 打开编辑窗口 并设置值
	function editActionItem(){
		$('#action-e-messagebox').html("");
		var row = $('#action-dg').datagrid('getSelected');
		if (row){
			//重置url 传action的id
			editActionUrl = "/action/update/"+row.id;
			$("#action-e-name").textbox('setValue',row.name);
			$("#action-e-url").textbox('setValue',row.url);
			$("#action-e-action_type").textbox('setValue',row.action_type);
			$("#action-edit-cc-parent_id").combotree('setValue',row.parent_id);
			$("#action-e-description").textbox('setValue',row.description);
			// 请求角色的具有的权限,成功后显示在权限树中
			$.ajax({
				url:'/GetActionPermissions/'+row.id,
				data:{
					// laravel 框架,post提交时,防止跨站请求伪造(CSRF)
					"_token":$('meta[name="csrf-token"]').attr('content'),
					// 返回错误的方式
					'back_error':"json"
				},
				// 此处设置为接收json格式,success的方法中就不用eval进行转换json
				dataType: "json",
				method:'post',
				error:function(response){
					var data = eval('('+response.responseText+')');
					$.messager.show({
						title: '失败消息',
						msg: data.msg
					});
				},
				success:function(data){
					if (data.success){
						var permission = data.data;
						// 设置权限树的默认
						$('#action-edit-cc').combotree('setValue',permission.id);
					} else {
						$.messager.show({
							title: '失败消息',
							msg: data.msg
						});
					}
				}
			});
			// 获取操作树
			
			$('#action-edit-dlg').dialog('open').dialog('center').dialog('setTitle','更新操作');
		}
	}
	// 提交更新
	function updateActionItem(){
		$('#action-editForm').form('submit',{
			url:editActionUrl,
			onSubmit:function(){
				return $(this).form('enableValidation').form('validate');
			},
			queryParams:{
				// laravel 框架,post提交时,防止跨站请求伪造(CSRF)
				"_token":$('meta[name="csrf-token"]').attr('content'),
				// 返回错误的方式
				'back_error':"json"
			},
			success:function (result){
				var data = eval('('+result+')');
				if(data.success){
					$('#action-dlg').dialog('close');
					$.messager.show({    // show error message
						title: '成功消息',
						msg: data.msg
					});
					//新增成功 刷新grid
					queryActions();
					$('#action-edit-dlg').dialog('close');
				}else{
					$('#action-e-messagebox').html(data.msg);
				}
			},
			error:function () {
				$.messager.alert('警告','系统出错!请刷新界面重试!','warning');
				window.location.reload();
			}
		});
	}
	// 提交新增项目
	function saveActionItem(){
	 $('#action-addForm').form('submit',{
	 onSubmit:function(){
	 return $(this).form('enableValidation').form('validate');
	 },
	 queryParams:{
	 // laravel 框架,post提交时,防止跨站请求伪造(CSRF)
	 "_token":$('meta[name="csrf-token"]').attr('content'),
	 // 返回错误的方式
	 'back_error':"json"
	 },
	 success:function (result){
	 var data = eval('('+result+')');
	 if(data.success){
	 $('#action-dlg').dialog('close');
	 $.messager.show({    // show error message
	 title: '成功消息',
	 msg: data.msg
	 });
	 // 新增成功 刷新grid
	 queryActions();
	 $('#action-dlg').dialog('close');
	 // 新增菜单之后之后,刷新菜单树menu-cc
	 $('#action-cc').combotree('reload');
	 }else{
	 $('#action-messagebox').html(data.msg);
	 }
	 },
	 error:function () {
	 $.messager.alert('警告','系统出错!请刷新界面重试!','warning');
	 window.location.reload();
	 }
	 });
	 }
	// 删除方法
	function deleteActionItem() {
		var row = $('#action-dg').datagrid('getSelected');
		if (row){
			$.messager.confirm('警告','你确定要删除操作:' + row.name + '?',function(r){
				if (r){
					$.ajax({
						url:'/action/delete/'+row.id,
						data:{
							// laravel 框架,post提交时,防止跨站请求伪造(CSRF)
							"_token":$('meta[name="csrf-token"]').attr('content'),
							// 返回错误的方式
							'back_error':"json"
						},
						dataType: "json",
						method:'post',
						error:function(response){
							var data = eval('('+response.responseText+')');
							$.messager.show({
								title: '失败消息',
								msg: data.msg
							});
						},
						success:function(data){
							if (data.success){
								$('#action-dg').datagrid('reload');
								$.messager.show({
									title: '成功消息',
									msg: data.msg
								});
							} else {
								$.messager.show({
									title: '失败消息',
									msg: data.msg
								});
							}
						}
					});
				}
			});
		}
	}*/
</script>
</html>