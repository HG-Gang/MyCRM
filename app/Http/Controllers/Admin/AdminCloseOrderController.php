<?php
	/**
	 * Created by PhpStorm.
	 * User: Hank
	 * Date: 2018/3/31
	 * Time: 15:00
	 */
	
	namespace App\Http\Controllers\admin;
	
	use Illuminate\Http\Request;
	use App\Model\Mt4Users;
	use App\Model\Agents;
	use App\Model\Mt4Trades;
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	
	class AdminCloseOrderController extends Abstract_Mt4service_Controller
	{
		public function close_list ()
		{
			return view ('admin.close_order.close_order_list_browse')->with(['_permit' => $this->Role()]);
		}
		
		public function closeListSearch (Request $request)
		{
			
			$data = array(
				'userId'            => $request->userId,
				'orderId'           => $request->orderId,
				'is_coercion'       => $request->is_coercion,
				'startdate'         => $request->startdate,
				'enddate'           => $request->enddate,
			);
			
			$result = array('rows' => '', 'total' => '');
			
			$_rs = $this->admin_get_current_agents_close_order_id_list('page', $data);
			
			if (!empty($_rs)) {
				$result['rows'] = $_rs;
				$result['total'] = $this->admin_get_current_agents_close_order_id_list ('count', $data);
				$_datasum = $this->admin_get_current_agents_close_order_id_sum_data ($data);
				$result['footer'] = [[
					'ticket'        => '',
					'login'         => '',
					'symbol'        => '总计',
					'cmd'           => '',
					'volume'        => $_datasum['search_total_volume'],
					'commission'    => $_datasum['search_total_comm'],
					'profit'        => $_datasum['search_total_profit'],
					'swaps'         => $_datasum['search_total_swaps']
				]];
			}
			
			return json_encode ($result);
		}
		
		protected function admin_get_current_agents_close_order_id_list ($totalType, $data)
		{
			$query_sql = Mt4Trades::selectRaw("
				MT4_TRADES.TICKET as ticket, MT4_TRADES.LOGIN as login, MT4_TRADES.SYMBOL as symbol,
				MT4_TRADES.CMD as cmd, MT4_TRADES.VOLUME as volume, MT4_TRADES.SL as sl, MT4_TRADES.TP as tp,
				MT4_TRADES.COMMISSION as commission, MT4_TRADES.PROFIT as profit, MT4_TRADES.SWAPS as swaps,
				MT4_TRADES.OPEN_PRICE as open_price,MT4_TRADES.CLOSE_PRICE as close_price, MT4_TRADES.CLOSE_TIME as close_time,
				MT4_TRADES.COMMENT as orderComment
			")->whereIn('mt4_trades.CMD', array (0,1,2,3,4,5))
				->where('mt4_trades.CLOSE_TIME', '>', '1970-01-01 00:00:00')
				->where('mt4_trades.CONV_RATE1', '<>', 0)
				->where(function ($subWhere) use ($data) {
					$this->_exte_set_search_condition($subWhere, $data);
				});
			
			return $this->_exte_get_query_sql_data($query_sql, $totalType, 'mt4_trades.MODIFY_TIME');
		}
		
		protected function admin_get_current_agents_close_order_id_sum_data ($data)
		{
			$_all_rs        = array ();
			
			//当前查找条件所有客户数据汇总
			$_allsumdata['search_total'] = Mt4Trades::selectRaw ("
				/*总手续费*/
				sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.COMMISSION else 0 end ) as total_comm,
				/*总手数 ==  总交易量*/
				sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as total_volume,
				/*总利息*/
				sum( case when mt4_trades.swaps < 0 and mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.swaps else 0 end ) as total_swaps,
				/*总盈亏*/
				sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.PROFIT else 0 end ) as total_profit
			")->whereIn('mt4_trades.CMD', array (0,1,2,3,4,5))
				->where('mt4_trades.CLOSE_TIME', '>', '1970-01-01 00:00:00')
				->where('mt4_trades.CONV_RATE1', '<>', 0)
				->where(function ($subWhere) use ($data) {
					$this->_exte_set_search_condition($subWhere, $data);
				})->get()->toArray();
			
			//重新整理数据结构
			if (!empty($_allsumdata) &&  count ($_allsumdata) > 0) {
				foreach ($_allsumdata as $vdata) {
					
					/*总手续费*/
					$_all_rs['search_total_comm']               = number_format($vdata[0]['total_comm'], 2, '.', '');
					/*总手数 == 总交易量*/
					$_all_rs['search_total_volume']             = $vdata[0]['total_volume'] / 100;
					/*总利息*/
					$_all_rs['search_total_swaps']              = number_format($vdata[0]['total_swaps'], 2, '.', '');
					/*盈亏*/
					$_all_rs['search_total_profit']             = number_format($vdata[0]['total_profit'], 2, '.', '');
				}
			}
			
			return $_all_rs;
		}
		
		protected function _exte_set_search_condition($subWhere, $data)
		{
			if (!empty($data['startdate']) && !empty($data['enddate']) && $this->_exte_is_Date ($data['startdate']) && $this->_exte_is_Date ($data['enddate'])) {
				$subWhere->whereBetween('mt4_trades.CLOSE_TIME', [$data['startdate'] .' 00:00:00', $data['enddate'] . ' 23:59:59']);
			} else {
				if(!empty($data['startdate']) && $this->_exte_is_Date ($data['startdate'])) {
					$subWhere->where('mt4_trades.CLOSE_TIME',  '>= ', $data['startdate'] .' 23:59:59');
				}
				if(!empty($data['enddate']) && $this->_exte_is_Date ($data['enddate'])) {
					$subWhere->where('mt4_trades.CLOSE_TIME', '<', $data['enddate'] .' 00:00:00');
				}
			}
			
			if (!empty($data['userId'])) {
				$subWhere->where('mt4_trades.LOGIN', 'like', '%' . $data['userId'] . '%');
			}
			if (!empty($data['orderId'])) {
				$subWhere->where('mt4_trades.TICKET', 'like', '%' . $data['orderId'] . '%');
			}
			if (!empty($data['is_coercion']) && $data['is_coercion'] == 'Yes') {
				$subWhere->where('mt4_trades.COMMENT', 'like', 'so%');
			} else if (!empty($data['is_coercion']) && $data['is_coercion'] == 'No') {
				$subWhere->where('mt4_trades.COMMENT', 'Not like', 'so%');
			}
			
			$subWhere->whereNotIn('mt4_trades.LOGIN',function ($subWhere2) {
				$subWhere2->selectRaw("
								/*普通客户*/
								user.user_id from user where parent_id in (
									select agents.user_id  from agents where agents.parent_id in (
										select agents.user_id  from agents where agents.parent_id in (
											select agents.user_id  from agents where agents.parent_id = ". intval($this->_agentsIdIndex) ." and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = ". intval($this->_agentsIdIndex) ."
										) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = ". intval($this->_agentsIdIndex) ."
									) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = ". intval($this->_agentsIdIndex) ."
								)
								/*代理商*/
								UNION
								select agents.user_id  from agents where agents.parent_id in (
									select agents.user_id  from agents where agents.parent_id in (
										select agents.user_id  from agents where agents.parent_id = ". intval($this->_agentsIdIndex) ." and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = ". intval($this->_agentsIdIndex) ."
									) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = ". intval($this->_agentsIdIndex) ."
								) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = ". intval($this->_agentsIdIndex) ."
							");
			});
			
			return $subWhere;
		}
	}