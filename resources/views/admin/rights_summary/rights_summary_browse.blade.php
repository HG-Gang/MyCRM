@extends('user.layout.main_right')

@section('public-resources')
	<script type="text/javascript" src="{{ URL::asset('js/plugins/jquery-easyui-1.5.1/datagrid-detailview.js') }}?ver={{ resource_version_number() }}"></script>
@endsection

@section('content')
	<form class="layui-form" action="" id="RightsSumForm" style="margin-top: 8px;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">交易账户</label>
				<div class="layui-input-block">
					<input type="text" name="userId" id="userId" autocomplete="off" placeholder="请输入交易账户" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">结算状态</label>
				<div class="layui-input-inline">
					<select name="orderstatus" id="orderstatus">
						<option value="">请选择结算状态</option>
						<option value="1" selected>等待结算</option>
						<option value="2">已经结算</option>
					</select>
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">权益周期</label>
				<div class="layui-input-inline">
					<select name="rightsUserCycle" id="rightsUserCycle">
						<option value="">请选择权益周期</option>
						<option value="1" selected>周结</option>
						<option value="2">半月结</option>
						<option value="3">月结</option>
					</select>
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">结算时间</label>
				<div class="layui-input-inline" style="width: 200px;">
					<input type="text" name="startdate" id="startdate" placeholder="请输入开始时间" autocomplete="off" class="layui-input">
				</div>
				<div class="layui-form-mid">-</div>
				<div class="layui-input-inline" style="width: 200px;">
					<input type="text" name="enddate" id="enddate" placeholder="请输入结束时间" autocomplete="off" class="layui-input">
				</div>
			</div>
			<button type="button" class="layui-btn" onclick="searchRightsSum()">查找</button>
		</div>
	</form>
	<div id="ohterForm" style="display: none;">
		<form class="layui-form" action="" id="RightsSumFormOther" style="margin-top: 8px;">
			<input type="hidden" id="uid" name="uid" readonly="readonly">
			<input type="hidden" id="sumdata" name="sumdata" readonly="readonly">
			<input type="hidden" id="status" name="status" readonly="readonly">
			<input type="hidden" id="type" name="type" readonly="readonly">
			<div class="layui-form-item">
				<div class="layui-inline">
					<label class="layui-form-label">实返金额</label>
					<div class="layui-input-block">
						<input type="text" name="real_amt" id="real_amt" autocomplete="off" placeholder="请输入实返金额" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
					</div>
				</div>
			</div>
			<div class="layui-form-item">
				<div class="layui-inline">
					<label class="layui-form-label">其他扣款</label>
					<div class="layui-input-block">
						<input type="text" name="other_amt" id="other_amt" autocomplete="off" placeholder="请输入其他扣款非必填" class="layui-input" style="width: 200px;">
					</div>
				</div>
			</div>
			<div class="layui-form-item">
				<div class="layui-inline">
					<label class="layui-form-label">应得金额</label>
					<div class="layui-input-block">
						<input type="text" name="should_amt" id="should_amt" autocomplete="off" placeholder="请输入应得金额" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
					</div>
				</div>
			</div>
			<div class="layui-form-item">
				<div class="layui-input-block">
					<button type="button" class="layui-btn" onclick="submitConfirm()">确定</button>
				</div>
			</div>
		</form>
	</div>
	<div id="manualForm" style="display:none;">
		<form class="layui-form" action="" id="RightsSumFormOther" style="margin-top: 8px;">
			<input type="hidden" id="manual_uid" name="manual_uid" readonly="readonly">
			<input type="hidden" id="manual_sumdata" name="manual_sumdata" readonly="readonly">
			<input type="hidden" id="manual_status" name="manual_status" readonly="readonly">
			<div class="layui-form-item">
				<label class="layui-form-label">原因</label>
				<div class="layui-input-block">
					<textarea id="manual_reason" name="manual_reason" autocomplete="off" placeholder="请输入手动结算原因" class="layui-textarea" style="width: 300px; resize: none;"></textarea>
				</div>
			</div>
			<div class="layui-form-item">
				<div class="layui-input-block">
					<button type="button" class="layui-btn" onclick="ManualsubmitConfirm()">确定</button>
				</div>
			</div>
		</form>
	</div>
	<div style="margin:20px 0px;"></div>
	<div id="real" style="margin-left: 20px;"></div>
	<table id="data_list" style="width: 99%;" pagination="true" title="权益结算"></table>
@section('custom-resources')
	<script type="text/javascript">
		$("#other_amt").bind("keyup",function(){
			$("#other_amt").val($("#other_amt").val().replace(/[^(\d+)\.(\d)]/g,''));
		});
		
		$("#other_amt").bind("input propertychange", function() {
			var ohter_amt = $("#other_amt").val();
			var real_amt = $("#real_amt").val();
			
			if ($.trim(ohter_amt) != "" && !isNaN(ohter_amt)) {
				$("#should_amt").val((real_amt - Math.abs(ohter_amt)).toFixed(2));
			} else {
				$("#should_amt").val("");
			}
		});
		
		function searchRightsSum() {
			createTable();
		}
		
		function dataGridConfig() {
			var config = {};
			config.DataColumns = [[
				{field:'rightsUserId' ,title:'{{ trans ('systemlanguage.rights_sum_userId') }}', width:100, align:'center',},
				{field:'rightsUserValue' ,title:'{{ trans ('systemlanguage.rights_sum_userVal2') }}', width:100, align:'center',},
				{field:'realamt' ,title:'{{ trans ('systemlanguage.rights_sum_realamt') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
						if(value < 0) {
							return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
						}
						return parseFloatToFixed(value);
					}},
				{field:'rightsSumDate' ,title:'{{ trans ('systemlanguage.rights_sum_date') }}', width:100, align:'center',},
				{field:'rightsSumStatus' ,title:'{{ trans ('systemlanguage.rights_sum_status') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
						return rightsSumStatus(value);
				}},
				{field:'rightsManualReason' ,title:'{{ trans ('systemlanguage.rights_manual_reason') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if (value != "") {
						return '<a href="javascript:;" onclick="showManualReason('+ rowData.rightsUserId +', '+ "'"+ value +"'"+')" class="l-btn l-btn-small l-btn-plain">' +
							'<span class="l-btn-left l-btn-icon-left">' +
							'<span class="l-btn-text" style="color: black;">查看</span>' +
							'<span class="l-btn-icon icon-search">&nbsp;</span>' +
							'</span>'+
							'</a>';
					} else {
						return "========";
					}
				}},
				{field:'rightsMt4OrderId',title:'{{ trans ('systemlanguage.rights_sum_mt4OrderId') }}', width:100, align:'center',},
				{field:'rightsUseCycle' ,title:'{{ trans ('systemlanguage.rights_sum_userCycle') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
						return rightsUseCycle(value);
					}},
				{field:'rec_crt_date' ,title:'{{ trans ('systemlanguage.rights_sum_reccrtdate') }}', width:100, align:'center',},
				{field:'rights_sum_action' ,title:'{{ trans ('systemlanguage.rights_sum_action') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
						return rights_sum_action(rowData.rightsUserId, rowData.realamt, rowData.rightsSumStatus, rowData.rightsSumDate);
					}},
			]];
			
			config.Buttons = [{
				text: '{{ trans ('systemlanguage.export') }}',
				iconCls:'icon-export',
				handler:function(){
					flow_export("RightsSumForm", "RightsSumFlow", "admin", "{{ csrf_token() }}")
				}
			}];
			
			return config;
		}
		
		function DbClickEditAccountInfo(rowIndex, rowData) {
			console.log("没有可查看的信息");
		}
		
		function createTable() {
			var config = dataGridConfig();
			$('#data_list').datagrid({
				url: route_prefix() + '/amount/rightsSummarySearch',
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
				pageList: [5, 10, 20, 20 * 2, 20 * 4],
				idField: 'rightsUserId',
				queryParams: getQueryParam(), //json,object,function getQueryParams
				columns: config.DataColumns,
				view: detailview,
				detailFormatter:function(index,row){
					return '<div style="padding:2px;position:relative;"><table class="ddv"></table></div>';
				},
				onDblClickRow: function (rowIndex, rowData) {
					DbClickEditAccountInfo(rowIndex, rowData);
				},
				onLoadSuccess: function (rowData) {
					if (rowData.rows.length == 0) {
						$('#data_list').closest('div.datagrid-wrap').find('div.pagination').hide();
						$('#data_list').closest('div.datagrid-wrap').find('div.datagrid-footer').hide();
					} else {
						$('#data_list').closest('div.datagrid-wrap').find('div.pagination').css('display', 'block');
						$('#data_list').closest('div.datagrid-wrap').find('div.datagrid-footer').css('display', 'block');
					}
				},
				onExpandRow: function(index,row){
					var ddv = $(this).datagrid('getRowDetail',index).find('table.ddv');
					ddv.datagrid({
						url:route_prefix() + '/amount/rightsSummarySearchDetail/' + row.rightsUserId + '/' + row.rightsSumStatus + '/' + row.rightsSumDate,
						method: 'get',
						fitColumns:true, // 网格宽度自适应
						//toolbar: toolbar, // 表格工具栏
						striped: true, // 数据表格条纹化
						nowrap: true, // 一行里显示
						showFooter: false,
						rownumbers: true,
						loadMsg:'正在努力加载...', // 提示消息
						height:'auto',
						showFooter: true,
						singleSelect: true,
						columns:[[
							{field:'rightsUserId' ,title:'{{ trans ('systemlanguage.rights_sum_userId') }}', width:100, align:'center',},
							{field:'rightsSumRemarks' ,title:'{{ trans ('systemlanguage.rights_sum_remarks') }}', width:200, align:'center',},
							{field:'rightsUserProfit' ,title:'{{ trans ('systemlanguage.rights_sum_profit') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
								if (rowData.rightsUserId) {
									return userProfitStyle(value);
								} else if (rowData.rightsSumRemarks == '总计') {
									return parseFloatToFixed(value);
								} else {
									return '';
								}
							}},
							{field:'rightsSumShouxufei' ,title:'{{ trans ('systemlanguage.rights_sum_shouxufei') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
								if (rowData.rightsUserId) {
									return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
								} else if (rowData.rightsSumRemarks == '总计') {
									return parseFloatToFixed(value);
								} else {
									return '';
								}
							}},
							{field:'rightsSumSwaps' ,title:'{{ trans ('systemlanguage.rights_sum_swaps') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
								if (rowData.rightsUserId) {
									return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
								} else if (rowData.rightsSumRemarks == '总计') {
									return parseFloatToFixed(value);
								} else {
									return '';
								}
							}},
							{field:'rightsUserVolume' ,title:'{{ trans ('systemlanguage.rights_sum_volume') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
								if (rowData.rightsUserId) {
									return parseFloatToFixed(value);
								} else if (rowData.rightsSumRemarks == '总计') {
									return parseFloatToFixed(value);
								} else {
									return '';
								}
							}},
							{field:'rightsUserValue' ,title:'{{ trans ('systemlanguage.rights_sum_userVal') }}', width:100, align:'center',},
							{field:'rightsUserValueDiff' ,title:'{{ trans ('systemlanguage.rights_sum_userValDiff') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
								if (rowData.rightsUserId) {
									return value;
								} else {
									return '';
								}
							}},
							/*{field:'rightsSumReturnamt' ,title:'{{ trans ('systemlanguage.rights_sum_returnamt') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
								if (rowData.rightsUserId) {
									return value;
								} else {
									return '';
								}
							}},*/
							{field:'rightsSumMoney' ,title:'{{ trans ('systemlanguage.rights_sum_money') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
								if (rowData.rightsUserId) {
									return value;
								} else {
									return '';
								}
							}},
							{field:'rightsSumYajin' ,title:'{{ trans ('systemlanguage.rights_sum_yajin') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
								if (rowData.rightsUserId) {
									return value;
								} else {
									return '';
								}
							}},
							{field:'rightsSumRealamt' ,title:'{{ trans ('systemlanguage.rights_sum_realamt') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
								if(value < 0) {
									return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
								}
								return parseFloatToFixed(value);
							}},
						]],
						onResize:function(){
							$('#data_list').datagrid('fixDetailRowHeight',index);
						},
						onLoadSuccess:function(){
							setTimeout(function(){
								$('#data_list').datagrid('fixDetailRowHeight',index);
								$('#data_list').datagrid('fixRowHeight', index);
							},0);
						}
					});
					$('#data_list').datagrid('fixDetailRowHeight',index);
				}
			});
			
			$('#data_list').datagrid('getPager').pagination({
				buttons: config.Buttons,
				beforePageText: '第',//页数文本框前显示的汉字
				afterPageText: '页   共 {pages} 页',
				displayMsg: '显示 {from} - {to} 条记录   共 {total} 条记录.  ' + '',
				layout: ['list', 'sep', 'first', 'prev', 'links', 'next', 'last', 'sep', 'refresh', 'manual'],
			});
		}
		
		function rights_sum_action(uid, value, status, sumdata) {
			if ("{{ $role }}" == "1" && status == "1") {
				if (value < 0) {
					return "<span style='color: red; cursor: pointer;' onclick='WithdrawOrDepositAmount2("+ uid +", "+ value +", "+ status +", "+ sumdata +", "+'"withdraw"'+")'>"+ '账户出金' +"</span>" + "&nbsp;&nbsp;" +
							"<span style='color: #3300CC; cursor: pointer;' onclick='ManualsettlementDialog("+ uid +", "+ status +", "+ sumdata +")'>"+ '手动结算' +"</span>";
				} else if (value > 0) {
					return "<span style='color: #009688; cursor: pointer;' onclick='WithdrawOrDepositAmount2("+ uid +", "+ value +", "+ status +", "+ sumdata +", "+'"deposit"'+")'>"+ '账户入金' +"</span>" + "&nbsp;&nbsp;" +
							"<span style='color: #3300CC; cursor: pointer;' onclick='ManualsettlementDialog("+ uid +", "+ status +", "+ sumdata +")'>"+ '手动结算' +"</span>";
				} else if (value == 0) {
					return "<span style='color: #3300CC; cursor: pointer;' onclick='ManualsettlementDialog("+ uid +", "+ status +", "+ sumdata +")'>"+ '手动结算' +"</span>";
				}
			} else {
				if (status == "1") {
					if (value < 0) {
						return "<span style='color: red; cursor: pointer;' onclick='WithdrawOrDepositAmount2("+ uid +", "+ value +", "+ sumdata +", "+'"withdraw"'+")'>"+ '账户出金' +"</span>";
					} else if (value > 0) {
						return "<span style='color: #009688; cursor: pointer;' onclick='WithdrawOrDepositAmount2("+ uid +", "+ value +", "+ sumdata +", "+'"deposit"'+")'>"+ '账户入金' +"</span>";
					} else if (value == 0) {
						return "<span>"+ '========' +"</span>";
					}
				} else if (status == "2") {
					return "<span>"+ '========' +"</span>";
				}
			}
		}
		
		function WithdrawOrDepositAmount2(uid, value, status, sumdata, type) {
			var index1 = openLoadShade();
			$("#uid").val(uid);$("#real_amt").val(value);$("#sumdata").val(sumdata);$("#type").val(type);$("#status").val(status);
			layer.open({
				type: 1,
				title: '附加项',
				skin: 'layui-layer-molv',
				//closeBtn: 0,
				move: false,
				area: ['450px', '300px'],
				content: $("#ohterForm"),
				cancel: function(index, layero){
					closeLoadShade(index1);
					layer.close(index)
					return false;
				}
			});
		}
		
		function ManualsettlementDialog(uid, status, sumdata) {
			var index1 = openLoadShade();
			$("#manual_uid").val(uid);$("#manual_sumdata").val(sumdata);$("#manual_status").val(status);
			layer.open({
				type: 1,
				title: '手动结算原因',
				skin: 'layui-layer-molv',
				//closeBtn: 0,
				move: false,
				area: ['450px', '250px'],
				content: $("#manualForm"),
				cancel: function(index, layero){
					closeLoadShade(index1);
					layer.close(index)
					return false;
				}
			});
		}
		
		function submitConfirm() {
			layer.confirm('该操作不可逆,请仔细确认', {icon: 3, title:'操作确认提示', move: false,}, function(index) {
				var index1 = openLoadShade();
				$.ajax({
					url: route_prefix() + '/amount/confirm_options',
					data: {
						_token:     "{{ csrf_token() }}",
						uid:        $("#uid").val(),
						real_amt:	$("#real_amt").val(), //实返金额
						other_amt:	$("#other_amt").val(), // 扣款金额
						amount:     $("#should_amt").val(), //应得金额
						sumdata:	$("#sumdata").val(),
						status:     $("#status").val(),
						type:       ($("#should_amt").val() != "" && $("#should_amt").val() < 0) ? "withdraw" : $("#type").val(),
					},
					dateType: "JSON",
					type: "POST",
					async: false,
					success: function (data) {
						if (data.msg == "SUC") {
							closeLoadShade(index1);
							layer.closeAll();
							errorTips("操作成功!", "msg", data.col);
							$("#other_amt").val("");
							$("#should_amt").val("");
							searchRightsSum();
							return;
						} else if (data.msg == "FAIL") {
							closeLoadShade(index1);
							layer.closeAll();
							if (data.err == "errflowrecord") {
								errorTips("无效的记录!", "msg", data.col);
							} else if (data.err == "errparams") {
								layer.alert("参数错误, 请重新操作!", {icon: 5}, function () {
									location.href = "{{url(route_prefix() . '/amount/rights_summary')}}";
								});
							} else if (data.err == "errrightsSumMoney") {
								errorTips("实返金额与实际金额不符!", "msg", "real_amt");
							} else if (data.err == "erroptions") {
								layer.alert("操作失败, 请重新操作!", {icon: 5}, function () {
									location.href = "{{url(route_prefix() . '/amount/rights_summary')}}";
								});
							} else if (data.err == "erralreadybalance") {
								errorTips("该记录的结算周期已结算过!", "msg", data.col);
							}
						}
					},
					error: function (data) {
						closeLoadShade(index1);
						errorTips('系统错误，请刷新重新操作')
					}
				});
			});
		}
		
		function ManualsubmitConfirm() {
			if ($.trim($("#manual_reason").val()) == "") {
				errorTips("手动结算原因必填!", "msg", "manual_reason");
			} else {
				layer.confirm('该操作不可逆,请仔细确认', {icon: 3, title:'操作确认提示', move: false,}, function(index) {
					var index1 = openLoadShade();
					$.ajax({
						url: route_prefix() + '/amount/manual_confirm_options',
						data: {
							_token:             "{{ csrf_token() }}",
							manual_uid:         $("#manual_uid").val(),
							manual_sumdata:	    $("#manual_sumdata").val(),
							manual_status:      $("#manual_status").val(),
							manual_reason:      $("#manual_reason").val(),
						},
						dateType: "JSON",
						type: "POST",
						async: false,
						success: function (data) {
							if (data.msg == "SUC") {
								closeLoadShade(index1);
								layer.closeAll();
								errorTips("操作成功!", "msg", data.col);
								$("#manual_reason").val("");
								searchRightsSum();
								return;
							} else if (data.msg == "FAIL") {
								closeLoadShade(index1);
								layer.closeAll();
								if (data.err == "errflowrecord") {
									errorTips("无效的记录!", "msg", data.col);
								} else if (data.err == "errparams") {
									layer.alert("参数错误, 请重新操作!", {icon: 5}, function () {
										location.href = "{{url(route_prefix() . '/amount/rights_summary')}}";
									});
								} else if (data.err == "errrightsSumMoney") {
									errorTips("实返金额与实际金额不符!", "msg", "real_amt");
								} else if (data.err == "erroptions") {
									layer.alert("操作失败, 请重新操作!", {icon: 5}, function () {
										location.href = "{{url(route_prefix() . '/amount/rights_summary')}}";
									});
								} else if (data.err == "erralreadybalance") {
									errorTips("该记录的结算周期已结算过!", "msg", data.col);
								}
							}
						},
						error: function (data) {
							closeLoadShade(index1);
							errorTips('系统错误，请刷新重新操作')
						}
					});
				});
			}
		}
		
		function getQueryParam() {
			var c = {}, p = {}, formId = $("#RightsSumForm");
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
		
		function showManualReason(orderId, value) {
			layer.open({
				type: 1,
				area: ['400px', '200px'],
				title: "用户 " + orderId + " 的手动结算原因",
				anim: 5,
				move: false,
				skin: 'layui-layer-molv', //样式类名
				content: "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" + value,
			});
		}
	</script>
@endsection