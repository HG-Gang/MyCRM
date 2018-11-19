@extends('user.layout.main_right')

@section('public-resources')
@endsection

@section('content')
    <form class="layui-form" action="" id="AdminCustListForm" style="margin-top: 8px;">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">账户ID</label>
                <div class="layui-input-block">
                    <input type="text" name="userId" id="userId" value="{{ $_acc_info['user_id'] }}" autocomplete="off" placeholder="请输入账户ID" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">账户姓名</label>
                <div class="layui-input-block">
                    <input type="text" name="username" id="username" value="{{ $_acc_info['user_name'] }}"autocomplete="off" placeholder="请输入账户姓名" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">账户密码</label>
                <div class="layui-input-block">
                    <input type="password" name="password" id="password" value="********"autocomplete="off" placeholder="请输入账户密码" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">身份证号</label>
                <div class="layui-input-block">
                    <input type="text" name="userIdcardNo" id="userIdcardNo" value="{{ $_acc_info['IDcard_no'] }}" autocomplete="off" placeholder="请输入身份证号" class="layui-input" style="width: 200px;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">手机号</label>
                <div class="layui-input-block">
                    <input type="text" name="userphoneNo" id="userphoneNo" value="{{ substr_replace(substr($_acc_info['phone'], (stripos($_acc_info['phone'], '-') + 1)), '*****', 3, -3) }} "autocomplete="off" placeholder="请输入手机号" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">邮箱</label>
                <div class="layui-input-block">
                    <input type="text" name="useremail" id="useremail" value="{{ substr_replace($_acc_info['email'], '*****', 3, (stripos($_acc_info['email'], '@') - 3)) }} "autocomplete="off" placeholder="请输入邮箱" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
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
                            @if($_acc_info['mt4_grp'] == $vdata['user_group_name'])
                                <option value="{{ $vdata['user_group_id'] }}" selected="selected">{{ $vdata['user_group_name'] }}</option>
                            @else
                                <option value="{{ $vdata['user_group_id'] }}">{{ $vdata['user_group_name'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">交易杠杆</label>
                <div class="layui-input-block">
                    <input type="text" name="cust_lvg" id="cust_lvg" value="{{ $_acc_info['cust_lvg'] }}" autocomplete="off" onkeyup="value=value.replace(/[^\d]/g,'')" placeholder="请输入交易杠杆" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">上级代理</label>
                <div class="layui-input-block">
                    <input type="text" name="userparentId" id="userparentId" value="{{ $_acc_info['parent_id'] }}" autocomplete="off" onkeyup="value=value.replace(/[^\d]/g,'')" placeholder="请输入上级代理" class="layui-input" style="width: 200px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">账户状态</label>
            <div class="layui-input-block">
                @if($_acc_info['enable'] == 1)
                    {{--勾上，能登录和交易--}}
                    <input type="checkbox" name="enable" id="enable" value="1" lay-skin="primary" title="启用" checked="">
                @else
                    {{--未勾上，不能登录--}}
                    <input type="checkbox" name="enable" id="enable" value="0" lay-skin="primary" title="启用">
                @endif
                @if($_acc_info['enable_readonly'] == 1)
                    {{--勾上，只读 能登录 不能交易--}}
                    <input type="checkbox" name="enable_readonly" value="1" id="enable_readonly" lay-skin="primary" title="只读" checked="">
                @else
                    {{--未勾上，能登录 交易--}}
                    <input type="checkbox" name="enable_readonly" value="0" id="enable_readonly" lay-skin="primary" title="只读">
                @endif
                @if($_acc_info['is_out_money'] == 1)
                    {{--勾上，不允许出金--}}
                    <input type="checkbox" name="is_out_money" value="1" id="is_out_money" lay-skin="primary" title="出金锁定" checked="">
                @else
                    {{--未勾上，允许出金--}}
                    <input type="checkbox" name="is_out_money" value="0" id="is_out_money" lay-skin="primary" title="出金锁定">
                @endif
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">开户时间</label>
                <div class="layui-input-inline">
                    <input type="text" name="reccrtdate" id="reccrtdate" value="{{ $_acc_info['rec_crt_date'] }}" autocomplete="off" placeholder="请输入开户时间" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">备注</label>
                <div class="layui-input-block">
                    <input type="text" name="userremark" id="userremark" value="{{ $_acc_info['remark'] }}" autocomplete="off" placeholder="请输入备注" class="layui-input" style="width: 530px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">层级关系</label>
                <div class="layui-input-block">
                    <input type="text" name="usercountry" id="usercountry" value="{{ $_acc_info['country'] }}" autocomplete="off" placeholder="请输入层级关系" class="layui-input" style="width: 856px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="button" class="layui-btn" onclick="custSave_2('{{ csrf_token() }}')">确定</button>
            </div>
        </div>
    </form>
@endsection

@section('custom-resources')
    <script type="text/javascript">
        $("#password").on('keydown', function (e) {
            if (e.keyCode == 8) {
                $(this).val("");
            }
        });

        function custSave_2(token) {
            if (userIdcardNo() && check_user_grp() && check_user_agtId())
            {
                custSave(token, "{{url(route_prefix() . '/cust/list')}}");
            }
        }
    </script>
@endsection