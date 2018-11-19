<?php
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/3/14
	 * Time: 16:27
	 */
	
	namespace App\Http\Controllers\User;
	
	use Illuminate\Http\Request;
	use App\Model\Mt4Users;
	use App\Model\Agents;
	use App\Model\Mt4Trades;
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	
	class OpenOrderController extends Abstract_Mt4service_Controller
	{
		
		public function open_order_browse ()
		{
			return view ('user.open_order.open_order_browse')->with (['_user_info' => $this->_user]);
		}
		
		public function openOrderSearch (Request $request)
		{
			
			$result = array('rows' => '', 'total' => '');
			
			$_rs = $this->get_current_agents_open_order_id_list('page', $request);
			
			if (!empty($_rs)) {
				$result['rows'] = $_rs;
				$result['total'] = $this->get_current_agents_open_order_id_list ('count', $request);
				$_datasum = $this->get_current_agents_open_order_id_sum_data ($request);
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
		
		public function open_order_detail ($orderId, $orderType, $role)
		{
			$_rs = Mt4Trades::where('mt4_trades.TICKET', $orderId)->where('mt4_trades.CLOSE_TIME', '1970-01-01 00:00:00')
				->where('mt4_trades.CONV_RATE1', '<>', 0)->whereIn('mt4_trades.CMD', array (0,1,2,3,4,5))->get()->toArray();
			
			return view ('user.open_order.open_order_detail')->with (['_rs' => $_rs, '_orderType' => $orderType, '_role' => $role]);
		}
		
		protected function get_current_agents_open_order_id_list ($search, $request)
		{
			
			$userId         = $request->userId;
			$orderId        = $request->orderId;
			$startdate      = $request->startdate;
			$enddate        = $request->enddate;
			$loginId        = $this->_user;
			
			$query_sql = Mt4Trades::selectRaw("
				MT4_TRADES.TICKET as ticket, MT4_TRADES.LOGIN as login, MT4_TRADES.SYMBOL as symbol,
				MT4_TRADES.CMD as cmd, MT4_TRADES.VOLUME as volume, MT4_TRADES.SL as sl, MT4_TRADES.TP as tp,
				MT4_TRADES.COMMISSION as commission, MT4_TRADES.PROFIT as profit, MT4_TRADES.SWAPS as swaps,
				MT4_TRADES.OPEN_PRICE as open_price, MT4_TRADES.OPEN_TIME as open_time
			")->whereIn('MT4_TRADES.LOGIN',function ($query) use($loginId) {
				$query->selectRaw("
					/*普通客户*/
					user.user_id from user where parent_id in (
						select agents.user_id  from agents where agents.parent_id in (
							select agents.user_id  from agents where agents.parent_id in (
								select agents.user_id  from agents where agents.parent_id = " . intval ($loginId['user_id']) . " and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
							) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
						) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
					)
					/*代理商*/
					UNION
					select agents.user_id  from agents where agents.parent_id in (
						select agents.user_id  from agents where agents.parent_id in (
							select agents.user_id  from agents where agents.parent_id = " . intval ($loginId['user_id']) . " and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
						) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
					) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
				");
			})->whereIn('mt4_trades.CMD', array (0,1,2,3,4,5))
				->where('mt4_trades.CLOSE_TIME', '1970-01-01 00:00:00')
				->where('mt4_trades.CONV_RATE1', '<>', 0)
				->where(function ($subWhere) use ($userId, $orderId, $startdate, $enddate) {
					if (!empty($startdate) && !empty($enddate) && $this->_exte_is_Date ($startdate) && $this->_exte_is_Date ($enddate)) {
						$subWhere->whereBetween('mt4_trades.OPEN_TIME', [$startdate .' 00:00:00', $enddate . ' 23:59:59']);
					} else {
						if(!empty($startdate) && $this->_exte_is_Date ($startdate)) {
							$subWhere->where('mt4_trades.OPEN_TIME',  '>= ', $startdate .' 23:59:59');
						}
						if(!empty($enddate) && $this->_exte_is_Date ($enddate)) {
							$subWhere->where('mt4_trades.OPEN_TIME', '<', $enddate .' 00:00:00');
						}
					}
					
					if (!empty($userId)) {
						$subWhere->where('mt4_trades.LOGIN', 'like', '%' . $userId . '%');
					}
					if (!empty($orderId)) {
						$subWhere->where('mt4_trades.TICKET', 'like', '%' . $orderId . '%');
					}
				});
			
			if ($search == 'page') {
				$id_list = $query_sql->skip($this->_offset)->take($this->_pageSize)->orderBy('OPEN_TIME', 'desc')->get()->toArray();
			} else if ($search == 'count') {
				$id_list = $query_sql->count();
			} else if ($search == 'sum') {
				$id_list = $query_sql->get()->toArray();
			}
			
			return $id_list;
		}
		
		protected function get_current_agents_open_order_id_sum_data ($request)
		{
			
			$userId         = $request->userId;
			$orderId        = $request->orderId;
			$startdate      = $request->startdate;
			$enddate        = $request->enddate;
			$loginId        = $this->_user;
			$_all_rs        = array ();
			
			$_allsumdata[$loginId['user_id']]['search_total'] = Mt4Trades::selectRaw ("
				/*总手续费*/
				sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME = '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.COMMISSION else 0 end ) as total_comm,
				/*总手数 ==  总交易量*/
				sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME = '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as total_volume,
				/*总利息*/
				sum( case when mt4_trades.SWAPS < 0 and mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME = '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.SWAPS else 0 end ) as total_swaps,
				/*总盈亏*/
				sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME = '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.PROFIT else 0 end ) as total_profit
			")->whereIn('MT4_TRADES.LOGIN',function ($query) use($loginId) {
				$query->selectRaw("
					/*普通客户*/
					user.user_id from user where parent_id in (
						select agents.user_id  from agents where agents.parent_id in (
							select agents.user_id  from agents where agents.parent_id in (
								select agents.user_id  from agents where agents.parent_id = " . intval ($loginId['user_id']) . " and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
							) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
						) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
					)
					/*代理商*/
					UNION
					select agents.user_id  from agents where agents.parent_id in (
						select agents.user_id  from agents where agents.parent_id in (
							select agents.user_id  from agents where agents.parent_id = " . intval ($loginId['user_id']) . " and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
						) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
					) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
				");
			})->whereIn('mt4_trades.CMD', array (0,1,2,3,4,5))
				->where('mt4_trades.CLOSE_TIME', '1970-01-01 00:00:00')
				->where('mt4_trades.CONV_RATE1', '<>', 0)
				->where(function ($subWhere) use ($userId, $orderId, $startdate, $enddate) {
					if (!empty($startdate) && !empty($enddate) && $this->_exte_is_Date ($startdate) && $this->_exte_is_Date ($enddate)) {
						$subWhere->whereBetween('mt4_trades.OPEN_TIME', [$startdate .' 00:00:00', $enddate . ' 23:59:59']);
					} else {
						if(!empty($startdate) && $this->_exte_is_Date ($startdate)) {
							$subWhere->where('mt4_trades.OPEN_TIME',  '>= ', $startdate .' 23:59:59');
						}
						if(!empty($enddate) && $this->_exte_is_Date ($enddate)) {
							$subWhere->where('mt4_trades.OPEN_TIME', '<', $enddate .' 00:00:00');
						}
					}
					
					if (!empty($userId)) {
						$subWhere->where('mt4_trades.LOGIN', 'like', '%' . $userId . '%');
					}
					if (!empty($orderId)) {
						$subWhere->where('mt4_trades.TICKET', 'like', '%' . $orderId . '%');
					}
				})->get()->toArray();
			
			//重新整理数据结构
			if (!empty($_allsumdata) &&  count ($_allsumdata) > 0) {
				foreach ($_allsumdata as $vdata) {
					/*总手续费*/
					$_all_rs['search_total_comm']               = number_format($vdata['search_total'][0]['total_comm'], 2, '.', '');
					/*总手数 == 总交易量*/
					$_all_rs['search_total_volume']             = $vdata['search_total'][0]['total_volume'] / 100;
					/*总利息*/
					$_all_rs['search_total_swaps']              = number_format($vdata['search_total'][0]['total_swaps'], 2, '.', '');
					/*盈亏*/
					$_all_rs['search_total_profit']             = number_format($vdata['search_total'][0]['total_profit'], 2, '.', '');
				}
			}
			
			return $_all_rs;
		}
	}