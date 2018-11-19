<!DOCTYPE html>
<html>
    <head>
        <title>这是系统发送的邮件, 请不要回复</title>
            <style>
                html, body { height: 100%;}
                body {
                        margin: 0;
                        padding: 0;
                        width: 100%;
                        display: table;
                        font-weight: 100;
                        font-family: 'Lato';
                        background-color: #e4f6ff;
                }
            </style>
    </head>
        <body>
            <table>
                <tr>
                    <td>您的验证码为: {{ $vcode }}</td>
                    {{--<td>{{ $user->user_name }}</td>--}}
                </tr>
                <tr>
                    {{--<td>您的登录账号: </td>--}}
                    {{--<td>{{ $user->user_id }}</td>--}}
                </tr>
                <tr>
                    {{--<td colspan="2">请点击链接来激活你的登录账户</td>
                    <td style="display: none"></td>--}}
                </tr>
                <tr>
                   {{-- <td colspan="2">{{ URL::asset('registerinto/confirmmail') }}/{{ base64_encode($user->user_id) }}/{{ base64_encode($user->password) }}/{{ time() }}</td>--}}
                    {{--<td style="display: none"></td>--}}
                </tr>
                <tr>
                    {{--<td colspan="2">这是一封确认注册成功邮件, 请不要回复, 有效期10分钟(600秒)</td>
                    <td style="display: none"></td>--}}
                </tr>
            </table>
        </body>
</html>
