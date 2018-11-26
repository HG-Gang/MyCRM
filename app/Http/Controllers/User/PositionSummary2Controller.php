<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018-04-26
	 * Time: 下午 4:27
	 */
	
	namespace App\Http\Controllers\User;
	
	use Illuminate\Http\Request;
	use App\Model\Mt4Trades;
	use App\Model\User;
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	
	class PositionSummary2Controller extends Abstract_Mt4service_Controller
	{
		public function position_summary2_browse() {
			return view ('user.position_summary2.position_summary2_browse')->with (['_user_info' => $this->_user]);
		}
		
		public function positionSummary2Search (Request $request)
		{
			
			$result = array('rows' => '', 'total' => '');
			
			$_rs = User::selectRaw('user.user_id, user.user_name')
				->leftjoin('mt4_users', function ($leftjoin)  {
					$leftjoin->on('mt4_users.LOGIN', ' = ', 'user.user_id');
				})->where('user.user_id', $this->_user['user_id'])->get()->toArray();
			
			if (!empty($_rs)) {
				$_sumdata = $this->get_self_loginId_sum_data ($request);
				$_rs[0]['total_yuerj']                 = $_sumdata['total_yuerj'];
				$_rs[0]['total_yuecj']                 = $_sumdata['total_yuecj'];
				$_rs[0]['total_profit']                = $_sumdata['total_profit'];
				$_rs[0]['total_comm']                  = $_sumdata['total_comm'];
				$_rs[0]['total_net_worth']             = $_sumdata['total_net_worth'];
				$_rs[0]['total_noble_metal']           = $_sumdata['total_noble_metal'];
				$_rs[0]['total_for_exca']              = $_sumdata['total_for_exca'];
				$_rs[0]['total_crud_oil']              = $_sumdata['total_crud_oil'];
				$_rs[0]['total_index']                 = $_sumdata['total_index'];
				$_rs[0]['total_volume']                = $_sumdata['total_volume'];
				$_rs[0]['total_swaps']                 = $_sumdata['total_swaps'];
				
				$result['rows'] = $_rs;
				$result['total'] = 1;
			}
			
			return json_encode($result);
		}
		
		protected function get_self_loginId_sum_data ($request)
		{
			$startdate          = $request->startdate;
			$enddate            = $request->enddate;
			$loginId            = $this->_user;
			
			$_one_sumdata = Mt4Trades::selectRaw ("
					/*普通客户余额入金*/
					sum( case when mt4_trades.PROFIT > 0 and mt4_trades.CMD in (6) and mt4_trades.COMMENT NOT LIKE '%Adj%' then mt4_trades.PROFIT else 0 end ) as total_yuerj,
					/*普通客户余额出金*/
					sum( case when mt4_trades.PROFIT < 0 and mt4_trades.CMD in (6) and mt4_trades.COMMENT NOT LIKE '%Adj%' then mt4_trades.PROFIT else 0 end ) as total_yuecj,
					/*普通客户盈亏*/
					sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.PROFIT else 0 end ) as total_profit,
					/*普通客户手续费*/
					abs( sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.COMMISSION else 0 end ) ) as total_comm,
					/*普通客户贵金属*/
					 sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 1 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as total_noble_metal,
					/*普通客户外汇*/
					 sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 2 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as total_for_exca,
					/*普通客户原油*/
					 sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 3 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as total_crud_oil,
					/*普通客户指数*/
					 sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 4 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as total_index,
					/*普通客户手数 == 代理商交易量*/
					sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as total_volume,
					/*普通客户利息*/
					sum( case when mt4_trades.SWAPS < 0 and mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.SWAPS else 0 end ) as total_swaps
				")->where('mt4_trades.LOGIN', $loginId['user_id'])
				->where(function ($query2) use($startdate, $enddate) {
					if (!empty($startdate) && $this->_exte_is_Date ($startdate) && !empty($enddate) && $this->_exte_is_Date ($enddate)) {
						$query2->whereBetween('mt4_trades.CLOSE_TIME', [$startdate . ' 00:00:00', $enddate . ' 23:59:59']);
					} else {
						if (!empty($startdate) && $this->_exte_is_Date ($startdate)) {
							$query2->where('mt4_trades.CLOSE_TIME', '>=', $startdate . '  23:59:59');
						}
						if (!empty($enddate) && $this->_exte_is_Date ($enddate)) {
							$query2->where('mt4_trades.CLOSE_TIME', '<', $enddate . ' 00:00:00');
						}
					}
				})->get()->toArray();
			
			return $this->_exte_format_data($_one_sumdata);
		}
		
		protected function _exte_format_data($vdata)
		{
			
			//总入金
			$_rs['total_yuerj']                    = number_format ($vdata[0]['total_yuerj'], 2, '.', '');
			//总出金
			$_rs['total_yuecj']                    = number_format ($vdata[0]['total_yuecj'], 2, '.', '');
			//总盈亏
			$_rs['total_profit']                   = number_format ($vdata[0]['total_profit'], 2, '.', '');
			//总手续费
			$_rs['total_comm']                     = number_format ($vdata[0]['total_comm'], 2, '.', '');
			//总净入金 = 入金 - 出金
			$_rs['total_net_worth']                = number_format(((number_format ($vdata[0]['total_yuerj'], 2, '.', '') - abs(number_format ($vdata[0]['total_yuecj'], 2, '.', '')))), 2, '.', '');
			//总贵金属
			$_rs['total_noble_metal']              =  $vdata[0]['total_noble_metal'] / 100;
			//总外汇
			$_rs['total_for_exca']                 =  $vdata[0]['total_for_exca'] / 100;
			//总原油
			$_rs['total_crud_oil']                 =  $vdata[0]['total_crud_oil'] / 100;
			//总指数
			$_rs['total_index']                    =  $vdata[0]['total_index'] / 100;
			//总手数
			$_rs['total_volume']                   =  $vdata[0]['total_volume'] / 100;
			//总利息
			$_rs['total_swaps']                    = number_format ($vdata[0]['total_swaps'], 2, '.', '');
			
			return $_rs;
		}
	}