@extends('user.layout.main_right')

@section('content')
    <form class="layui-form" action="" id="AdminAgentsListForm" style="margin-top: 8px;">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">账户ID</label>
                <div class="layui-input-block">
                    <input type="text" name="userId" id="userId" value="{{ $ag_info['user_id'] }}" autocomplete="off" placeholder="请输入账户ID" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">账户姓名</label>
                <div class="layui-input-block">
                    <input type="text" name="username" id="username" value="{{ $ag_info['user_name'] }}"autocomplete="off" placeholder="请输入账户姓名" class="layui-input">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">账户密码</label>
                <div class="layui-input-block">
                    <input type="password" name="password" id="password" value="********"autocomplete="off" placeholder="请输入账户密码" class="layui-input" style="width: 200px;">
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">身份证号</label>
                <div class="layui-input-block">
                    <input type="text" name="userIdcardNo" id="userIdcardNo" value="{{ $ag_info['IDcard_no'] }}" autocomplete="off" placeholder="请输入身份证号" class="layui-input" style="width: 200px;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">手机号</label>
                <div class="layui-input-block">
                    <input type="text" name="userphoneNo" id="userphoneNo" value="{{ substr($ag_info['phone'], (stripos($ag_info['phone'], '-') + 1)) }}"autocomplete="off" placeholder="请输入手机号" class="layui-input" style="width: 200px;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">邮箱</label>
                <div class="layui-input-block">
                    <input type="text" name="useremail" id="useremail" value="{{ $ag_info['email'] }}"autocomplete="off" placeholder="请输入邮箱" class="layui-input" style="width: 200px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">用户组别</label>
                <div class="layui-input-inline" style="width: 200px; margin-right: 0px;">
                    <select name="usergrpId" id="usergrpId">
                        <option value="">请选用户组别</option>
                        @foreach($ag_lvl as $key => $vdata)
                            @if($ag_info['mt4_grp'] == $vdata['user_group_name'])
                                <option value="{{ $vdata['user_group_id'] }}" selected="selected">{{ $vdata['user_group_name'] }}</option>
                            @else
                                <option value="{{ $vdata['user_group_id'] }}">{{ $vdata['user_group_name'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">账户模式</label>
                <div class="layui-input-inline" style="width: 200px; margin-right: 0px;">
                    <select name="usertype" id="usertype" lay-filter="trands_mode">
                        <option value="">请选择账户模式</option>
                        @if($ag_info['trans_mode'] == '0')
                            <option value="0" selected="selected">返佣模式</option>
                        @else
                            <option value="0">返佣模式</option>
                        @endif
                        @if($ag_info['trans_mode'] == '1')
                            <option value="1" selected="selected">权益模式</option>
                        @else
                            <option value="1">权益模式</option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">账户权益</label>
                <div class="layui-input-block">
                    <input type="text" name="userrights" id="userrights" value="{{ $ag_info['rights'] }}" autocomplete="off" placeholder="请输入账户权益" class="layui-input" style="width: 200px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">结算周期</label>
                <div class="layui-input-inline" style="width: 200px; margin-right: 0px;" id="select_enabled">
                    <select name="usercycle" id="usercycle">
                        <option value="">请选择结算周期</option>
                        @if($ag_info['cycle'] == '1')
                            <option value="1" selected="selected">周结</option>
                        @else
                            <option value="1">周结</option>
                        @endif
                        @if($ag_info['cycle'] == '2')
                            <option value="2" selected="selected">半月结</option>
                        @else
                            <option value="2">半月结</option>
                        @endif
                        @if($ag_info['cycle'] == '3')
                            <option value="3" selected="selected">月结</option>
                        @else
                            <option value="3">月结</option>
                        @endif
                    </select>
                </div>
                <div class="layui-input-inline" style="width: 200px; display: none; margin-right: 0px;" id="select_disabled">
                    <select name="usercycle" id="usercycle" disabled>
                        <option value="">请选择结算周期</option>
                        @if($ag_info['cycle'] == '1')
                            <option value="1" selected="selected">周结</option>
                        @else
                            <option value="1">周结</option>
                        @endif
                        @if($ag_info['cycle'] == '2')
                            <option value="2" selected="selected">半月结</option>
                        @else
                            <option value="2">半月结</option>
                        @endif
                        @if($ag_info['cycle'] == '3')
                            <option value="3" selected="selected">月结</option>
                        @else
                            <option value="3">月结</option>
                        @endif
                    </select>
                </div>
            </div>
			<div class="layui-inline">
                <label class="layui-form-label">交易杠杆</label>
                <div class="layui-input-block">
                    <input type="text" name="cust_lvg" id="cust_lvg" value="{{ $ag_info['cust_lvg'] }}" autocomplete="off" onkeyup="value=value.replace(/[^\d]/g,'')" placeholder="请输入交易杠杆" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">上级代理</label>
                <div class="layui-input-block">
                    <input type="text" name="userparentId" id="userparentId" value="{{ $ag_info['parent_id'] }}" autocomplete="off" onkeyup="value=value.replace(/[^\d]/g,'')" placeholder="请输入上级代理" class="layui-input" style="width: 200px;">
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">代理级别</label>
                <div class="layui-input-inline" style="width: 200px; margin-right: 0px;">
                    <select name="useragtId" id="useragtId">
                        <option value="">请选择代理级别</option>
                        @foreach($ag_grp as $key => $vdata)
                            @if($ag_info['group_id'] == $vdata['group_id'])
                                <option value="{{ $vdata['group_id'] }}" selected="selected">{{ $vdata['group_name'] }}</option>
                            @else
                                <option value="{{ $vdata['group_id'] }}">{{ $vdata['group_name'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">返佣比例</label>
                <div class="layui-input-block">
                    <input type="text" name="userrebate" id="userrebate" value="{{ $ag_info['comm_prop'] }}" onkeyup="value=value.replace(/[^\d]/g,'')" maxlength="3" autocomplete="off" placeholder="请输入返佣比例" class="layui-input" style="width: 200px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">账户状态</label>
                <div class="layui-input-block">
                    @if($ag_info['enable'] == 1)
                        {{--勾上，能登录和交易--}}
                        <input type="checkbox" name="enable" id="enable" value="1" lay-skin="primary" title="启用" checked="">
                    @else
                        {{--未勾上，不能登录--}}
                        <input type="checkbox" name="enable" id="enable" value="0" lay-skin="primary" title="启用">
                    @endif
                    @if($ag_info['enable_readonly'] == 1)
                        {{--勾上，只读 能登录 不能交易--}}
                        <input type="checkbox" name="enable_readonly" value="1" id="enable_readonly" lay-skin="primary" title="只读" checked="">
                    @else
                        {{--未勾上，能登录 交易--}}
                        <input type="checkbox" name="enable_readonly" value="0" id="enable_readonly" lay-skin="primary" title="只读">
                    @endif
                    @if($ag_info['is_out_money'] == 1)
                        {{--勾上，不允许出金--}}
                        <input type="checkbox" name="is_out_money" value="1" id="is_out_money" lay-skin="primary" title="出金锁定" checked="">
                    @else
                        {{--未勾上，允许出金--}}
                        <input type="checkbox" name="is_out_money" value="0" id="is_out_money" lay-skin="primary" title="出金锁定">
                    @endif
                </div>
            </div>
            {{--TODO parentId=0,有选择权利？　还是他的下级也有可以选择的权利？--}}
            <div class="layui-inline">
                <label class="layui-form-label">结算模式</label>
                <div class="layui-input-block">
                    @if($ag_info['parent_id'] == 0)
                        @if($ag_info['settlement_model'] == '1')
                            <input type="radio" name="settlement_model" id="settlement_model_1" value="1" lay-skin="primary" title="线上模式" checked="">
                        @else
                            <input type="radio" name="settlement_model" id="settlement_model_1" value="1" lay-skin="primary" title="线上模式">
                        @endif
                        @if($ag_info['settlement_model'] == '2')
                            <input type="radio" name="settlement_model" id="settlement_model_2" value="2" lay-skin="primary" title="线下模式" checked="">
                        @else
                            <input type="radio" name="settlement_model" id="settlement_model_2" value="2" lay-skin="primary" title="线下模式">
                        @endif
                    @else
                        @if($ag_info['settlement_model'] == '1')
                            <input type="radio" name="settlement_model" id="settlement_model_1" value="1" lay-skin="primary" title="线上模式" checked="" disabled>
                        @else
                            <input type="radio" name="settlement_model" id="settlement_model_1" value="1" lay-skin="primary" title="线上模式" disabled>
                        @endif
                        @if($ag_info['settlement_model'] == '2')
                            <input type="radio" name="settlement_model" id="settlement_model_2" value="2" lay-skin="primary" title="线下模式" checked="" disabled>
                        @else
                            <input type="radio" name="settlement_model" id="settlement_model_2" value="2" lay-skin="primary" title="线下模式" disabled>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">开户时间</label>
                <div class="layui-input-inline">
                    <input type="text" name="reccrtdate" id="reccrtdate" value="{{ $ag_info['rec_crt_date'] }}" autocomplete="off" placeholder="请输入开户时间" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">备注</label>
                <div class="layui-input-block">
                    <input type="text" name="userremark" id="userremark" value="{{ $ag_info['remark'] }}" autocomplete="off" placeholder="请输入备注" class="layui-input" style="width: 530px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">层级关系</label>
                <div class="layui-input-block">
                    <input type="text" name="usercountry" id="usercountry" value="{{ $ag_info['country'] }}" autocomplete="off" placeholder="请输入层级关系" class="layui-input" style="width: 856px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="button" class="layui-btn" onclick="agentsSave_1('{{ csrf_token() }}')">确定</button>
            </div>
        </div>
    </form>
@endsection

@section('custom-resources')
    <script type="text/javascript">
        $(function () {
            init_select_status();
        });

        $("#password").on('keydown', function (e) {
            if (e.keyCode == 8) {
                $(this).val("");
            }
        });

        function agentsSave_1(token) {
            if (username() && password() && userIdcardNo() && userphoneNo()
                && useremail() && check_user_grp() && check_user_agtId()
                && check_user_rebate() && check_user_type() && check_userparentId()
                && check_cust_lvg()
            ) {
                agentsSave(token, "{{url(route_prefix() . '/agents_list')}}");
            }
        }
    </script>
@endsection