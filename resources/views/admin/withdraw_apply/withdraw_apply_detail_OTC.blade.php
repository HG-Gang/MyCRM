@extends('user.layout.main_right')

@section('public-resources')
@endsection

@section('content')
    <form class="layui-form" action="" id="AdminWithdrawApplyDetailForm" style="margin-top: 8px;">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">订单号</label>
                <div class="layui-input-block">
                    <input type="text" name="mt4_trades_no" id="mt4_trades_no" value="{{ $_order_info['mt4_trades_no'] }}" autocomplete="off" placeholder="请输入订单号" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">交易账户</label>
                <div class="layui-input-block">
                    <input type="text" name="userId" id="userId" value="{{ $_order_info['user_id'] }}" autocomplete="off" placeholder="请输入交易账户" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">账户姓名</label>
                <div class="layui-input-block">
                    <input type="text" name="user_name" id="user_name" value="{{ $_order_info['user_name'] }}" autocomplete="off" placeholder="请输入交易账户" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">申请金额/USD</label>
                <div class="layui-input-block">
                    <input type="text" name="apply_amount" id="apply_amount" value="{{ $_order_info['apply_amount'] }}" autocomplete="off" placeholder="请输入申请金额" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">实际金额/RMB</label>
                <div class="layui-input-block">
                    <input type="text" name="act_apply_amount" id="act_apply_amount" value="{{ $_order_info['act_draw'] }}" autocomplete="off" placeholder="请输入实际金额" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">手续费</label>
                <div class="layui-input-block">
                    <input type="text" name="act_pdg_rmb" id="act_pdg_rmb" value="{{ $_order_info['draw_poundage'] }}" autocomplete="off" placeholder="请输入手续费" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">银行卡号</label>
                <div class="layui-input-block">
                    <input type="text" name="draw_bank_no" id="draw_bank_no" value="{{ $_order_info['draw_bank_no'] }}" autocomplete="off" placeholder="请输入银行卡号" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">银行名称</label>
                <div class="layui-input-block">
                    <input type="text" name="userId" id="userId" value="{{ $_order_info['draw_bank_class'] }}" autocomplete="off" placeholder="请输入银行名称" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">出金汇率</label>
                <div class="layui-input-block">
                    <input type="text" name="draw_rate" id="draw_rate" value="{{ $_order_info['draw_rate'] }}" autocomplete="off" placeholder="请输入出金汇率" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">开户行地址</label>
                <div class="layui-input-block">
                    <input type="text" name="userId" id="userId" value="{{ $_order_info['draw_bank_info'] }}" autocomplete="off" placeholder="请输入开户行地址" class="layui-input" readonly="readonly" style="width: 600px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">处理状态</label>
            <div class="layui-input-block">
                @if($_order_info['apply_status'] == 0)
                    <input type="radio" name="apply_status" value="0" title="待处理" checked disabled>
                @else
                    <input type="radio" name="apply_status" value="0" title="待处理">
                @endif
                @if($_order_info['orderId_OTC'] != '')
                    @if($_order_info['apply_status'] == 2)
                        <input type="radio" name="apply_status" value="2" title="同意" checked>
                    @else
                        <input type="radio" name="apply_status" value="2" title="同意">
                    @endif
                @endif
                @if($_order_info['apply_status'] == 3)
                    <input type="radio" name="apply_status" value="3" title="拒绝" checked>
                @else
                    <input type="radio" name="apply_status" value="3" title="拒绝">
                @endif
            </div>
        </div>
        @if($_order_info['apply_status'] == 3)
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">失败原因</label>
                <div class="layui-input-block">
                    {{--<input type="text" id="apply_remark" name="apply_remark" value="{{ $_order_info['apply_remark'] }}" autocomplete="off" placeholder="请输入订单处理失败原因" class="layui-input" readonly="readonly" style="width: 600px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">--}}
                    <textarea id="apply_remark" name="apply_remark" autocomplete="off" placeholder="请输入订单处理失败原因" class="layui-textarea" readonly="readonly" style="width: 600px; resize: none; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">{{ $_order_info['apply_remark'] }}</textarea>
                </div>
            </div>
        @elseif($_order_info['apply_status'] == 0)
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">失败原因</label>
                <div class="layui-input-block">
                    {{--<input type="text" id="apply_remark" name="apply_remark" autocomplete="off" placeholder="请输入订单处理失败原因" class="layui-input" style="width: 600px;">--}}
                    <textarea id="apply_remark" name="apply_remark" autocomplete="off" placeholder="请输入订单处理失败原因" class="layui-textarea" style="width: 600px; resize: none;"></textarea>
                </div>
            </div>
        @endif
        @if($_order_info['apply_status'] == 0 && $_order_info['orderId_OTC_status'] == '')
            <div class="layui-form-item">
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" id="confirmBtn" class="layui-btn" onclick="orderConfirm()">确认</button>
                    </div>
                </div>
            </div>
        @endif
    </form>
@endsection

@section('custom-resources')
    <script>
        $(function () {
            if ("{{ $_order_info['apply_status'] }}" != "0") {
                $("input:radio").attr("disabled",true);
            }
        });

        function check_order_status() {
            var idval = $("input[name='apply_status']:checked").val();
            if (idval == "0") {
                errorTips("请选择处理状态!", "msg");
            } else if (idval == "3" && $("#apply_remark").val() == "") {
                errorTips("请输入失败原因!", "msg", "apply_remark");
            } else {
                return true;
            }
        }

        function orderConfirm() {
            if (!$("#confirmBtn").hasClass("layui-btn-disabled")) {
                if (check_order_status()) {
                    var index1 = openLoadShade();
                    $("#confirmBtn").addClass("layui-btn-disabled");
                    $.ajax({
                        url: route_prefix() + "/amount/order_status_OTC",
                        data: {
                            orderId:                "{{ $_order_info['record_id'] }}",
                            orderStatus:			$("input[name='apply_status']:checked").val(),
                            orderRemark:			$("#apply_remark").val(),
                            _token:					"{{ csrf_token() }}",
                        },
                        dateType: "JSON",
                        type: "POST",
                        async: false,
                        success: function(data) {
                            if (data.msg == "FAIL") {
                                closeLoadShade(index1);
                                if (data.err == "UPDATEFAIL") {
                                    layer.msg("操作失败", {
                                        time: 20000, //20s后自动关闭
                                        btn: ['知道了'],
                                        yes: function (index, layero) {
                                            parent.layer.closeAll();
                                            parent.window.location.href = "{{url(route_prefix() . '/amount/withdraw_apply')}}";
                                        }
                                    });
                                } else if (data.err == "invalidValue") {
                                    errorTips("无效的处理状态!", "msg", data.col);
                                } else if (data.err == "OTCWITHDRAWFAIL") {
                                    console.log(data.col);
                                    errorTips("OTC验证订单失败,请联系技术人员!", "msg");
                                }
                            } else if (data.msg == "SUC") {
                                console.log(data.col);
                                closeLoadShade(index1);
                                layer.msg("操作成功", {
                                    time: 20000, //20s后自动关闭
                                    btn: ['知道了'],
                                    yes: function (index, layero) {
                                        parent.layer.closeAll();
                                        parent.window.createTable();
                                    }
                                });
                            }
                        },
                        error:function(data) {
                            closeLoadShade(index1);
                            alert("系统错误");
                        }
                    });
                }
            }
        }
    </script>
@endsection