<form class="layui-form" action="" id="DirectWithdrawFlowForm" style="margin-top: 8px;">
	<div class="layui-form-item">
		<div class="layui-inline">
			<label class="layui-form-label">订单号</label>
			<div class="layui-input-block">
				<input type="text" name="direct_withdraw_id" id="direct_withdraw_id" autocomplete="off" placeholder="请输入订单号" class="layui-input" style="width: 200px;">
			</div>
		</div>
		{{--<div class="layui-inline">
			<label class="layui-form-label">出金来源</label>
			<div class="layui-input-block">
				<input type="text" name="direct_withdraw_source" id="direct_withdraw_source" autocomplete="off" placeholder="请输入出金来源" class="layui-input" style="width: 200px;">
			</div>
		</div>--}}
		<div class="layui-inline">
			<label class="layui-form-label">出金时间</label>
			<div class="layui-input-inline" style="width: 200px;">
				<input type="text" name="direct_withdraw_startdate" id="direct_withdraw_startdate" placeholder="请输入开始时间" autocomplete="off" class="layui-input">
			</div>
			<div class="layui-form-mid">-</div>
			<div class="layui-input-inline" style="width: 200px;">
				<input type="text" name="direct_withdraw_enddate" id="direct_withdraw_enddate" placeholder="请输入结束时间" autocomplete="off" class="layui-input">
			</div>
		</div>
		<button type="button" class="layui-btn" onclick="direct_withdrawal_flow()">查找</button>
	</div>
</form>
<div style="margin:20px 0px;"></div>
<table id="direct_withdrawal_data_list" style="width: 99%;" pagination="true" title="直属出金列表"></table>