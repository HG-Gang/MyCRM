@extends('user.layout.main_right')

@section('content')
	<form class="layui-form" action="" id="NewsListForm" style="margin-top: 8px;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">创建时间</label>
				<div class="layui-input-inline" style="width: 200px;">
					<input type="text" name="startdate" id="startdate" placeholder="请输入开始时间" autocomplete="off" class="layui-input">
				</div>
				<div class="layui-form-mid">-</div>
				<div class="layui-input-inline" style="width: 200px;">
					<input type="text" name="enddate" id="enddate" placeholder="请输入结束时间" autocomplete="off" class="layui-input">
				</div>
			</div>
		<button type="button" class="layui-btn" onclick="autoSearchNews()">查找</button>
		</div>
	</form>
	<div style="margin:20px 0px;"></div>
	<table id="data_list" style="width: 99%;" pagination="true" title="新闻列表"></table>
@endsection

@section('custom-resources')
	<script type="text/javascript">
		$(function () {
			autoSearchNews();
		});

		function autoSearchNews() {
			createTable();
		}

		function dataGridConfig() {
			var config = {};
			config.DataColumns = [[
				{field:'news_title' ,title:'{{ trans ('systemlanguage.news_title') }}', width:100, align:'center',},
				{field:'rec_crt_date' ,title:'{{ trans ('systemlanguage.news_rec_crt_date') }}', width:100, align:'center',},
				{field:'newsedit' ,title:'{{ trans ('systemlanguage.news_edit') }}' ,width:100 ,align:'center', formatter: function (value, rowData, rowIndex) {
					return '<a href="javascript:;" onclick="newsDetail('+ rowData.news_id +')" class="l-btn l-btn-small l-btn-plain" style="color: blue;">' +
								'<span class="l-btn-left l-btn-icon-left">' +
									'<span class="l-btn-text">查看</span>' +
									'<span class="l-btn-icon icon-search">&nbsp;</span>' +
								'</span>'+
							'</a>';
				}},
			]];
			
			return config;
		}
		
		function DbClickEditAccountInfo(rowIndex, rowData) {
			console.log("没有可查看看的信息!");
		}
		
		function createTable() {
			var config = dataGridConfig(), pagerData;
			pagerData = new $.WidgetPage({
				reqUrl: '/user/newsListSearch',
				tableId: 'data_list',
				formId: 'NewsListForm',
				method: 'post',
				columns : config.DataColumns,
				formToken: "{{ csrf_token() }}",
				idField: 'news_id',
				extraParam: subPuid,
				rownumbers: true,
				singleSelect: true,
				showFooter: true,
			});
			
			pagerData.GridInit();
		}
		</script>
@endsection