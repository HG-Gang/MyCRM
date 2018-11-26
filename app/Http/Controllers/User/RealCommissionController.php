<?php
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/3/14
	 * Time: 18:22
	 */
	
	namespace App\Http\Controllers\User;
	
	use App\Http\Controllers\CommonController\NofityInfo;
	use Illuminate\Http\Request;
	use App\Model\Mt4Trades;
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	
	class RealCommissionController extends Abstract_Mt4service_Controller
	{
		
		public function realtime_rebate_browse ()
		{
			
			return view ('user.realtime_rebate.realtime_rebate_browse')->with (['_user_info' => $this->_user]);
		}
		
		public function realtimeRebateSearch (Request $request)
		{
			
			$result = array ('rows' => '', 'total' => '');
			
			$_rs = $this->get_current_agents_rebate_order_id_list ('page', $request);
			
			if (!empty($_rs)) {
				$result['rows'] = $_rs;
				$result['total'] = $this->get_current_agents_rebate_order_id_list ('count', $request);
				$_datasum = $this->get_current_agents_rebate_order_id_sum_data ($request);
				$result['footer'] = [[
					'ticket'      => '总计',
					'login'       => '',
					'profit'      => $_datasum['search_total_profit'],
					'comment'     => '',
					'modify_time' => '',
				]];
			}
			
			return json_encode ($result);
		}
		
		public function realtime_rebate_detail ($orderNo, $role)
		{
			$_rs = Mt4Trades::select('mt4_trades.*', 'user_trades.rec_comp_date', 'user_trades.voided')->where('mt4_trades.TICKET', $orderNo)
				->leftJoin('user_trades', 'user_trades.ticket', ' = ',  'mt4_trades.TICKET')
				->whereIn('mt4_trades.CMD', array('0', '1', '2', '3', '4', '5'))
				->get()->toArray();
			
			$_rebate_info = Mt4Trades::selectRaw("
				mt4_trades.TICKET as ticket,
				mt4_trades.LOGIN as login,mt4_trades.PROFIT as profit, mt4_trades.MODIFY_TIME as modify_time,
				agents.user_id as userId, agents.user_name as userName, agents.comm_prop as commProp
			")->leftJoin('agents', 'mt4_trades.LOGIN', '=', 'agents.user_id')
			->where('mt4_trades.COMMENT', 'like', '%' . $orderNo . '-FY%')->where('mt4_trades.CMD', '6')
			->get()->toArray();
			
			$_rs['role'] = $role;
			$_rs['rebate_info'] = $_rebate_info;
			
			return view ('user.realtime_rebate.realtime_rebate_detail')->with (['_rs' => $_rs]);
		}
		
		protected function get_current_agents_rebate_order_id_list ($search, $request)
		{
			
			$userId         = $request->userId;
			$orderId        = $request->orderId;
			$startdate      = $request->startdate;
			$enddate        = $request->enddate;
			$loginId        = $this->_user;
			
			$query_sql = Mt4Trades::selectRaw("
				MT4_TRADES.TICKET as ticket, MT4_TRADES.LOGIN as login, MT4_TRADES.PROFIT as profit,
				MT4_TRADES.COMMENT as comment, MT4_TRADES.MODIFY_TIME as modify_time"
			)->whereIn('MT4_TRADES.LOGIN',function ($query) use($loginId) {
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
			})->where('mt4_trades.CMD', 6)
			->where('mt4_trades.COMMENT', 'like', '%-FY')
				->where(function ($subWhere) use ($userId, $orderId, $startdate, $enddate) {
					if (!empty($startdate) && !empty($enddate) && $this->_exte_is_Date ($startdate) && $this->_exte_is_Date ($enddate)) {
						$subWhere->whereBetween('mt4_trades.MODIFY_TIME', [$startdate .' 00:00:00', $enddate . ' 23:59:59']);
					} else {
						if(!empty($startdate) && $this->_exte_is_Date ($startdate)) {
							$subWhere->where('mt4_trades.MODIFY_TIME',  '>= ', $startdate .' 23:59:59');
						}
						if(!empty($enddate) && $this->_exte_is_Date ($enddate)) {
							$subWhere->where('mt4_trades.MODIFY_TIME', '<', $enddate .' 00:00:00');
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
				$id_list = $query_sql->skip($this->_offset)->take($this->_pageSize)->orderBy('MODIFY_TIME', 'desc')->get()->toArray();
			} else if ($search == 'count') {
				$id_list = $query_sql->count();
			} else if ($search == 'sum') {
				$id_list = $query_sql->get()->toArray();
			}
			
			return $id_list;
		}
		
		protected function get_current_agents_rebate_order_id_sum_data ($request)
		{
			
			$userId         = $request->userId;
			$orderId        = $request->orderId;
			$startdate      = $request->startdate;
			$enddate        = $request->enddate;
			$loginId        = $this->_user;
			$_all_rs        = array ();
			
			$_allsumdata[$loginId['user_id']]['search_total'] = Mt4Trades::selectRaw ("
					/*总返佣*/
					sum( case when mt4_trades.CMD = 6 and mt4_trades.COMMENT like '%-FY' then mt4_trades.PROFIT else 0 end ) as total_profit
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
				})->where('mt4_trades.CMD', 6)
				->where('mt4_trades.COMMENT', 'like', '%-FY')
				->where(function ($subWhere) use ($userId, $orderId, $startdate, $enddate) {
					if (!empty($startdate) && !empty($enddate) && $this->_exte_is_Date ($startdate) && $this->_exte_is_Date ($enddate)) {
						$subWhere->whereBetween('mt4_trades.MODIFY_TIME', [$startdate .' 00:00:00', $enddate . ' 23:59:59']);
					} else {
						if(!empty($startdate) && $this->_exte_is_Date ($startdate)) {
							$subWhere->where('mt4_trades.MODIFY_TIME',  '>= ', $startdate .' 23:59:59');
						}
						if(!empty($enddate) && $this->_exte_is_Date ($enddate)) {
							$subWhere->where('mt4_trades.MODIFY_TIME', '<', $enddate .' 00:00:00');
						}
					}
					
					if (!empty($userId)) {
						$subWhere->where('mt4_trades.LOGIN', 'like', '%' . $userId . '%');
					}
					if (!empty($orderId)) {
						$subWhere->where('mt4_trades.TICKET', 'like', '%' . $orderId . '%');
					}
				})->get()->toArray();
			
			if (!empty($_allsumdata) &&  count ($_allsumdata) > 0) {
				foreach ($_allsumdata as $vdata) {
					/*盈亏*/
					$_all_rs['search_total_profit']             = number_format($vdata['search_total'][0]['total_profit'], 2, '.', '');
				}
			}
			
			return $_all_rs;
		}
	}