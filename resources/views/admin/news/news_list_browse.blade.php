@extends('user.layout.main_right')

@section('content')
	<form class="layui-form" action="" id="AdminNewsListForm" style="margin-top: 8px;">
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
			<button type="button" class="layui-btn" onclick="createTable()">查找</button>
		</div>
	</form>
	<div style="margin:20px 0px;"></div>
	<table id="data_list" style="width: 99%;" pagination="true" title="新闻列表"></table>
@endsection

@section('custom-resources')
	<script type="text/javascript">
		function dataGridConfig() {
			var config = {};
			config.DataColumns = [[
				{field:'news_title' ,title:'{{ trans ('systemlanguage.news_title') }}', width:100, align:'center',},
				{field:'news_content' ,title:'{{ trans ('systemlanguage.news_content') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					return encodeHtml(value);
				}},
				{field:'is_push' ,title:'{{ trans ('systemlanguage.news_is_push') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if (value == "0") {
						return "<span style='background: #7cc33c; color: #fff; border-radius: 13%; font-size: 12px;'>是</span>";
					} else if (value == "1") {
						return "<span style='background: #ccc; color: #fff; border-radius: 13%; font-size: 12px;'>否</span>";
					} else {
						return value;
					}
				}},
				{field:'news_user' ,title:'{{ trans ('systemlanguage.news_user') }}', width:100, align:'center',},
				{field:'rec_upd_date' ,title:'{{ trans ('systemlanguage.news_rec_upd_date') }}', width:100, align:'center',},
				{field:'rec_crt_date' ,title:'{{ trans ('systemlanguage.news_rec_crt_date') }}', width:100, align:'center',},
				{field:'newsedit' ,title:'{{ trans ('systemlanguage.news_edit') }}' ,width:100 ,align:'center', formatter: function (value, rowData, rowIndex) {
					return '<a href="javascript:;" onclick="newsEditInfo('+ rowData.news_id +')" class="l-btn l-btn-small l-btn-plain" style="color: blue;">' +
								'<span class="l-btn-left l-btn-icon-left">' +
									'<span class="l-btn-text">编辑</span>' +
									'<span class="l-btn-icon icon-edit">&nbsp;</span>' +
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
				reqUrl: route_prefix() + '/news/newsListSearch',
				tableId: 'data_list',
				formId: 'AdminNewsListForm',
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
		
		function newsEditInfo(newsid) {
			var index = layer.open({
				type: 2,
				title: '新闻编辑',
				skin: 'layui-layer-molv',
				move:false,
				content: route_prefix() + '/news/news_edit/' + newsid,
			});
			
			maxWindow(index);
		}
		
	</script>
@endsection