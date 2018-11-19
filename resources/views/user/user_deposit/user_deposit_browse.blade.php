@extends('user.layout.main_right')

@section('public-resources')
    <link rel="stylesheet" href="{{ URL::asset('css/icon-bank.css') }}?ver={{ resource_version_number() }}"/>
@endsection

@section('content')
    {{--@if($_user_info['IDcard_status'] != '2')
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
        <form class="" action="{{ URL::asset('/user/deposit_request') }}" method="post" id="userDepositForm" name="userDepositForm" target="_blank" style="margin-top: 20px; margin-left: 20%;">
            {!! csrf_field() !!}
            <div class="layui-form-item">
                <span class="layui-form-label">存款账户</span>
                <div class="layui-input-block">
                    <input type="text" name="userId" id="userId" value="{{ $_user_info['user_id'] }}" autocomplete="off" placeholder="请输入交易账户" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff;cursor:text;font-size: 15px; font-weight: 100;">
                </div>
            </div>
            <div class="layui-form-item">
                <span class="layui-form-label">存款金额</span>
                <div class="layui-input-inline">
                    <input type="text" name="deposit_amt" id="deposit_amt" oninput="depoActAmount()" autocomplete="off" placeholder="请输入存款金额" class="layui-input" style="width: 200px;">
                </div>
                <div class="layui-form-mid"> RMB </div>
            </div>
            <div class="layui-form-item">
                <span class="layui-form-label">当前汇率</span>
                <div class="layui-input-block">
                    <input type="text" name="deposit_rate" id="deposit_rate" value="{{ $_sys_conf['sys_deposit_rate'] }}" autocomplete="off" placeholder="请输入当前汇率" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff;cursor:text;font-size: 15px; font-weight: 100;">
                </div>
            </div>
            <div class="layui-form-item">
                <span class="layui-form-label">实际到账</span>
                <div class="layui-input-inline">
                    <input type="text" name="deposit_act_amt" id="deposit_act_amt" autocomplete="off" placeholder="" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff;cursor:text;font-size: 15px; font-weight: 100;">
                </div>
                <div class="layui-form-mid"> USD </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline" style="display: block;">
                    <span class="layui-form-label" style="cursor: pointer; text-align: center;" id="tongdao1" {{--onclick="tongdaoYI()"--}}>通道一</span>
                </div>
                <div class="layui-inline" style="display: none;">
                    <span class="layui-form-label" style="cursor: pointer; text-align: center;" id="tongdao2" onclick="tongdaoER()">通道二</span>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline" id="gateway1">
                    <div id="pay_type_gateway">
                        <input type="hidden" id="pay_gateway" name="pay_gateway">
                        <input type="hidden" id="pay_channel" name="pay_channel">
                        {{--<div style="margin-top: 4px;">
                            <label for="ICBC">
                                <input type="radio" name="gateway_bank" id="ICBC" style="width: 16px;">
                                <i class="icon-bank icon-icbc"></i>
                                工商银行
                            </label>
                            <label for="ABC">
                                <input type="radio" name="gateway_bank" id="ABC" style="width: 16px;">
                                <i class="icon-bank icon-agricultural"></i>
                                农业银行
                            </label>
                            <label for="CCB">
                                <input type="radio" name="gateway_bank" id="CCB" style="width: 16px;">
                                <i class="icon-bank icon-construction"></i>
                                建设银行
                            </label>
                            <label for="CMBCHINA">
                                <input type="radio" name="gateway_bank" id="CMBCHINA" style="width: 16px;">
                                <i class="icon-bank icon-merchants"></i>
                                招商银行
                            </label>
                            --}}{{--<label for="BOCO">
                                <input type="radio" name="gateway_bank" id="BOCO" style="width: 16px;">
                                <i class="icon-bank icon-communications"></i>
                                交通银行
                            </label>--}}{{--
                            <label for="PINGANBANK">
                                <input type="radio" name="gateway_bank" id="PINGANBANK" style="width: 16px;">
                                <i class="icon-bank icon-pingan"></i>
                                平安银行
                            </label>
                            <label for="SHB">
                                <input type="radio" name="gateway_bank" id="SHB" style="width: 16px;">
                                <i class="icon-bank icon-shanghai"></i>
                                上海银行
                            </label>
                        </div>--}}
                        <div class="layui-form-item">
                            {{--<label for="BCCB">
                                <input type="radio" name="gateway_bank" id="BCCB" style="width: 16px;">
                                <i class="icon-bank icon-beijing"></i>
                                北京银行
                            </label>--}}
                            {{--<label for="POST">
                                <input type="radio" name="gateway_bank" id="POST" style="width: 16px;">
                                <i class="icon-bank icon-postal"></i>
                                邮政银行
                            </label>--}}
                            {{--<label for="BOC">
                                <input type="radio" name="gateway_bank" id="BOC" style="width: 16px;">
                                <i class="icon-bank icon-china"></i>
                                中国银行
                            </label>--}}
                            {{--<label for="ECITIC" class="" style="height:32px;height: 32px; position: relative;top: 5px;">
                                <input type="radio" name="gateway_bank" id="ECITIC" style="width: 16px;margin-top:-7px">
                                <i class="icon-bank icon-citic"></i>
                                <span style="position: absolute;top: 8px;">中信银行</span>
                            </label>
                            <label for="CEB">
                                <input type="radio" name="gateway_bank" id="CEB" style="width: 16px;">
                                <i class="icon-bank icon-everbright"></i>
                                光大银行
                            </label>
                            <label for="HXB">
                                <input type="radio" name="gateway_bank" id="HXB" style="width: 16px;">
                                <i class="icon-bank icon-huaxia"></i>
                                华夏银行
                            </label>--}}
                            {{--<label for="GDB">
                                <input type="radio" name="gateway_bank" id="GDB" style="width: 16px;">
                                <i class="icon-bank icon-guangfa"></i>
                                广发银行
                            </label>--}}
                           {{-- <label for="CIB">
                                <input type="radio" name="gateway_bank" id="CIB" style="width: 16px;">
                                <i class="icon-bank icon-industrial"></i>
                                兴业银行
                            </label>--}}
                            {{--<label for="SPDB">
                                <input type="radio" name="gateway_bank" id="SPDB" style="width: 16px;">
                                <i class="icon-bank icon-pufa"></i>
                                浦发银行
                            </label>--}}
                            {{--<label for="CMBC">
                                <input type="radio" name="gateway_bank" id="CMBC" style="width: 16px;">
                                <i class="icon-bank icon-minsheng"></i>
                                民生银行
                            </label>--}}
                            {{--<label for="NBCB">
                                <input type="radio" name="gateway_bank" id="NBCB" style="width:16px;">
                                <i class="icon-bank icon-ningbo"></i>
                                宁波银行
                            </label>--}}
                           {{-- <label for="CZBANK">
                                <input type="radio" name="gateway_bank" id="CZBANK" style="width:16px;">
                                <i class="icon-bank icon-zheshang"></i>
                                浙商银行
                            </label>--}}
                        </div>
                    </div>
                </div>
                <div class="layui-inline" id="gateway2">
                    <div id="pay_type_gateway2">
                        <input type="hidden" id="pay_gateway2" name="pay_gateway2">
                        <input type="hidden" id="pay_channel2" name="pay_channel2">
                        <div style="margin-top: 4px;">
                            {{--<label for="BCCB">
                                <input type="radio" name="gateway_bank2" id="BCCB" style="width: 16px;">
                                <i class="icon-bank icon-beijing"></i>
                                北京银行
                            </label>--}}
                            <label for="POST">
                                <input type="radio" name="gateway_bank2" id="POST" style="width: 16px;">
                                <i class="icon-bank icon-postal"></i>
                                邮政银行
                            </label>
                            <label for="BOC">
                                <input type="radio" name="gateway_bank2" id="BOC" style="width: 16px;">
                                <i class="icon-bank icon-china"></i>
                                中国银行
                            </label>
                            {{--<label for="ECITIC" class="" style="height:32px;height: 32px; position: relative;top: 5px;">
                                <input type="radio" name="gateway_bank" id="ECITIC" style="width: 16px;margin-top:-7px">
                                <i class="icon-bank icon-citic"></i>
                                <span style="position: absolute;top: 8px;">中信银行</span>
                            </label>--}}
                            <label for="CEB">
                                <input type="radio" name="gateway_bank2" id="CEB" style="width: 16px;">
                                <i class="icon-bank icon-everbright"></i>
                                光大银行
                            </label>
                            {{--<label for="HXB">
                                <input type="radio" name="gateway_bank" id="HXB" style="width: 16px;">
                                <i class="icon-bank icon-huaxia"></i>
                                华夏银行
                            </label>--}}
                            {{--<label for="CGB">
                                <input type="radio" name="gateway_bank2" id="CGB" style="width: 16px;">
                                <i class="icon-bank icon-guangfa"></i>
                                广发银行
                            </label>--}}
                            {{--<label for="CIB2">
                                <input type="radio" name="gateway_bank2" id="CIB2" style="width: 16px;">
                                <i class="icon-bank icon-industrial"></i>
                                兴业银行
                            </label>--}}
                            {{--<label for="SPDB">
                                <input type="radio" name="gateway_bank" id="SPDB" style="width: 16px;">
                                <i class="icon-bank icon-pufa"></i>
                                浦发银行
                            </label>--}}
                            {{--<label for="CMBC">
                                <input type="radio" name="gateway_bank2" id="CMBC" style="width: 16px;">
                                <i class="icon-bank icon-minsheng"></i>
                                民生银行
                            </label>--}}
                            {{--<label for="NBCB">
                                <input type="radio" name="gateway_bank" id="NBCB" style="width:16px;">
                                <i class="icon-bank icon-ningbo"></i>
                                宁波银行
                            </label>
                            <label for="CZBANK2">
                                <input type="radio" name="gateway_bank2" id="CZBANK2" style="width:16px;">
                                <i class="icon-bank icon-zheshang"></i>
                                浙商银行
                            </label>--}}
                        </div>
                        <div class="layui-form-item">
                            <label for="ICBC">
                                <input type="radio" name="gateway_bank2" id="ICBC" style="width: 16px;">
                                <i class="icon-bank icon-icbc"></i>
                                工商银行
                            </label>
                            <label for="ABC">
                                <input type="radio" name="gateway_bank2" id="ABC" style="width: 16px;">
                                <i class="icon-bank icon-agricultural"></i>
                                农业银行
                            </label>
                            <label for="CCB">
                                <input type="radio" name="gateway_bank2" id="CCB" style="width: 16px;">
                                <i class="icon-bank icon-construction"></i>
                                建设银行
                            </label>
                            <label for="CMBCHINA">
                                <input type="radio" name="gateway_bank2" id="CMBCHINA" style="width: 16px;">
                                <i class="icon-bank icon-merchants"></i>
                                招商银行
                            </label>
                            {{--<label for="BOCO">
                                <input type="radio" name="gateway_bank2" id="BOCO" style="width: 16px;">
                                <i class="icon-bank icon-communications"></i>
                                交通银行
                            </label>--}}
                            {{--<label for="PINGANBANK2">
                                <input type="radio" name="gateway_bank2" id="PINGANBANK2" style="width: 16px;">
                                <i class="icon-bank icon-pingan"></i>
                                平安银行
                            </label>--}}
                            {{--<label for="SHB">
                                <input type="radio" name="gateway_bank2" id="SHB" style="width: 16px;">
                                <i class="icon-bank icon-shanghai"></i>
                                上海银行
                            </label>--}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button type="button" id="depositBtn" name="depositBtn" onclick="depositRequest()" class="layui-btn">确定</button>
                    <p style="display: none; margin-top: 10px; margin-left: -9%;" id="show_hide">温馨提示: 未能跳转支付页面<a href="javascript:openBlankSubmit()" style="color: red;">请点击这里</a></p>
                </div>
            </div>
            </div>
        </form>
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
        <p>1.通道一单笔入金最高20万人民币{{--，通道二单笔入金最高18万人民币--}}。</p>
       {{-- <p>1.通道一单笔入金最高18万人民币。</p>--}}
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
            tongdaoER();
        });
        /*function tongdaoYI() {
            $("#gateway1").css({"display":"none"});
            $("#gateway2").css({"display":"none"});
            $("#tongdao1").css({"background":"#1AA094", "color": "#fff"});
            $("#tongdao2").css({"background":"#fff", "color": "#000000"});
            $("#pay_channel2").val("");
            $("#pay_gateway2").val("");
            $("input[name='gateway_bank']:checked").attr('checked', false);
            $("#pay_channel").val("tongdaoYI");
        }*/
        function tongdaoER() {
            $("#gateway1").css("display", "none");
            $("#gateway2").css("display", "block");
            $("#tongdao1").css({"background":"#1AA094", "color": "#fff"});
            $("#tongdao2").css({"background":"#fff", "color": "#000000"});
            $("#pay_channel").val("");
            $("#pay_gateway").val("");
            $("input[name='gateway_bank2']:checked").attr('checked', false);
            $("#pay_channel2").val("tongdaoER");
        }
        function checkAmount() {
            var deposit_amount = $.trim($("#deposit_amt").val());
            var amountReg = /^[\d]+(\.[\d]{1,2})?$/;
            
            if ("{{ $_user_info['user_id'] }}" != "637001") {
                //TODO 还需要验证单笔最少存款金额及最大存款金额
                if (deposit_amount == "" || deposit_amount < 0) {
                    errorTips("请输入存款金额", "msg", 'deposit_amt')
                } else if (deposit_amount > 0 && !amountReg.test(deposit_amount)) {
                    errorTips("只允许最多两位正小数", "msg", "deposit_amount");
                } else if (deposit_amount != "" && Number(deposit_amount) < 700) {
                    errorTips("存款金额单笔最低700RMB", "msg", "deposit_amount");
                } else if (deposit_amount != "" && deposit_amount > 0 && deposit_amount > 200001 && $("#pay_channel").val() == "tongdaoYI") {
                    errorTips("通道一存款金额单笔最高20万RMB", "msg", "deposit_amount");
                } else if (deposit_amount != "" && deposit_amount > 0 && deposit_amount > 180000 && $("#pay_channel2").val() == "tongdaoER") {
                    errorTips("通道二存款金额单笔最高18万RMB", "msg", "deposit_amount");
                } else if (deposit_amount != "" && !(Number(deposit_amount) % 100 == 0)) {
                    errorTips("存款金额单笔必须是100的整倍数", "msg", "deposit_amount");
                } /*else if ((!$("input:radio[name='gateway_bank']").is(":checked")) && $("#pay_channel").val() == "tongdaoYI") {
                    layer.msg("请选择支付银行!", {time: 2000});
                    return;
                }*/ else if ((!$("input:radio[name='gateway_bank2']").is(":checked")) && $("#pay_channel2").val() == "tongdaoER") {
                    layer.msg("请选择支付银行!", {time: 2000});
                    return;
                } else {
                    return true;
                }
            } else {
                return true;
            }
            
        }

        function depositRequest() {
            if ($("#depositBtn").hasClass("layui-btn-disabled")) return;
            if (checkAmount()) {
                document.getElementById("userDepositForm").submit();
                $("#show_hide").show();
                $("#depositBtn").addClass('layui-btn-disabled');
            }
        }

        function openBlankSubmit() {
            if (checkAmount()) {
                var obj_data =$("#userDepositForm").serializeArray();
                var reqUrl = "{{ route('user_deposit_request') }}";
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
                if ("{{ $_user_info['user_id'] }}" != "637001") {
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