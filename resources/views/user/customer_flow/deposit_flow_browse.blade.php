<form class="layui-form" action="" id="DepositFlowForm" style="margin-top: 8px;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">订单号</label>
				<div class="layui-input-block">
					<input type="text" name="deposit_id" id="deposit_id" autocomplete="off" placeholder="请输入订单号" class="layui-input" style="width: 200px;">
				</div>
			</div>
			{{--<div class="layui-inline">
				<label class="layui-form-label">入金来源</label>
				<div class="layui-input-block">
					<input type="text" name="deposit_source" id="deposit_source" autocomplete="off" placeholder="请输入入金来源" class="layui-input" style="width: 200px;">
				</div>
			</div>--}}
			<div class="layui-inline">
				<label class="layui-form-label">入金时间</label>
				<div class="layui-input-inline" style="width: 200px;">
					<input type="text" name="deposit_startdate" id="deposit_startdate" placeholder="请输入开始时间" autocomplete="off" class="layui-input">
				</div>
				<div class="layui-form-mid">-</div>
				<div class="layui-input-inline" style="width: 200px;">
					<input type="text" name="deposit_enddate" id="deposit_enddate" placeholder="请输入结束时间" autocomplete="off" class="layui-input">
				</div>
			</div>
			<button type="button" class="layui-btn" onclick="deposit_flow()">查找</button>
		</div>
	</form>
	<div style="margin:20px 0px;"></div>
	<table id="deposit_data_list" style="width: 99%;" pagination="true" title="入金列表"></table>