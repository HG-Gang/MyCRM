@extends('user.layout.main_right')

@section('public-resources')
@endsection

@section('content')
	<form class="layui-form" action="" id="AdminWithdrawBatchForm" style="margin-top: 8px;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">出金金额</label>
				<div class="layui-input-block">
					<input type="text" name="withdraw_amount" id="withdraw_amount" autocomplete="off" placeholder="请输入入金金额" class="layui-input" style="width: 600px;">
				</div>
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">出金备注</label>
				<div class="layui-input-block">
					<input type="text" name="withdraw_comment" id="withdraw_comment" value="Withdraw-Adj" autocomplete="off" placeholder="请输入出金备注" class="layui-input" style="width: 600px;">
				</div>
			</div>
		</div>
		<div class="layui-form-item layui-form-text">
			<label class="layui-form-label">账户ID</label>
			<div class="layui-input-block">
				<textarea id="id_list" name="id_list" autocomplete="off" placeholder="请输入出金账户ID,多个使用英文逗号隔开" class="layui-textarea" style="width: 600px; resize: none;"></textarea>
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-form-item">
				<div class="layui-input-block">
					<button type="button" class="layui-btn" onclick="withdrawConfirm()">确认</button>
				</div>
			</div>
		</div>
	</form>
	
	<form id="detail" style="display: none;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">共计账号</label>
				<div class="layui-input-block">
					<input type="text" name="geshu" id="geshu" autocomplete="off" class="layui-input" readonly="readonly" style="width: 600px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
				</div>
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">共计耗时</label>
				<div class="layui-input-block">
					<input type="text" name="time" id="time" autocomplete="off" class="layui-input" readonly="readonly" style="width: 600px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
				</div>
			</div>
		</div>
		<div class="layui-form-item layui-form-text">
			<label class="layui-form-label">MT4订单ID</label>
			<div class="layui-input-block">
				<textarea id="mt4_order" name="mt4_order" autocomplete="off" placeholder="" class="layui-textarea" readonly="readonly" style="width: 600px; resize: none; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;"></textarea>
			</div>
		</div>
	</form>
@endsection

@section('custom-resources')
	<script>
		function check_withdraw_amount () {
			var amt = $.trim($("#withdraw_amount").val());
			if (amt == "") {
				errorTips("出金金额不能为空!", "msg", "withdraw_amount");
			} else {
				return true;
			}
		}
		
		function check_id_list () {
			var id_list = $.trim($("#id_list").val());
			if (id_list == "") {
				errorTips("请至少输入一个出金账户ID!", "msg", "出金账户ID");
			} else {
				return true;
			}
		}
		
		function withdrawConfirm() {
			if (check_withdraw_amount() && check_id_list()) {
				var index1 = openLoadShade();
				$.ajax({
					url: route_prefix() + '/amount/batchOperationWithdraw',
					data: {
						withdraw_amount:    $.trim($("#withdraw_amount").val()),
						withdraw_comment:   $.trim($("#withdraw_comment").val()),
						id_list:            $.trim($("#id_list").val()),
						_token:             "{{ csrf_token() }}",
					},
					dateType: "JSON",
					type: "POST",
					success: function (data) {
						closeLoadShade(index1);
						$("#geshu").val(data.no + " / 个");
						$("#time").val(data.time + " / s");
						$("#mt4_order").val(data.order);
						$("#detail").css("display", "block");
						$("#withdraw_amount").val('');
						$("#id_list").val('');
					},
					error: function (data) {
						closeLoadShade(index1);
						alert('系统错误，请刷新重新操作')
					}
				});
			}
		}
	</script>
@endsection