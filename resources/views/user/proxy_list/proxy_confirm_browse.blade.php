@extends('user.layout.main_right')

@section('public-resources')
	<style>
	a {
		color: #4FA7ED;
		text-decoration: none;
		font-weight: 900;
	}
	</style>
@endsection

@section('content')
	<form class="layui-form" action="" id="proxyConfirmForm" style="margin-top: 8px;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">交易账户</label>
				<div class="layui-input-block">
					<input type="text" name="userId" id="userId" autocomplete="off" placeholder="请输入交易账户" class="layui-input" style="width: 200px;">
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
			<button type="button" class="layui-btn" onclick="searchResult()">查找</button>
		</div>
	</form>
	<div style="margin:20px 0px;"></div>
	<div id="real" style="margin-left: 20px;"></div>
	<table id="data_list" style="width: 99%;" pagination="true" title="待确认代理列表"></table>
@endsection

@section('custom-resources')
	<script type="text/javascript">
		function searchResult() {
			createTable();
		}
		
		function dataGridConfig() {
			var config = {};
			config.DataColumns = [[
				{field: 'userId', title: '{{ trans ('systemlanguage.proxy_user_id') }}', width: 100, align: 'center', formatter: function (value, rowData, rowIndex) {
					if (rowData.userStatus == '1') {
						return "<span class='l-btn-left l-btn-icon-right' title='已认证'>" +
									"<span class='l-btn-text'>" + value + "</span>" +
									"<span class='l-btn-icon icon-auth-man'>&nbsp;</span>" +
								"</span>";
					}
					
					return "<span class='l-btn-left l-btn-icon-right'><span class='l-btn-text'>" + value + "</span></span>";
				}},
				{field: 'userName', title: '{{ trans ('systemlanguage.proxy_user_name') }}', width: 100, align: 'center', },
				{field: 'userSex', title: '{{ trans ('systemlanguage.proxy_user_sex') }}', width: 100, align: 'center', },
				{field: 'userEmail', title: '{{ trans ('systemlanguage.proxy_user_email') }}', width: 100, align: 'center', formatter: function (value, rowData, rowIndex) {
					return userEmailFormat(value);
				}},
				{field: 'userPhone', title: '{{ trans ('systemlanguage.proxy_user_phone') }}', width: 100, align: 'center', formatter: function (value, rowData, rowIndex) {
					return userPhoneFormat(value);
				}},
				{field: 'userGroupId', title: '{{ trans ('systemlanguage.proxy_user_groupId') }}', width: 100, align: 'center', formatter: function (value, rowData, rowIndex) {
					return getUserGroupName(value, rowData);
				}},
				{field: 'userRights', title: '{{ trans ('systemlanguage.proxy_agents_rights') }}', width: 100, align: 'center', },
				{field: 'rec_crt_date', title: '{{ trans ('systemlanguage.proxy_rec_crt_date') }}', width: 100, align: 'center',},
				{field: 'userOptions',title: '{{ trans ('systemlanguage.proxy_user_options') }}',width: 100, align: 'center',formatter: function (value, rowData, rowIndex) {
					if (rowData.userId) {
						return '<a href="javascript:;" title="确认代理" onclick="confirmAgentsLevel('+ rowData.userId +', '+ rowData.userGroupId +')" class="l-btn l-btn-small l-btn-plain" style="color: blue;">' +
										'<span class="l-btn-left l-btn-icon-left">' +
										'<span class="l-btn-text">确认代理</span>' +
										'<span class="l-btn-icon icon-confirm">&nbsp;</span>' +
									'</span>'+
								'</a>';
					}
				}},
			]];
			
			return config;
		}
		
		function createTable() {
			var config = dataGridConfig(), pagerData;
			pagerData = new $.WidgetPage({
				//title: ajaxGetTableTitle(),
				reqUrl: '/user/proxy/proxyConfirmSearch',
				tableId: 'data_list',
				formId: 'proxyConfirmForm',
				method: 'post',
				columns: config.DataColumns,
				//buttons: config.Buttons,
				formToken: "{{ csrf_token() }}",
				idField: 'userId',
				extraParam: subPuid,
				rownumbers: true,
				singleSelect: true,
				showFooter: false,
			});
			
			pagerData.GridInit();
		}

		function confirmAgentsLevel(userId, groupId) {
			var gName = $("#groudId" + groupId + " option:selected").text();
			var gId = $("#groudId" + groupId + " option:selected").val();
			var str = "<div>";
				str += "确定将 " + "<span style='color: #009688'>"+ userId +"</span>";
				str += " 设为: " + "<span style='color: #FF7F00'>" + gName + "</span>" + " 吗? " + "</div>";
			layer.confirm(str, { btn: ['确定','取消']},function (index, layero) {
				var index1 = openLoadShade();
				$.ajax({
					url: "/user/proxy/confirmLevelChange",
					data: {
						userId:		userId,
						gId:		gId,
						gName:		gName,
						_token:		"{{ csrf_token() }}",
					},
					dateType: "JSON",
					type: "POST",
					async: false,
					success: function (data) {
						if (data.msg == "FAIL") {
							console.log(data.err);
							layer.msg("确认代理失败,请重新操作", {
								time: 30000, //3s后自动关闭
								btn: ['知道了'],
								yes: function (index, layero) {
									layer.close(index);
									closeLoadShade(index1);
								}
							});
						} else if (data.msg == 'SUC') {
							layer.msg("确认代理成功", {
								time: 20000, //20s后自动关闭
								btn: ['知道了'],
								yes: function (index, layero) {
									layer.close(index);
									closeLoadShade(index1);
									searchResult();
									//parent.layer.closeAll();
									//location.href = "{{url('/user/proxy/confirm')}}";
								}
							});
						}
					},
					error: function () {
						closeLoadShade(index1);
						searchResult();
						layer.msg("未知错误,请尝试重新操作或联系客服.");
					}
					});
				});
			}
		
		//双击查看直属代理商信息
		function DbClickEditAccountInfo(rowIndex, rowData) {
		console.log("没有可查看的信息");
		}
	</script>
@endsection