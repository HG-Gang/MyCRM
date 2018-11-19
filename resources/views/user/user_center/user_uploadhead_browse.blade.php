@extends('user.layout.main_right')

@section('public-resources')
    <style>
        .a-upload{padding: 4px 10px;height: 27px;line-height: 28px;position: relative;top: 12px;left: -14px;cursor: pointer;color: #888;background: #fafafa;border: 1px solid #ddd;border-radius: 4px;overflow: hidden;display: inline-block;*display: inline;*zoom: 1;}
        .a-upload input{position: absolute;font-size: 100px;right: 0;top: 0;opacity: 0;filter: alpha(opacity=0);cursor: pointer;}
        .a-upload:hover{color: #444;background: #eee;border-color: #ccc;text-decoration: none;}
    </style>
@endsection

@section('content')
    <form class="layui-form" action="" id="UserUpLoadHeadForm" style="margin-top: 8px;">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">用户头像</label>
                <div class="layui-input-block">
                    <input type="text" name="headimg" id="headimg" autocomplete="off" class="layui-input" placeholder="支持JPG,JPEG,PNG格式且小于2M" readonly="readonly" style="width: 270px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
            <a href="javascript:void(0);" class="a-upload"><input type="file" name="file_img" id="file_img" onchange="BankCardfillFile()">选择文件</a>
            <div class="layui-form-mid">&nbsp;&nbsp;</div>

                <button type="button" class="layui-btn" onclick="ajaxUploadHeadImg()">提交资料</button>
        </div>
    </form>
    <div class="layer-photos-demo" style="margin-left: 30px; display: none;">
        <img id="image" src="" style="width: 450px; height: 300px; border: 1px solid #303030;">
    </div>
@endsection

@section('custom-resources')
    <script type="text/javascript">
        function BankCardfillFile() {
            var file = $("#file_img")[0].files[0];
            var bankCard = $("input[name='file_img']").val().lastIndexOf("\\");
            $("#headimg").val($("input[name='file_img']").val().substr(bankCard + 1));
            console.log(file.size);
            //图片大小的限制
            if(file.size > (2 * 1024 * 1024)) {
                alert("请上传小于少于2M的图片");
            }
            //获取img对象
            var img = document.getElementById("image");
            //建一条文件流来读取图片
            var reader = new FileReader();
            //根据url将文件添加的流中
            reader.readAsDataURL(file);
            //实现onload接口
            reader.onload = function(e) {
                //获取文件在流中url
                url = reader.result;
                //将url赋值给img的src属性
                img.src = url;
                $(".layer-photos-demo").css("display", "block");
            };

        }

        function ajaxUploadHeadImg() {
            var formData = new FormData();
            formData.append("_token", "{{ csrf_token() }}");
            formData.append("headimg", $("#file_img")[0].files[0]); //正面照

            if (checkheadimg()) {
                var index = openLoadShade();
                $.ajax({
                    url: "/user/center/uploadHeadImg",
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
                                errorTips("头像照片不能超过2M", "msg", data.col);
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
                                //time: 20000, //20s后自动关闭
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