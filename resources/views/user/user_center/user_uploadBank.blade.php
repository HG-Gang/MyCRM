@extends('user.layout.main_right')

@section('public-resources')
    <style>
        .a-upload{padding: 4px 10px;height: 27px;line-height: 28px;position: relative;top: 12px;left: -14px;cursor: pointer;color: #888;background: #fafafa;border: 1px solid #ddd;border-radius: 4px;overflow: hidden;display: inline-block;*display: inline;*zoom: 1;}
        .a-upload input{position: absolute;font-size: 100px;right: 0;top: 0;opacity: 0;filter: alpha(opacity=0);cursor: pointer;}
        .a-upload:hover{color: #444;background: #eee;border-color: #ccc;text-decoration: none;}
    </style>
@endsection

@section('content')
    <form class="layui-form" action="" id="UserInfoForm" style="margin-top: 8px;">
        <div class="layui-form-item" enctype="multipart/form-data">
            <div class="layui-inline">
                <label class="layui-form-label">持卡人</label>
                <div class="layui-input-block">
                    <input type="text" name="username" id="username" value="{{ $_user_info['user_name'] }}" autocomplete="off" placeholder="请输入持卡人名称" class="layui-input" readonly="readonly" style="width: 270px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">开户银行</label>
                <div class="layui-input-block">
                    <input type="text" name="bankclass" id="bankclass" autocomplete="off" placeholder="请输入开户银行" class="layui-input" style="width: 270px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">银行卡号</label>
                <div class="layui-input-block">
                    <input type="text" name="bankno" id="bankno" autocomplete="off" placeholder="请输入银行卡号" class="layui-input" style="width: 270px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">开户支行</label>
                <div class="layui-input-block">
                    <input type="text" name="bankinfo" id="bankinfo" autocomplete="off" placeholder="请输入省、市、区(县)、支行" class="layui-input" style="width: 270px;">
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">卡号正面照</label>
                <div class="layui-input-block">
                    <input type="text" name="bankimg" id="bankimg" autocomplete="off" class="layui-input" placeholder="支持JPG,JPEG,PNG格式且小于2M" readonly="readonly" style="width: 270px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
            <a href="javascript:void(0);" class="a-upload"><input type="file" name="file_img" id="file_img" onchange="BankCardfillFile()">选择文件</a>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="button" class="layui-btn" onclick="ajaxUploadAuthBankCard()">提交资料</button>
            </div>
        </div>
    </form>
@endsection

@section('custom-resources')
    <script type="text/javascript">
        function BankCardfillFile() {
            var bankCard = $("input[name='file_img']").val().lastIndexOf("\\");
            $("#bankimg").val($("input[name='file_img']").val().substr(bankCard + 1));
        }

        function ajaxUploadAuthBankCard() {
            var formData = new FormData();
            formData.append("username", $("#username").val());
            formData.append("bankclass", $("#bankclass").val());
            formData.append("bankinfo", $("#bankinfo").val());
            formData.append("_token", "{{ csrf_token() }}");
            formData.append("bankno", $("#bankno").val());
            formData.append("bankimg", $("#file_img")[0].files[0]); //正面照
            formData.append("uploadType", "BankCardUpload");

            if (username() && checkphotoBankCard() && checkbankclass() && checkbankNo() && checkbankinfo() ) {
                var index = openLoadShade();
                $.ajax({
                    url: "/user/center/uploadBankCard",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dateType: "JSON",
                    type: "POST",
                    async: false,
                    success: function(data) {
                        closeLoadShade(index);
                        if (data.msg == "FAIL") {
                            if (data.err == "POSOVERSIZE1") {
                                errorTips("正面照不能超过2M", "msg", data.col);
                            } else if (data.err == "POSERRORFORMAT") {
                                errorTips("请上传支持的图片格式!", "msg", data.col);
                            } else if (data.err == "uploadErr") {
                                console.log(data.col);
                                layer.msg("图片上传失败", {icon: 5, shift: 6});
                            } else if (data.err == "UPDATEFAIL") {
                                errorTips("上传失败, 请稍后再试", "msg", data.col);
                            }
                        } else if (data.msg == "SUC") {
                            layer.msg("资料上传成功", {
                                time: 20000, //20s后自动关闭
                                btn: ['知道了'],
                                yes: function (index, layero) {
                                    parent.layer.closeAll();
                                    top.location = '/user/index';
                                }
                            });
                        }
                    },
                    error: function (data) {
                        closeLoadShade(index);
                        errorTips("单张图片不能超过2M", "msg");
                    }
                });
            }
        }
    </script>
@endsection