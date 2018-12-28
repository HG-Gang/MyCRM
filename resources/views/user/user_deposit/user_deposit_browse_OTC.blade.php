@extends('user.layout.main_right')

@section('public-resources')
	<link rel="stylesheet" href="{{ URL::asset('css/icon-bank.css') }}?ver={{ resource_version_number() }}"/>
@endsection

@section('content')
	{{--@if($_user_info['user_id'] != '9180001')
		<div class="not-allow_withdrawals">
			<span><img src="{{URL::asset('/img/not_allowed_withdrawals.png') }}" style="margin-top: 20px;"></span>
			<span class="span-text-bottom">
				<h2>很抱歉，入金功能正在进行维护升级中.....</h2>
				<h2>如有疑问, 请联系客服</h2>
			</span>
		</div>
		@if($_user_info['IDcard_status'] != '2')
			<div class="not-allow_withdrawals">
				<span><img src="{{URL::asset('/img/not_allowed_withdrawals.png') }}" style="margin-top: 20px;"></span>
				<span class="span-text-bottom">
					<h2>很抱歉，您的身份证信息还未认证，暂时无法进行入金操作.</h2>
					<h2>如有疑问, 请联系客服</h2>
				</span>
			</div>
	@else--}}@if($_global_role['para_data0'] == '1')
		<div class="not-allow_withdrawals">
			<span><img src="{{URL::asset('/img/not_allowed_withdrawals.png') }}" style="margin-top: 20px;"></span>
            <span class="span-text-bottom">
                <h2>很抱歉，系统设定为不允许入金</h2>
                <h2>如有疑问, 请联系客服</h2>
            </span>
		</div>
	@elseif($_user_info['enable_readonly'] == 1)
		<div class="not-allow_withdrawals">
			<span><img src="{{URL::asset('/img/not_allowed_withdrawals.png') }}" style="margin-top: 20px;"></span>
            <span class="span-text-bottom">
                <h2>很抱歉，您的账户已被设定为不能入金</h2>
                <h2>如有疑问, 请联系客服</h2>
            </span>
		</div>
	@elseif(!empty($_today_role) && (int)$_today_role['start'] == (int)$_today_role['end'] && in_array (date ('w'), array ('0', '6'), true))
		{{--特殊日期 周六日 不能入金--}}
		<div class="not-allow_withdrawals">
			<span><img src="{{URL::asset('/img/not_allowed_withdrawals.png') }}" style="margin-top: 20px;"></span>
            <span class="span-text-bottom">
                <h2>请予入金时间充值</h2>
                <h2>如有疑问, 请联系客服</h2>
            </span>
		</div>
	@elseif(!empty($_today_role) && ((int)strtotime (date('YmdHis')) >= $_today_role['start'] && (int)strtotime (date('YmdHis')) <= (int)$_today_role['end']) && in_array (date ('w'), array ('1', '2', '3', '4', '5', '0', '6'), true))
		<div style="display: inline-block; padding: 9px 44px;">
			<div style="display: inline-block; padding-left: 5px;">
				<span><img src="{{URL::asset('/img/Otc_pay_process.jpg') }}" style="margin-top: 10px;"></span>
			</div>
			<div style="float:right;display: inline-block;">
				<form class="" action="{{ URL::asset('/user/deposit_request_otc') }}" method="post" id="userDepositForm" name="userDepositForm" target="_blank" style="margin-top: 8px;">
					{!! csrf_field() !!}
					<div class="layui-form-item">
						<div class="layui-inline">
							<span class="layui-form-label">存款账户</span>
							<div class="layui-input-block">
								<input type="text" name="userId" id="userId" value="{{ $_user_info['user_id'] }}" autocomplete="off" placeholder="请输入交易账户" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff;cursor:text;font-size: 15px; font-weight: 100;">
								<input type="hidden" name="playerId" id="playerId" value="{{ $_user_info['player_Id'] }}">
							</div>
						</div>
						<div class="layui-inline">
							<span class="layui-form-label">存款金额</span>
							<div class="layui-input-inline">
								<input type="text" name="deposit_amt" id="deposit_amt" oninput="depoActAmount()" autocomplete="off" placeholder="请输入存款金额" class="layui-input" style="width: 200px;">
							</div>
							<div class="layui-form-mid"> RMB </div>
						</div>
					</div>
					<div class="layui-form-item">
						<div class="layui-inline">
							<span class="layui-form-label">当前汇率</span>
							<div class="layui-input-block">
								<input type="text" name="deposit_rate" id="deposit_rate" value="{{ $_sys_conf['sys_deposit_rate'] }}" autocomplete="off" placeholder="请输入当前汇率" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff;cursor:text;font-size: 15px; font-weight: 100;">
							</div>
						</div>
						<div class="layui-inline">
							<span class="layui-form-label">实际到账</span>
							<div class="layui-input-inline">
								<input type="text" name="deposit_act_amt" id="deposit_act_amt" autocomplete="off" placeholder="" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff;cursor:text;font-size: 15px; font-weight: 100;">
							</div>
							<div class="layui-form-mid"> USD </div>
						</div>
					</div>
					<div class="layui-form-item">
						<div class="layui-inline">
							<div class="layui-input-block">
								<button type="button" id="depositBtn" name="depositBtn" onclick="depositRequest()" class="layui-btn">确定</button>
								<p style="display: none; margin-left: 5px;" id="show_hide">温馨提示: 未能跳转支付页面<a href="javascript:openBlankSubmit()" style="color: red;">请点击这里</a></p>
							</div>
						</div>
					</div>
				</form>
				<div style="width:650px; padding: 9px 15px;">
					<h5><span style="color:red; font-weight: 600;font-size:14px;">请注意</span>: 为确保充值成功,请在输入的存款金额与点击【确定】按钮后输入的存款金额保持一致,否则账户无法正确的收到换算后的入金金额</h5>
					<h5>如有疑问, 请联系客服</h5>
				</div>
			</div>
		</div>
	@else
		<div class="not-allow_withdrawals">
			<span><img src="{{URL::asset('/img/not_allowed_withdrawals.png') }}" style="margin-top: 20px;"></span>
            <span class="span-text-bottom">
                <h2>请予入金时间充值</h2>
                <h2>如有疑问, 请联系客服</h2>
            </span>
		</div>
	@endif
	<div class="deposit-matter">
		<p style="margin-bottom:0px; height: 32px; line-height: 32px;color:red; font-weight: 600;font-size:14px;">入金注意事项</p>
		{{--@if($_dep_dispaly['para_data0'] == '0' && $_dep_dispaly['para_data1'] == '')--}}
		<p>1.通道一单笔入金最高10万人民币.</p>
		{{--@else
			<p>1.通道一单笔入金最高1万人民币,通道二单笔入金最高5万人民币。</p>
		@endif--}}
		<p>2.请确保以上数据完整及属实，如有不全或遗漏，会影响到帐时间。</p>
		<p>3.客户是采用人民币入金美元到账，汇率按照工作日9:30分的中国银行(香港)外汇牌价现汇买卖价作为存取款汇率，本公司保留更改汇率的最终决定权。</p>
		<p>4.本公司在线入金每天二十四小时全自动实时处理(每星期七天，公众假期照常)。</p>
		<p>5.在线入金每笔最低为700元人民币，在线入金暂不支持美元账户直接入金。</p>
		<p>6.请在发起入金申请7分钟内完成支付，若未完成请刷新重新发起入金，若超时支付超时需3个工作日退款.</p>
		<p>7.如果对于在线入金有任何疑问，请与我们的客户服务部联络。</p>

	</div>
@endsection

@section('custom-resources')
	<script type="text/javascript">
		$(function () {
			tongdaoYI();
		});
		function tongdaoYI() {
			$("#gateway1").css({"display":"block"});
			$("#gateway2").css({"display":"none"});
			$("#tongdao1").css({"background":"#1AA094", "color": "#fff"});
			$("#tongdao2").css({"background":"#fff", "color": "#000000"});
			$("#pay_channel2").val("");
			$("#pay_gateway2").val("");
			$("input[name='gateway_bank']:checked").attr('checked', false);
			$("#pay_channel").val("tongdaoYI");
		}
		function tongdaoER() {
			$("#gateway1").css("display", "none");
			$("#gateway2").css("display", "block");
			$("#tongdao2").css({"background":"#1AA094", "color": "#fff"});
			$("#tongdao1").css({"background":"#fff", "color": "#000000"});
			$("#pay_channel").val("");
			$("#pay_gateway").val("");
			$("input[name='gateway_bank2']:checked").attr('checked', false);
			$("#pay_channel2").val("tongdaoER");
		}
		function checkAmount() {
			var deposit_amount = $.trim($("#deposit_amt").val());
			var amountReg = /^[\d]+(\.[\d]{1,2})?$/;

			if ("{{ $_user_info['user_id'] }}" != "9180001") {
				//TODO 还需要验证单笔最少存款金额及最大存款金额
				if (deposit_amount == "" || deposit_amount < 0) {
					errorTips("请输入存款金额", "msg", 'deposit_amt')
				} else if (deposit_amount > 0 && !amountReg.test(deposit_amount)) {
					errorTips("只允许最多两位正小数", "msg", "deposit_amount");
				} else if (deposit_amount != "" && Number(deposit_amount) < 700) {
					errorTips("存款金额单笔最低700RMB", "msg", "deposit_amount");
				} else if (deposit_amount != "" && deposit_amount > 0 && deposit_amount > 100001 && true/*$("#pay_channel").val() == "tongdaoYI"*/) {
					errorTips("通道一存款金额单笔最高10万RMB", "msg", "deposit_amount");
				} /*else if (deposit_amount != "" && deposit_amount > 0 && deposit_amount > 50001 && $("#pay_channel2").val() == "tongdaoER") {
				 errorTips("通道二存款金额单笔最高5万RMB", "msg", "deposit_amount");
				 } else if (deposit_amount != "" && !(Number(deposit_amount) % 100 == 0)) {
				 errorTips("存款金额单笔必须是100的整倍数", "msg", "deposit_amount");
				 }*/ /*else if ((!$("input:radio[name='gateway_bank']").is(":checked")) && $("#pay_channel").val() == "tongdaoYI") {
				 layer.msg("请选择支付银行!", {time: 2000});
				 return;
				 } else if ((!$("input:radio[name='gateway_bank2']").is(":checked")) && $("#pay_channel2").val() == "tongdaoER") {
				 layer.msg("请选择支付银行!", {time: 2000});
				 return;
				 }*/ else {
					return true;
				}
			} else {
				return true;
			}

		}

		function depositRequest() {
			/*if ("{{--{{ $_upd_playerId['flag'] }}--}}" == "fail") {
			 layer.msg('网络故障,请重新刷新再试!', {icon: 5});

			 return;
			 } else {*/
			if ($("#depositBtn").hasClass("layui-btn-disabled")) return;
			if (checkAmount()) {
				document.getElementById("userDepositForm").submit();
				$("#show_hide").show();
				$("#depositBtn").addClass('layui-btn-disabled');
			}
			//}
		}

		function openBlankSubmit() {
			if (checkAmount()) {
				var obj_data =$("#userDepositForm").serializeArray();
				var reqUrl = "{{ route('user_deposit_request_otc') }}";
				var temp_form = document.createElement("form");//表单对象
				temp_form.setAttribute('style', 'display:none');   //在form表单中添加查询参数
				temp_form.setAttribute('target', '_blank');
				temp_form.setAttribute('action', reqUrl);
				temp_form.setAttribute('method', 'post');
				temp_form.setAttribute('id', 'open_window_req');
				temp_form.setAttribute('name', 'open_window_req');
				for (var i=0; i < obj_data.length; i ++) {
					var opt = document.createElement("input");
					opt.name = obj_data[i]['name'];
					opt.value = obj_data[i]['value'];
					temp_form.appendChild(opt);
				}
				document.body.appendChild(temp_form);
				temp_form.submit();
			}
		}

		function depoActAmount() {
			var payment = $("#deposit_amt").val();
			if($("#deposit_amt").val() == "") {
				$("#deposit_act_amt").val("");
			} else {
				var money = ($("#deposit_amt").val() / Number("{{ $_sys_conf['sys_deposit_rate'] }}"));
				if ("{{ $_user_info['user_id'] }}" != "318001") {
					$("#deposit_act_amt").val(money.toFixed(2));
				} else {
					$("#deposit_act_amt").val("1.00");
				}
			}
		}

		$("#pay_type_gateway > div > label").click(function(){
			$("#pay_gateway").val($("input[name='gateway_bank']:checked").attr('id'));
			$(this).addClass("border_color").siblings().removeClass("border_color");
		});
		$("#pay_type_gateway2 > div > label").click(function(){
			$("#pay_gateway2").val($("input[name='gateway_bank2']:checked").attr('id'));
			$(this).addClass("border_color").siblings().removeClass("border_color");
		});
	</script>
@endsection