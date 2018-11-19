<form class="layui-form" action="" id="WithdrawApplyFlowForm" style="margin-top: 8px;">
	<div class="layui-form-item">
		<div class="layui-inline">
			<label class="layui-form-label">订单号</label>
			<div class="layui-input-block">
				<input type="text" name="withdraw_apply_id" id="withdraw_apply_id" autocomplete="off" placeholder="请输入订单号" class="layui-input" style="width: 200px;">
			</div>
		</div>
		<div class="layui-inline">
			<label class="layui-form-label">出金状态</label>
			<div class="layui-input-inline">
				<select name="withdraw_apply_status" id="withdraw_apply_status">
					<option value="">请选择出金状态</option>
					<option value="0">待处理</option>
					<option value="1">正在处理</option>
					<option value="2">已出款</option>
					<option value="3">出款失败</option>
				</select>
			</div>
		</div>
		<div class="layui-inline">
			<label class="layui-form-label">出金时间</label>
			<div class="layui-input-inline" style="width: 200px;">
				<input type="text" name="withdraw_apply_startdate" id="withdraw_apply_startdate" placeholder="请输入开始时间" autocomplete="off" class="layui-input">
			</div>
			<div class="layui-form-mid">-</div>
			<div class="layui-input-inline" style="width: 200px;">
				<input type="text" name="withdraw_apply_enddate" id="withdraw_apply_enddate" placeholder="请输入结束时间" autocomplete="off" class="layui-input">
			</div>
		</div>
		<button type="button" class="layui-btn" onclick="withdrawal_apply_flow()">查找</button>
	</div>
</form>
<div style="margin:20px 0px;"></div>
<table id="withdraw_apply_data_list" style="width: 99%;" pagination="true" title="出金申请列表"></table>