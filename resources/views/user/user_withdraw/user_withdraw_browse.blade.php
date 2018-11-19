@extends('user.layout.main_right')

@section('public-resources')
    <link rel="stylesheet" href="{{ URL::asset('css/icon-bank.css') }}?ver={{ resource_version_number() }}"/>
@endsection

@section('content')
    @if($_global_role['para_data0'] == '1')
        <div class="not-allow_withdrawals">
            <span><img src="{{URL::asset('/img/not_allowed_withdrawals.png') }}" style="margin-top: 20px;"></span>
            <span class="span-text-bottom">
                <h2>很抱歉，系统设定为不允许出金</h2>
                <h2>如有疑问, 请联系客服</h2>
            </span>
        </div>
    @elseif($_user_info['is_out_money'] == '1')
        <div class="not-allow_withdrawals">
            <span><img src="{{URL::asset('/img/not_allowed_withdrawals.png') }}" style="margin-top: 20px;"></span>
            <span class="span-text-bottom">
                <h2>很抱歉，出金申请已锁定</h2>
                <h2>请联系客服</h2>
            </span>
        </div>
    @elseif($_user_info['user_status'] != '1')
        <div class="not-allow_withdrawals">
            <span><img src="{{URL::asset('/img/not_allowed_withdrawals.png') }}" style="margin-top: 20px;"></span>
            <span class="span-text-bottom">
                <h2>很抱歉，您的资料未通过审核...</h2>
                <h2>请联系客服</h2>
            </span>
        </div>
    @elseif(!empty($_today_role) && ((int)strtotime (date('YmdHis')) >= $_today_role['start'] && (int)strtotime (date('YmdHis')) <= (int)$_today_role['end']) && in_array (date ('w'), array ('1', '2', '3', '4', '5'), true))
        <form class="" action="{{ URL::asset('/user/deposit_request') }}" method="post" id="userWithdrawForm" name="userDepositForm" target="_blank" style="margin-top: 20px; margin-left: 20%;">
            {!! csrf_field() !!}
            <div class="layui-form-item">
                <label class="layui-form-label">账户ID</label>
                <div class="layui-input-block">
                    <input type="text" name="userId" id="userId" value="{{ $_user_info['user_id'] }}" autocomplete="off" placeholder="请输入交易账户" class="layui-input" readonly="readonly" style="width: 300px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff;cursor:text;font-size: 15px; font-weight: 100;">
                </div>
            </div>
            {{--<div class="layui-form-item">
                <label class="layui-form-label">银行户名</label>
                <div class="layui-input-block">
                    <input type="text" name="deposit_amt" id="deposit_amt" oninput="depoActAmount()" autocomplete="off" placeholder="请输入存款金额" class="layui-input" style="width: 200px;">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">银行卡号</label>
                <div class="layui-input-block">
                    <input type="text" name="deposit_amt" id="deposit_amt" oninput="depoActAmount()" autocomplete="off" placeholder="请输入存款金额" class="layui-input" style="width: 200px;">
                </div>
            </div>--}}
            <div class="layui-form-item">
                <label class="layui-form-label">账户余额</label>
                <div class="layui-input-block">
                    <input type="text" name="balance_amt" id="balance_amt" value="{{ $_user_info['user_money'] }}" autocomplete="off" placeholder="请输入账户余额" class="layui-input" readonly="readonly" style="width: 300px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff;cursor:text;font-size: 15px; font-weight: 100;">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">可取金额</label>
                <div class="layui-input-block">
                    <input type="text" name="avai_amt" id="avai_amt" value="{{ $_user_info['available_bond_money'] }}" autocomplete="off" placeholder="请输入可取金额" class="layui-input" readonly="readonly" style="width: 300px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff;cursor:text;font-size: 15px; font-weight: 100;">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">当前汇率</label>
                <div class="layui-input-block">
                    <input type="text" name="withdraw_rate" id="withdraw_rate" value="{{ $_sys_conf['sys_draw_rate'] }}" autocomplete="off" placeholder="请输入当前汇率" class="layui-input" readonly="readonly" style="width: 300px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff;cursor:text;font-size: 15px; font-weight: 100;">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">取款金额</label>
                <div class="layui-input-inline">
                    <input type="text" name="withdraw_amt" id="withdraw_amt" autocomplete="off" class="layui-input" placeholder="请输入取款金额" style="width: 300px;">
                </div>
                <div class="layui-form-mid" style="margin-left: 100px;"> USD </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">取款密码</label>
                <div class="layui-input-block">
                    <input type="password" name="withdraw_password" id="withdraw_password" autocomplete="off" placeholder="请输入登录密码" class="layui-input" style="width: 300px;">
                </div>
            </div>
            <span class="foter-text nth-child-span">备注:</span>
            <ul class="foter-text nth-child-span2"style="margin-left: 61px;margin-top: -20px;">
                <li> 客户需自行承担因出金造成交易账户内的净值未能达到本公司的</li>
                <li style="margin-bottom: 10px;">维持保证金水平,而导致交易账号内的订单进行强制平仓所造成的损失.</li>
                <li>
                    <input type="checkbox" name="agree" id="agree">
                    <label for="agree" style="color:#555;">已阅读并同意</label>
                </li>
            </ul>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button type="button" id="depositBtn" name="depositBtn" onclick="withdrawRequest()" class="layui-btn">确定</button>
                </div>
            </div>
        </form>
    @else<div class="not-allow_withdrawals">
            <span><img src="{{URL::asset('/img/not_allowed_withdrawals.png') }}" style="margin-top: 20px;"></span>
            <span class="span-text-bottom">
                    <h2>请予出金时间提交申请</h2>
            </span>
        </div>
    @endif
    <div class="deposit-matter" >
        <p style="color:red;  font-weight: 600;font-size:14px">出金注意事项</p>
        <p>1.为客户能尽早提取资金, 请核实阁下的银行帐户资料是否正确无误.</p>
        <p>2.出金申请采用T+1,当天入金当天不能出金,出金申请时间为周一至周五09:00-16:00, 出金到账时间为T+1到账。</p>
        <p>3.平台成功扣款后，由财务部门核实无误后办理汇款，汇款时间为工作日期间。{{--<br>（汇款时间过后提交的出金延迟到下一个工作日办理）--}}</p>
	    <p>4.在线申请出金单笔低于100美元，需收取额定5美元作为手续费, 单笔最大可取金额不能大于<span style="color: red">$7000</span>。</p>
        <p>5.客户申请取款后, 若发现有任何问题请尽快联络客服部门查询。</p>
    </div>
@endsection

@section('custom-resources')
	<script type="text/javascript">
		function checkWithdrawAmt() {
			var withdraw_amt = $.trim($("#withdraw_amt").val()); //取款金额
			var avai_money = "{{ $_user_info['available_bond_money'] }}"; //可取款金额
			var withdraw_psw = $.trim($("#withdraw_password").val()); //密码
			var amountReg = /^[\d]+(\.[\d]{1,2})?$/;
			
			if (withdraw_amt == "") {
				errorTips("请输入取款金额!", "msg", "withdraw_amt");
			} else if (withdraw_amt != "" && Number(withdraw_amt) <= 5) {
				errorTips("请输入大于$5的取款金额!", "msg", "withdraw_amt");
			} else if (withdraw_amt != "" && Number(withdraw_amt) >= 7000) {
				errorTips("单笔取款金额不能大于$7000!", "msg", "withdraw_amt");
			} else if (withdraw_amt != "" && !amountReg.test(withdraw_amt)) {
				errorTips("只允许最多两位正小数!", "msg", "withdraw_amt");
			} else if (Number(withdraw_amt) > Number(avai_money)) {
				errorTips("不能大于可取金额!", "msg", "withdraw_amt");
			} else if (withdraw_psw == "") {
				errorTips("请输入密码!", "msg", "withdraw_password");
			} else if(!$("#agree").is(':checked')) {
				layer.tips("请先勾选提醒", $("#agree"), {tips: [1, "#335b9f"], time: 2000});
				return false;
			} else {
				return true;
			}
		}
		
		function withdrawRequest() {
			if (checkWithdrawAmt()) {
				var withdraw_amt = $.trim($("#withdraw_amt").val()); //取款金额
				var withdraw_psw = $.trim($("#withdraw_password").val()); //密码
				
				var index1 = openLoadShade();
				$.ajax({
					url: "/user/withdraw_request",
					data: {
						userId:		        $.trim($("#userId").val()),
						withdraw_amt:		withdraw_amt,
						withdraw_psw:		withdraw_psw,
                        poundagemoney:      "{{ $_sys_conf['sys_poundage_money'] }}",
						withdraw_rate:		$.trim($("#withdraw_rate").val()),
						_token:				"{{ csrf_token() }}",
					},
					dateType: "JSON",
					type: "POST",
					async: false,
					success: function (data) {
						if (data.msg == "FAIL") {
						console.log(data.err);
						closeLoadShade(index1);
							if (data.err == "margin_level_low100") {
								layer.msg("抱歉,您的出金风险率过低,暂不能出金!");
								return;
							} else if (data.err == "PSWERR") {
								errorTips("密码错误!", "msg", data.col)
							} else if (data.err == "FATALCANOTCONNECT") {
								layer.msg("抱歉,网络故障,请稍后重试!");
								return;
							} else if (data.err == "APPLYFAIL") {
								layer.msg("抱歉,出金申请失败,请联系客服!");
								return;
							} else if (data.err == "more_available_amt") {
								layer.alert("抱歉,出金申请不能大于可取金额!");
								window.location.href = "{{ url('/user/index') }}";
							} else if (data.err == "more_than_sys_val") {
								layer.alert("抱歉,单笔出金金额不能大于系统预设最大金额!");
								window.location.href = "{{ url('/user/index') }}";
							} else if (data.err == "SYSERR") {
								layer.alert("抱歉,因网络故障您的请求无法完成,请留意资金变动并联系客服!");
								window.location.href = "{{ url('/user/index') }}";
							}
						} else if (data.msg == 'SUC') {
							layer.msg("申请成功", {
								time: 200000, //20s后自动关闭
								btn: ['知道了'],
								yes: function (index, layero) {
									layer.close(index);
									closeLoadShade(index1);
									//parent.layer.closeAll();
									top.location.href = "/user/index";
								},
								end:function () {
									top.location.href = "/user/index";
								}
							});
						}
					},
					error: function () {
						closeLoadShade(index1);
						layer.msg("未知错误,请尝试刷新重新操作或联系客服.", {
							time: 200000, //20s后自动关闭
							btn: ['知道了'],
							yes: function (index, layero) {
								layer.close(index);
								closeLoadShade(index1);
								top.location.href = "/user/index";
							},
							end:function () {
								top.location.href = "/user/index";
							}
						});
					}
				});
			}
		}
	</script>
@endsection