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
				<label class="layui-form-label">姓名</label>
				<div class="layui-input-block">
					<input type="text" name="username" id="username" autocomplete="off" placeholder="请输入姓名" class="layui-input" style="width: 270px;">
				</div>
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">身份证号</label>
				<div class="layui-input-block">
					<input type="text" name="userIdcardNo" id="userIdcardNo" autocomplete="off" placeholder="请输入身份证号" class="layui-input" style="width: 270px;">
				</div>
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">正面照</label>
				<div class="layui-input-block">
					<input type="text" name="Idphoto1" id="Idphoto1" autocomplete="off" class="layui-input" placeholder="支持JPG,JPEG,PNG格式且小于2M" readonly="readonly" style="width: 270px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
				</div>
			</div>
			<a href="javascript:void(0);" class="a-upload"><input type="file" name="file_img1" id="file_img1" onchange="Idphoto1fillFile()">选择文件</a>
		</div>
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">反面照</label>
				<div class="layui-input-block">
					<input type="text" name="Idphoto2" id="Idphoto2" autocomplete="off" class="layui-input" placeholder="支持JPG,JPEG,PNG格式且小于2M" readonly="readonly" style="width: 270px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
				</div>
			</div>
			<a href="javascript:void(0);" class="a-upload"><input type="file" name="file_img2" id="file_img2" onchange="Idphoto2fillFile()">选择文件</a>
		</div>
		<div class="layui-form-item">
			<div class="layui-input-block">
				<button type="button" class="layui-btn" onclick="ajaxUploadAuthIdCard()">提交资料</button>
			</div>
		</div>
	</form>
@endsection

@section('custom-resources')
	<script type="text/javascript">
		function Idphoto1fillFile() {
			var Idphoto1 = $("input[name='file_img1']").val().lastIndexOf("\\");
			$("#Idphoto1").val($("input[name='file_img1']").val().substr(Idphoto1+1));
		}
		function Idphoto2fillFile() {
			var Idphoto2 = $("input[name='file_img2']").val().lastIndexOf("\\");
			$("#Idphoto2").val( $("input[name='file_img2']").val().substr(Idphoto2 + 1) );
		}
		
		function checkphoto() {
			var Idphoto1 = $("#Idphoto1").val(); //反面照
			var Idphoto2 = $("#Idphoto2").val(); //正面照
			
			if (Idphoto1 == "") {
				errorTips("请上传身份证正面照!", "msg", "Idphoto1");
			} else if (Idphoto2 == "") {
				errorTips("请上传身份证反面照!", "msg", "Idphoto2");
			}
			
			return true;
		}
		
		function ajaxUploadAuthIdCard() {
			var formData = new FormData();
			formData.append("username", $("#username").val());
			formData.append("userIdcardNo", $("#userIdcardNo").val());
			formData.append("_token", "{{ csrf_token() }}");
			formData.append("Idphoto1", $("#file_img1")[0].files[0]); //正面照
			formData.append("Idphoto2", $("#file_img2")[0].files[0]); //反面照
			formData.append("uploadType", "IdCardUpload");
			
			if (username() && userIdcardNo() && checkphoto()) {
				var index = openLoadShade();
				$.ajax({
					url: "/user/center/uploadIdCard",
					data: formData,
					processData: false,
					contentType: false,
					dateType: "JSON",
					type: "POST",
					//_token: "{{ csrf_token() }}",
					async: false,
					success: function(data) {
						closeLoadShade(index);
						if (data.msg == "FAIL") {
							if (data.err == "FATALCANOTCONNECT") {
								errorTips("网络故障,请稍后再上传...", "msg", "username");
							} else if (data.err == "POSOVERSIZE1") {
								errorTips("正面照不能超过2M", "msg", data.col);
							} else if (data.err == "POSERRORFORMAT1") {
								errorTips("请上传支持的图片格式!", "msg", data.col);
							} else if (data.err == "POSOVERSIZE2") {
								errorTips("反面照不能超过2M", "msg", data.col);
							} else if (data.err == "POSERRORFORMAT2") {
								errorTips("请上传支持的图片格式!", "msg", data.col);
							} else if (data.err == "uploadErr") {
								console.log(data.col);
								layer.msg("图片上传失败", {icon: 5, shift: 6});
							} else if (data.err == "UPDATEFAIL") {
								errorTips("上传失败, 请稍后再试", "msg", "username");
							} else if (data.err == "IdcardNoExiste") {
                                errorTips("身份证号已经存在!", "msg", data.col);
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