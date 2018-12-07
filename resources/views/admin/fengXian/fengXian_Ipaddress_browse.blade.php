@extends('user.layout.main_right')

@section('public-resources')
	<script type="text/javascript" src="{{ URL::asset('js/plugins/jquery-easyui-1.5.1/datagrid-detailview.js') }}?ver={{ resource_version_number() }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/plugins/jquery-easyui-1.5.1/datagrid-groupview.js') }}?ver={{ resource_version_number() }}"></script>
@endsection

@section('content')
	<form class="layui-form" action="" id="IPfengXianForm" style="margin-top: 8px;">
		{{--<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">交易账户</label>
				<div class="layui-input-block">
					<input type="text" name="userId" id="userId" autocomplete="off" placeholder="请输入交易账户" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">登录时间</label>
				<div class="layui-input-inline" style="width: 200px;">
					<input type="text" name="startdate" id="startdate" placeholder="请输入开始时间" autocomplete="off" class="layui-input">
				</div>
				<div class="layui-form-mid">-</div>
				<div class="layui-input-inline" style="width: 200px;">
					<input type="text" name="enddate" id="enddate" placeholder="请输入结束时间" autocomplete="off" class="layui-input">
				</div>
			</div>--}}
		<button type="button" class="layui-btn" onclick="searchRightsSum()">查找</button>
		</div>
	</form>
	<div style="margin:20px 0px;"></div>
	<div id="real" style="margin-left: 20px;"></div>
	<table id="data_list" style="width: 99%;" pagination="true" title="IP风险列表"></table>
@section('custom-resources')
	<script type="text/javascript">
		function searchRightsSum() {
			createTable1();
		}
		
		function dataGridConfig() {
			var config = {};
			config.DataColumns = [[
				{field:'login_id' ,title:'{{ trans ('systemlanguage.fengXian_loginId') }}', width:100, align:'center',},
				{field:'login_name' ,title:'{{ trans ('systemlanguage.fengXian_loginName') }}', width:100, align:'center',},
				{field:'login_ip' ,title:'{{ trans ('systemlanguage.fengXian_loginIp') }}', width:100, align:'center',},
				{field:'login_id_desc' ,title:'{{ trans ('systemlanguage.fengXian_loginIddesc') }}', width:100, align:'center',},
				{field:'login_count' ,title:'{{ trans ('systemlanguage.fengXian_loginIdCount') }}', width:100, align:'center',},
			]];
			
			config.DataColumnsdetail = [[
				{field:'login_id' ,title:'{{ trans ('systemlanguage.fengXian_loginId') }}', width:100, align:'center',},
				{field:'login_ip' ,title:'{{ trans ('systemlanguage.fengXian_loginIp') }}', width:100, align:'center',},
				{field:'login_id_desc' ,title:'{{ trans ('systemlanguage.fengXian_loginIddesc') }}', width:100, align:'center',},
				{field:'login_date' ,title:'{{ trans ('systemlanguage.fengXian_loginDate') }}', width:100, align:'center',},
			]];
			
			return config;
		}
		
		function DbClickEditAccountInfo(rowIndex, rowData) {
			console.log("没有可查看的信息");
		}
		
		function createTable1() {
			var config = dataGridConfig();
			
			$('#data_list').datagrid({
				url: route_prefix() + '/fengXian/IpaddressSearch',
				method: 'post',
				loadMsg:'正在努力加载...', // 提示消息
				emptyMsg: '<span style="color: red; font-size: 15px; font-weight: 600; line-height: 36px;">没有找到数据</span>',
				fitColumns:true, // 网格宽度自适应
				striped: true, // 数据表格条纹化
				nowrap: true, // 一行里显示
				showFooter: false,
				rownumbers: true,
				singleSelect: true,
				pageSize: 20,
				pageNumber: 1,
				pageList: [20, 20 * 2],
				queryParams: getQueryParam(),
				loadFilter:pagerFilter,
				columns: config.DataColumns,
				view: detailview,
				detailFormatter:function(index,row){
					return '<div style="padding:2px;position:relative;"><table class="'+ row.sys_id +'-ddv"></table></div>';
				},
				onDblClickRow: function (rowIndex, rowData) {
					console.log("没有可查看的信息");
				},
				onLoadSuccess: function (rowData) {
					if (rowData.rows.length == 0) {
						$('#data_list').closest('div.datagrid-wrap').find('div.pagination').hide();
						$('#data_list').closest('div.datagrid-wrap').find('div.datagrid-footer').hide();
					} else {
						$('#data_list').closest('div.datagrid-wrap').find('div.pagination').css('display', 'block');
						$('#data_list').closest('div.datagrid-wrap').find('div.datagrid-footer').css('display', 'block');
					}
					var fatherinternalTimer = '';
					clearTimeout(fatherinternalTimer);
					fatherinternalTimer =
							setInterval(function () {
								$.each($('#data_list').datagrid('getRows'), function (i, row) {
									$('#data_list').datagrid('fixRowHeight', i);
									$('#data_list').datagrid('fixDetailRowHeight', i);
								});
							}, 10);
				},
				onExpandRow: function(index,row){
					var ddv = $(this).datagrid('getRowDetail',index).find('table.' + row.sys_id + '-ddv');
					ddv.datagrid({
						url:route_prefix() + '/fengXian/IpaddressDeatail/' + row.login_ip,
						method: 'get',
						fitColumns:true, // 网格宽度自适应
						//toolbar: toolbar, // 表格工具栏
						striped: true, // 数据表格条纹化
						nowrap: true, // 一行里显示
						showFooter: false,
						rownumbers: true,
						loadMsg:'正在努力加载...', // 提示消息
						height:'auto',
						idField:'login_id',
						showFooter: true,
						singleSelect: true,
						showGroup: true,
						groupField: 'login_id',
						view: groupview,
						columns:config.DataColumnsdetail,
						groupFormatter: function (value, rows) {
							console.log("==========");
							console.log(rows);
							var rtnStr = "";
							rtnStr += "<span style='font-size: 12px;'>";
							rtnStr += "<span>" + value + "</span>";
							rtnStr += "&nbsp;&nbsp;" + "<span>" + '{{ trans ('systemlanguage.fengXian_loginName') }}' + ': ' + "<span style='text-decoration: underline; color: #0000cc'>" + rows[0].login_name + "</span></span>";
							rtnStr += "&nbsp;&nbsp;" + "<span>" + '{{ trans ('systemlanguage.fengXian_loginpid') }}' + ': ' + "<span style='text-decoration: underline; color: #0000cc'>" + rows[0].parent_id + "</span></span>";
							rtnStr += "&nbsp;&nbsp;" + "<span>" + '{{ trans ('systemlanguage.fengXian_logincrt_date') }}' + ': ' + "<span style='text-decoration: underline; color: #0000cc'>" + rows[0].rec_crt_date +  "</span></span>";
							rtnStr += "&nbsp;&nbsp;" + "<span>" + '{{ trans ('systemlanguage.fengXian_loginclose') }}' + ': ' + "<span style='text-decoration: underline; color: #0000cc'>" + rows[0].close +  "</span></span>";
							rtnStr += "&nbsp;&nbsp;" + "<span>" + '{{ trans ('systemlanguage.fengXian_loginopen') }}' + ': ' + "<span style='text-decoration: underline; color: #0000cc'>" + rows[0].open +  "</span></span>";
							rtnStr += "&nbsp;&nbsp;" + "<span>" + '{{ trans ('systemlanguage.fengXian_loginamount_rj') }}' + ': ' + "<span style='text-decoration: underline; color: #0000cc'>" + rows[0].amount_rj +  "</span></span>";
							rtnStr += "&nbsp;&nbsp;" + "<span>" + '{{ trans ('systemlanguage.fengXian_loginamount_cj') }}' + ': ' + "<span style='text-decoration: underline; color: #0000cc'>" + rows[0].amount_cj +  "</span></span>";
							rtnStr += "&nbsp;&nbsp;" + "<span>" +' 共 '+ rows.length + ' 条记录' + "</span></span>";
							
							return rtnStr;
						},
						onResize: function () {    //事件会在窗口或框架被调整大小时发生
							$.each($('.' + row.sys_id + '-ddv').datagrid('getRows'), function (i, rows) {
								$('.' + rows.sys_id + '-ddv').datagrid('fixRowHeight', i);
							});
							$.each($('.' + row.sys_id + '-ddv').datagrid('getRows'), function (i, rows) {
								$('.' + rows.sys_id + '-ddv').datagrid('fixRowHeight', i);
								$('.' + rows.sys_id + '-ddv').datagrid('fixDetailRowHeight', i);
							});
							//父表格改变大小
							$('.' + row.sys_id + '-ddv').datagrid('fixDetailRowHeight', row.sys_id);
							//爷爷表格改变大小
							/*$('#data_list').datagrid('fixDetailRowHeight', index);*/
						},
						onLoadSuccess: function () {
							setTimeout(function(){
							 //在加载爷爷列表明细（即：父列表）成功时，获取此时整个列表的高度，使其适应变化后的高度，此时的索引
							 $('#data_list').datagrid('fixDetailRowHeight',index);
							 //防止出现滑动条
							 $('#data_list').datagrid('fixRowHeight', index);
							 },0);
							
							$.each($('.' + row.sys_id + '-ddv').datagrid('getRows'), function (i, rows) {
								$('.' + rows.sys_id + '-ddv').datagrid('fixRowHeight', i);
							});
							/*	$.each($('#data_list').datagrid('getRows'), function (i, row) {
							 $('#data_list').datagrid('fixRowHeight', i);
							 });*/
							$('.' + row.sys_id + '-ddv').datagrid('fixDetailRowHeight', row.sys_id);
							/*$('#data_list').datagrid('fixDetailRowHeight', index);*/
						},
					});
					$('#data_list').datagrid('fixDetailRowHeight',index);
				}
			});
		}
		
		function pagerFilter(data){
			if (typeof data.length == 'number' && typeof data.splice == 'function'){
				data = {
					total: data.length,
					rows: data
				}
			}
			
			var dg = $('#data_list');
			var opts = dg.datagrid('options');
			var pager = dg.datagrid('getPager');
			
			pager.pagination({
				beforePageText: '第',//页数文本框前显示的汉字
				afterPageText: '页   共 {pages} 页',
				displayMsg: '显示 {from} - {to} 条记录   共 {total} 条记录.  ' + '',
				layout: ['list', 'sep', 'first', 'prev', 'links', 'next', 'last', 'sep', 'refresh', 'manual'],
				onSelectPage:function(pageNum, pageSize){
					opts.pageNumber = pageNum;
					opts.pageSize = pageSize;
					pager.pagination('refresh',{
						pageNumber:pageNum,
						pageSize:pageSize
					});
					dg.datagrid('loadData',data);
				}
			});
			if (!data.originalRows){
				data.originalRows = (data.rows);
			}
			var start = (opts.pageNumber-1)*parseInt(opts.pageSize);
			var end = start + parseInt(opts.pageSize);
			
			data.rows = (data.originalRows.slice(start, end));
			
			return data;
		}
		
		function getQueryParam() {
			var c = {}, p = {}, formId = $("#IPfengXianForm");
			$.each(formId.find("input,select,textarea"), function (e, i) {
				if (i.name = (i.name || "").replace(/^\s*|\s*&/, ""), i.name) {
					if (/^.*\[\]$/.test(i.name)) {
						var t = i.name.match(/^(.*)\[\]$/g)[0];
						p[t] = 0 | p[t], i.name = i.name.replace(/^(.*)\[\]$/, "$1[" + p[t]+++"]");
					}
					(/^checkbox|radio$/.test(i.type)) && !i.checked || (c[i.name] = i.value);
				}
			});
			
			//向对象 C 额外追加 参数和值
			c._token = "{{ csrf_token() }}";
			return $.extend({}, c);
		}
	</script>
@endsection