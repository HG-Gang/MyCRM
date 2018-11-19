<form class="layui-form" action="" id="DirectDepositFlowForm" style="margin-top: 8px;">
	<div class="layui-form-item">
		<div class="layui-inline">
			<label class="layui-form-label">订单号</label>
			<div class="layui-input-block">
				<input type="text" name="direct_deposit_id" id="direct_deposit_id" autocomplete="off" placeholder="请输入订单号" class="layui-input" style="width: 200px;">
			</div>
		</div>
		<div class="layui-inline">
			<label class="layui-form-label">入金来源</label>
			<div class="layui-input-block">
				<input type="text" name="direct_deposit_source" id="direct_deposit_source" autocomplete="off" placeholder="请输入入金来源" class="layui-input" style="width: 200px;">
			</div>
		</div>
		<div class="layui-inline">
			<label class="layui-form-label">入金时间</label>
			<div class="layui-input-inline" style="width: 200px;">
				<input type="text" name="direct_deposit_startdate" id="direct_deposit_startdate" placeholder="请输入开始时间" autocomplete="off" class="layui-input">
			</div>
			<div class="layui-form-mid">-</div>
			<div class="layui-input-inline" style="width: 200px;">
				<input type="text" name="direct_deposit_enddate" id="direct_deposit_enddate" placeholder="请输入结束时间" autocomplete="off" class="layui-input">
			</div>
		</div>
		<button type="button" class="layui-btn" onclick="direct_deposit_flow()">查找</button>
	</div>
</form>
<div style="margin:20px 0px;"></div>
<table id="direct_deposit_data_list" style="width: 99%;" pagination="true" title="直属入金列表"></table>