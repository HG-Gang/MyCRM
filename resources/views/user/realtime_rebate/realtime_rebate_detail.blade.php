@extends('user.layout.main_right')

@section('public-resources')
	<style>
		.public-top{width: 100%; height: 50px; }
		.public-l{width: 50%; height: 100%; float: left; position: relative; }
		.public-l>span{display: inline-block; height: 40px; line-height: 40px; width: 35%; position: absolute; left: 0; text-align: right; font-size: 14px; }
		.public-l>p{width: 55%; height: 36px; line-height: 40px; position: absolute; right: 10%; text-indent: 1em; border-bottom: 1px solid #ccc; }
		.public-r{width: 50%; height: 100%; float: left; position: relative; }
		.public-r>span{display: inline-block; height: 40px; line-height: 40px; width: 35%; position: absolute; left: 0; text-align: right; font-size: 14px; }
		.public-r>p{width: 55%; height: 36px; line-height: 40px; position: absolute; right: 10%; text-indent: 1em; border-bottom: 1px solid #ccc; }
	</style>
@endsection

@section('content')
	<div style="display: none;">{{ $_rs['role'] }}</div>
	<div class="public-top">
		<div class="public-l">
			<span>账户ID:</span>
			<p>{{ $_rs[0]['LOGIN'] }}</p>
		</div>
		<div class="public-r">
			<span>订单号 :</span>
			<p>{{ $_rs[0]['TICKET'] }}</p>
		</div>
	</div>
	<div class="public-top">
		<div class="public-l">
			<span>交易产品:</span>
			<p>{{ $_rs[0]['SYMBOL'] }}</p>
		</div>
		<div class="public-r">
			<span>产品类型: </span>
			@if($_rs[0]['CMD'] == 0)
				<p>Buy</p>
			@elseif($_rs[0]['CMD'] == 1)
				<p>Sell</p>
			@elseif($_rs[0]['CMD'] == 2)
				<p>Buy Limit</p>
			@elseif($_rs[0]['CMD'] == 3)
				<p>Sell Limit</p>
			@elseif($_rs[0]['CMD'] == 4)
				<p>Buy Stop</p>
			@elseif($_rs[0]['CMD'] == 5)
				<p>Sell Stop</p>
			@endif
		</div>
	</div>
	<div class="public-top">
		<div class="public-l">
			<span>交易量 :</span>
			<p>{{ ($_rs[0]['VOLUME'] / 100) }}</p>
		</div>
		<div class="public-r">
			<span>手续费 : </span>
			<p style="color: red;"> {{ $_rs[0]['COMMISSION'] }}</p>
		</div>
	</div>
	<div class="public-top">
		<div class="public-l">
			<span>盈亏 :</span>
			@if((int)$_rs[0]['PROFIT'] >= 0)
				<p style="color: green;">{{ $_rs[0]['PROFIT'] }}</p>
			@else
				<p style="color: red;">{{ $_rs[0]['PROFIT'] }}</p>
			@endif
		</div>
		<div class="public-r">
			<span>隔夜利息 : </span>
			<p style="color: red;"> {{ $_rs[0]['SWAPS'] }}</p>
		
		</div>
	</div>
	<div class="public-top">
		@if($_rs[0]['CONV_RATE1'] >= 0)
			<div class="public-l">
				<span>开仓日期 :</span>
				<p>{{ $_rs[0]['OPEN_TIME'] }}</p>
			</div>
		@endif
		@if($_rs[0]['CONV_RATE2'] >= 0)
			<div class="public-r">
				<span>平仓日期 : </span>
				<p>  {{ $_rs[0]['CLOSE_TIME'] }}</p>
			</div>
		@endif
	</div>
	<div class="public-top">
		@if($_rs[0]['CONV_RATE1'] >= 0)
			<div class="public-l">
				<span>开仓价格 :</span>
				<p>{{ $_rs[0]['OPEN_PRICE'] }}</p>
			</div>
		@endif
		@if($_rs[0]['CONV_RATE2'] >= 0)
			<div class="public-r">
				<span>平仓价格 : </span>
				<p>  {{ $_rs[0]['CLOSE_PRICE'] }}</p>
			</div>
		@endif
	</div>
	<div class="public-top">
		<div class="public-l">
			<span>止损价格 :</span>
			<p>{{ $_rs[0]['SL'] }}</p>
		</div>
		<div class="public-r">
			<span>止赢价格 : </span>
			<p>{{ $_rs[0]['TP'] }}</p>
		</div>
	</div>
	<div class="public-top">
		@if($_rs[0]['CONV_RATE1'] >= 0)
			<div class="public-l">
				<span>开仓兑换汇率 :</span>
				<p>{{ $_rs[0]['CONV_RATE1'] }}</p>
			</div>
		@endif
		@if($_rs[0]['CONV_RATE2'] >= 0)
			<div class="public-r">
				<span>平仓兑换汇率 :</span>
				<p>{{ $_rs[0]['CONV_RATE2'] }}</p>
			</div>
		@endif
	</div>
	<div class="public-top">
		<div class="public-l" style="display: none;">
			<span>最后修改日期 :</span>
			<p>{{ $_rs[0]['MODIFY_TIME'] }}</p>
		</div>
		@if($_rs['role'] == 'agents' || $_rs['role'] == 'admin')
			@if($_rs[0]['voided'] == '1')
				<div class="public-r">
					<span>返佣日期 : </span>
					<p>{{ $_rs[0]['rec_comp_date'] }}</p>
				</div>
			@endif
		@endif
	</div>
	@if($_rs['role'] == 'agents' || $_rs['role'] == 'admin')
		<div class="public-top">
			@if($_rs[0]['voided'] == '1')
				<div class="public-l">
					<span>是否返佣 : </span>
					<p style="color: green;">已返佣</p>
				</div>
			@else
				<div class="public-l">
					<span>是否返佣 : </span>
					<p style="color: red;">未返佣</p>
				</div>
			@endif
		</div>
	@endif
	
	 {{--//TODO 测试默认true, 上线的话去掉注释，这部分只能admin管理员才能看到--}}
	@if($_rs['role'] == 'admin')
		<table class="layui-table" id="orderRebateDetail">
			<thead>
				<tr>
					<th>返佣ID</th>
					<th>返佣账户</th>
					<th>账户姓名</th>
					<th>返佣金额</th>
					<th>返佣比例</th>
					<th>返佣时间</th>
				</tr>
			@for($i = 0; $i < count ($_rs['rebate_info']); $i++)
				<tr>
					<td>{{ $_rs['rebate_info'][$i]['ticket'] }}</td>
					<td>{{ $_rs['rebate_info'][$i]['userId'] }}</td>
					<td>{{ $_rs['rebate_info'][$i]['userName'] }}</td>
					<td>{{ $_rs['rebate_info'][$i]['profit'] }}</td>
					<td>{{ $_rs['rebate_info'][$i]['commProp'] }}</td>
					<td>{{ $_rs['rebate_info'][$i]['modify_time'] }}</td>
				</tr>
			@endfor
			</thead>
		</table>
	@endif
@endsection

@section('custom-resources')
@endsection