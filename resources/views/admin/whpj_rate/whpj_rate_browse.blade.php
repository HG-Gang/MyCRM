@extends('user.layout.main_right')

@section('public-resources')

@endsection

@section('content')
    <form class="layui-form" action="" id="AdminWhpjRateForm" style="margin-top: 8px;">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">存款汇率</label>
                <div class="layui-input-block">
                    <input type="text" name="sys_deposit_rate" id="sys_deposit_rate" autocomplete="off" placeholder="请输入存款汇率" class="layui-input" style="width: 200px;">
                </div>
                <p class="layui-form-mid layui-word-aux" style="left: 37px;top: 8px; line-height: 0px;">
                    当前存款汇率: {{ $sys_info['sys_deposit_rate'] }}
                </p>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">取款汇率</label>
                <div class="layui-input-block">
                    <input type="text" name="sys_draw_rate" id="sys_draw_rate" autocomplete="off" placeholder="请输入取款汇率" class="layui-input" style="width: 200px;">
                </div>
                <p class="layui-form-mid layui-word-aux" style="left: 37px;top: 8px; line-height: 0px;">
                    当前取款汇率: {{ $sys_info['sys_draw_rate'] }}
                </p>
            </div>
            <button type="button" class="layui-btn" onclick="whpj_save()">保存</button>
        </div>
    </form>
    <div style="margin-left: 34px;">
        <span>中国银行(香港)</span>
        <span><a href="http://services1.aastocks.com/WEB/BCHK/BOCHK/mktinfo.aspx?BCHKLanguage=chn&pagetype=public" style="font-weight: unset; color: blue; text-decoration: underline;" target="_blank">http://services1.aastocks.com/WEB/BCHK/BOCHK/mktinfo.aspx?BCHKLanguage=chn&pagetype=public</a></span>
    </div>
@endsection

@section('custom-resources')
    <script type="text/javascript">
        function whpj_save() {
            var sys_deposit_rate = $("#sys_deposit_rate").val();
            var sys_draw_rate = $("#sys_draw_rate").val();

            if (sys_deposit_rate == "") {
                errorTips("存款汇率不能为空!", "msg", "sys_deposit_rate");
            } else if (sys_draw_rate == "") {
                errorTips("取款汇率不能为空!", "msg", "sys_draw_rate");
            } else {
                var index1 = openLoadShade();
                $.ajax({
                    url: route_prefix() + '/amount/whpj_rate_save',
                    data: {
                        //sys_poundage_money: sys_poundage_money, //出金手续费
                        sys_deposit_rate: sys_deposit_rate, //入金汇率
                        sys_draw_rate: sys_draw_rate, //取款汇率
                        _token: "{{ csrf_token() }}",
                    },
                    dateType: "JSON",
                    type: "POST",
                    async: true,
                    success: function (data) {
                        closeLoadShade(index1);
                        if (data.msg == 'SUC') {
                            layer.msg("更新成功", {
                                time: 20000, //20s后自动关闭
                                btn: ['知道了'],
                                yes: function (index, layero) {
                                    location.href = "{{url(route_prefix() . '/amount/whpj_rate')}}";
                                }
                            });
                        } else {
                            layer.msg("更新失败", {
                                time: 20000, //20s后自动关闭
                                btn: ['知道了'],
                                yes: function (index, layero) {
                                    layer.closeAll();
                                }
                            });
                        }

                    },
                    error: function (data) {
                        //layer.close(index);
                        layer.msg('系统错误,请刷新页面重新操作');
                    }
                });
            }
        }
    </script>
@endsection