<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018-05-14
	 * Time: 下午 5:41
	 */
	
	namespace App\Http\Controllers\Admin;
	
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	use Illuminate\Http\Request;
	use App\Model\Agents;
	use App\Model\Mt4Trades;
	
	class PositionSummaryController extends Abstract_Mt4service_Controller
	{
		public function position_summary_list ()
		{
			return view('admin.position_summary.position_summary_list');
		}
		
		public function positionSummarySearch (Request $request)
		{
			$result = array('rows' => '', 'total' => '');
			
			$_agn_info = $this->get_agents_id_list ('page', $request);
			
			if (!empty($_agn_info)) {
				//获取查找条件的说要代理商ID, 并汇总每一个ID的各种金额
				$_sumdata = $this->get_agents_sum_data ($_agn_info, $request);
				
				//整理 $_agn_info 和 $_sumdata
				foreach ($_agn_info as $key => $vdata) {
					$_agn_info[$key]['total_yuerj']                 = $_sumdata[$_agn_info[$key]['user_id']]['total_yuerj'];
					$_agn_info[$key]['total_yuecj']                 = $_sumdata[$_agn_info[$key]['user_id']]['total_yuecj'];
					$_agn_info[$key]['total_rebate']                = $_sumdata[$_agn_info[$key]['user_id']]['total_rebate'];
					$_agn_info[$key]['total_profit']                = $_sumdata[$_agn_info[$key]['user_id']]['total_profit'];
					$_agn_info[$key]['total_comm']                  = $_sumdata[$_agn_info[$key]['user_id']]['total_comm'];
					$_agn_info[$key]['total_net_worth']             = $_sumdata[$_agn_info[$key]['user_id']]['total_net_worth'];
					$_agn_info[$key]['total_noble_metal']           = $_sumdata[$_agn_info[$key]['user_id']]['total_noble_metal'];
					$_agn_info[$key]['total_for_exca']              = $_sumdata[$_agn_info[$key]['user_id']]['total_for_exca'];
					$_agn_info[$key]['total_crud_oil']              = $_sumdata[$_agn_info[$key]['user_id']]['total_crud_oil'];
					$_agn_info[$key]['total_index']                 = $_sumdata[$_agn_info[$key]['user_id']]['total_index'];
					$_agn_info[$key]['total_volume']                = $_sumdata[$_agn_info[$key]['user_id']]['total_volume'];
					$_agn_info[$key]['total_swaps']                 = $_sumdata[$_agn_info[$key]['user_id']]['total_swaps'];
				}
				
				$result['rows'] = $_agn_info;
				$result['total'] = $this->get_agents_id_list ('count', $request);
				$_all_agn_info = $this->get_agents_id_list ('sum', $request);
				$_all_datasum = $this->get_all_agents_sum_data ($_all_agn_info, $request);
				
				$result['footer'] = [[
					'user_id'           => '',
					'user_name'         => '总计',
					'total_yuerj'       => $_all_datasum['search_all_total_yuerj'],
					'total_yuecj'       => $_all_datasum['search_all_total_yuecj'],
					'total_rebate'      => $_all_datasum['search_all_total_rebate'],
					'total_net_worth'   => $_all_datasum['search_all_total_net_worth'],
					'total_comm'        => $_all_datasum['search_all_total_comm'],
					'total_profit'      => $_all_datasum['search_all_total_profit'],
					'total_noble_metal' => $_all_datasum['search_all_total_noble_metal'],
					'total_for_exca'    => $_all_datasum['search_all_total_for_exca'],
					'total_crud_oil'    => $_all_datasum['search_all_total_crud_oil'],
					'total_index'       => $_all_datasum['search_all_total_index'],
					'total_volume'      => $_all_datasum['search_all_total_volume'],
					'total_swaps'       => $_all_datasum['search_all_total_swaps'],
				]];
			}
			
			return json_encode($result);
		}
		
		protected function get_agents_id_list($totalType, $request)
		{
			$userId             = $request->userId;
			$userName           = $request->userName;
			$userPId            = $request->userPId;
			$searchtype         = $request->searchtype;
			
			$query_sql = Agents::selectRaw('agents.user_id, agents.user_name, agents.parent_id, agents_group.group_id as agents_group_id, agents_group.group_name, agents_group.agents_comm_prop')
				->leftjoin('mt4_users', function ($leftjoin)  {
					$leftjoin->on('mt4_users.LOGIN', ' = ', 'agents.user_id');
				})->leftjoin('agents_group', 'agents.group_id', ' = ', 'agents_group.group_id')
				->whereRaw("agents.voided = '1' and agents.user_status in ('0', '1', '2', '4')")
				->where(function ($subWhere) use ($searchtype, $userId, $userName, $userPId) {
					if ($searchtype == 'autoSearch') {
						$subWhere->where('agents.parent_id', 0);
					} else {
						if ($searchtype == 'clickSearch') {
							if (empty($userId)) {
								$subWhere->where('agents.parent_id', 0);
								/*$subWhere->whereIn('agents.user_id', function ($query) use ($userId) {
									$query->selectRaw("
										agents.user_id from agents where agents.parent_id in (
											select agents.user_id  from agents where agents.parent_id in (
												select agents.user_id  from agents where agents.parent_id = " . $userId . " and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . $userId . "
											) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . $userId . "
										) and agents.voided = '1' and agents.user_status in ('0','1','2','4')
									");
								});*/
							} else {
								$subWhere->where('agents.user_id', $userId);
							}
						}
						
						if (!empty($userName)) {
							$subWhere->where('agents.user_name', 'like',  '%'. $userName .'%');
						}
						
						if (!empty($userPId) && $searchtype == 'subAgentsSearch') {
							$subWhere->where('agents.parent_id', $userPId)/*->orWhere('agents.user_id', $userPId)*/;
						}
					}
				});
			
			if ($totalType == 'page') {
				$data_list = $query_sql->skip($this->_offset)->take($this->_pageSize)->orderBy('agents.user_id', 'asc')->get()->toArray();
			} else if ($totalType == 'count') {
				$data_list = $query_sql->count();
			} else if ($totalType == 'sum') {
				$data_list = $query_sql->get()->toArray();
			}
			
			return $data_list;
		}
		
		protected function get_agents_sum_data($id_list, $request)
		{
			$startdate          = $request->startdate;
			$enddate            = $request->enddate;
			$searchtype         = $request->searchtype;
			
			//得到$data每一个user_id代理商的所有相关各种金额汇总
			//汇总当前页的代理商各种金额
			foreach ($id_list as $key => $vdata) {
				$_one_sumdata[$vdata['user_id']] = Mt4Trades::selectRaw ("
					/*代理商余额入金*/
					sum( case when mt4_trades.PROFIT > 0 and mt4_trades.CMD = 6 and mt4_trades.COMMENT NOT LIKE '%-FY' then mt4_trades.PROFIT else 0 end ) as total_yuerj,
					/*代理商余额出金*/
					sum( case when mt4_trades.PROFIT < 0 and mt4_trades.CMD = 6 then mt4_trades.PROFIT else 0 end ) as total_yuecj,
					/*代理商返佣金额*/
					sum( case when mt4_trades.PROFIT > 0 AND mt4_trades.CMD = 6 AND mt4_trades.COMMENT LIKE '%-FY' THEN mt4_trades.PROFIT ELSE 0 END ) as total_rebate,
					/*代理商盈亏*/
					sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.PROFIT else 0 end ) as total_profit,
					/*代理商手续费*/
					abs( sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.COMMISSION else 0 end ) ) as total_comm,
					/*代理商贵金属*/
					 sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 1 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as total_noble_metal,
					/*代理商外汇*/
					 sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 2 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as total_for_exca,
					/*代理商原油*/
					 sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 3 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as total_crud_oil,
					/*代理商指数*/
					 sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 4 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as total_index,
					/*代理商手数 == 代理商交易量*/
					sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as total_volume,
					/*代理商利息*/
					sum( case when mt4_trades.SWAPS < 0 and mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.SWAPS else 0 end ) as total_swaps
				")->where(function ($subQuery) use ($searchtype, $vdata) {
					$subQuery->whereIn('mt4_trades.LOGIN',function ($whereIn2) use($vdata) {
						$whereIn2->selectRaw("
								/*普通客户*/
								user.user_id from user where parent_id in (
									select agents.user_id  from agents where agents.parent_id in (
										select agents.user_id  from agents where agents.parent_id in (
											select agents.user_id  from agents where agents.parent_id = " . intval ($vdata['user_id']) . " and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($vdata['user_id']) . "
										) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($vdata['user_id']) . "
									) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($vdata['user_id']) . "
								)
								/*代理商*/
								UNION
								select agents.user_id  from agents where agents.parent_id in (
									select agents.user_id  from agents where agents.parent_id in (
										select agents.user_id  from agents where agents.parent_id = " . intval ($vdata['user_id']) . " and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($vdata['user_id']) . "
									) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($vdata['user_id']) . "
								) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($vdata['user_id']) . "
							");
					});
				})->where(function ($query2) use($startdate, $enddate) {
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
			}
			
			//当前页代理商数据整理
			return $this->again_modify_data_structure_currpage_total($id_list, $_one_sumdata);
		}
		
		protected function get_all_agents_sum_data($id_list, $request)
		{
			$startdate          = $request->startdate;
			$enddate            = $request->enddate;
			$searchtype         = $request->searchtype;
			
			foreach ($id_list as $key => $vdata) {
				$_one_sumdata[$vdata['user_id']]['all_total'] = Mt4Trades::selectRaw ("
					/*代理商余额入金*/
					sum( case when mt4_trades.PROFIT > 0 and mt4_trades.CMD = 6 and mt4_trades.COMMENT NOT LIKE '%-FY' then mt4_trades.PROFIT else 0 end ) as all_total_yuerj,
					/*代理商余额出金*/
					sum( case when mt4_trades.PROFIT < 0 and mt4_trades.CMD = 6 then mt4_trades.PROFIT else 0 end ) as all_total_yuecj,
					/*代理商返佣金额*/
					sum( case when mt4_trades.PROFIT > 0 AND mt4_trades.CMD = 6 AND mt4_trades.COMMENT LIKE '%-FY' THEN mt4_trades.PROFIT ELSE 0 END ) as all_total_rebate,
					/*代理商盈亏*/
					sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.PROFIT else 0 end ) as all_total_profit,
					/*代理商手续费*/
					abs( sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.COMMISSION else 0 end ) ) as all_total_comm,
					/*代理商贵金属*/
					 sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 1 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as all_total_noble_metal,
					/*代理商外汇*/
					 sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 2 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as all_total_for_exca,
					/*代理商原油*/
					 sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 3 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as all_total_crud_oil,
					/*代理商指数*/
					 sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 4 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as all_total_index,
					/*代理商手数 == 代理商交易量*/
					sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as all_total_volume,
					/*代理商利息*/
					sum( case when mt4_trades.SWAPS < 0 and mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.SWAPS else 0 end ) as all_total_swaps
				")->where(function ($subQuery) use ($vdata) {
					$subQuery->whereIn('mt4_trades.LOGIN',function ($whereIn2) use($vdata) {
						$whereIn2->selectRaw("
							/*普通客户*/
							user.user_id from user where parent_id in (
								select agents.user_id  from agents where agents.parent_id in (
									select agents.user_id  from agents where agents.parent_id in (
										select agents.user_id  from agents where agents.parent_id = " . intval ($vdata['user_id']) . " and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($vdata['user_id']) . "
									) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($vdata['user_id']) . "
								) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($vdata['user_id']) . "
							)
							/*代理商*/
							UNION
							select agents.user_id  from agents where agents.parent_id in (
								select agents.user_id  from agents where agents.parent_id in (
									select agents.user_id  from agents where agents.parent_id = " . intval ($vdata['user_id']) . " and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($vdata['user_id']) . "
								) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($vdata['user_id']) . "
							) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($vdata['user_id']) . "
						");
					});
				})->where(function ($query2) use($startdate, $enddate) {
					if (!empty($startdate) && $this->_exte_is_Date ($startdate) && !empty($enddate) && $this->_exte_is_Date ($enddate)) {
						$query2->whereBetween('mt4_trades.CLOSE_TIME', [$startdate . ' 00:00:00', $enddate . ' 23:59:59']);
					} else {
						if (!empty($startdate) && $this->_exte_is_Date ($startdate)) {
							$query2->where('mt4_trades.CLOSE_TIME', '>=', $startdate . ' 23:59:59');
						}
						if (!empty($enddate) && $this->_exte_is_Date ($enddate)) {
							$query2->where('mt4_trades.CLOSE_TIME', '<', $enddate . ' 00:00:00');
						}
					}
				})->get()->toArray();
			}
			
			//重新整理数据结构
			return $this->all_again_modify_data_structure_total ($_one_sumdata);
		}
		
		protected function again_modify_data_structure_currpage_total($init_data, $curr_data_ary)
		{
			$_rs = array ();
			if (!empty ($init_data) && !empty($curr_data_ary)) {
				foreach ($init_data as $key => $vdata) {
					//总入金
					$_rs[$vdata['user_id']]['total_yuerj']                    = number_format ($curr_data_ary[$vdata['user_id']][0]['total_yuerj'], 2, '.', '');
					//总出金
					$_rs[$vdata['user_id']]['total_yuecj']                    = number_format ($curr_data_ary[$vdata['user_id']][0]['total_yuecj'], 2, '.', '');
					//总返佣
					$_rs[$vdata['user_id']]['total_rebate']                   = number_format ($curr_data_ary[$vdata['user_id']][0]['total_rebate'], 2, '.', '');
					//总盈亏
					$_rs[$vdata['user_id']]['total_profit']                   = number_format ($curr_data_ary[$vdata['user_id']][0]['total_profit'], 2, '.', '');
					//总手续费
					$_rs[$vdata['user_id']]['total_comm']                     = number_format ($curr_data_ary[$vdata['user_id']][0]['total_comm'], 2, '.', '');
					//总净入金 = 入金 - 出金 + 返佣
					$_rs[$vdata['user_id']]['total_net_worth']                = number_format(((number_format ($curr_data_ary[$vdata['user_id']][0]['total_yuerj'], 2, '.', '') - abs(number_format ($curr_data_ary[$vdata['user_id']][0]['total_yuecj'], 2, '.', ''))) + number_format ($curr_data_ary[$vdata['user_id']][0]['total_rebate'], 2, '.', '')), 2, '.', '');
					//总贵金属
					$_rs[$vdata['user_id']]['total_noble_metal']              =  $curr_data_ary[$vdata['user_id']][0]['total_noble_metal'] / 100;
					//总外汇
					$_rs[$vdata['user_id']]['total_for_exca']                 =  $curr_data_ary[$vdata['user_id']][0]['total_for_exca'] / 100;
					//总原油
					$_rs[$vdata['user_id']]['total_crud_oil']                 =  $curr_data_ary[$vdata['user_id']][0]['total_crud_oil'] / 100;
					//总指数
					$_rs[$vdata['user_id']]['total_index']                    =  $curr_data_ary[$vdata['user_id']][0]['total_index'] / 100;
					//总手数
					$_rs[$vdata['user_id']]['total_volume']                   =  $curr_data_ary[$vdata['user_id']][0]['total_volume'] / 100;
					//总利息
					$_rs[$vdata['user_id']]['total_swaps']                    = number_format ($curr_data_ary[$vdata['user_id']][0]['total_swaps'], 2, '.', '');
				}
			}
			
			return $_rs;
		}
		
		//重新整理条件查找出的所有userId的数据，并进行循环累加求和
		protected function all_again_modify_data_structure_total($data_ary)
		{
			$_rs = array ();
			$_search_all_total_yuerj            = $_search_all_total_yuecj = 0;
			$_search_all_total_rebate           = $_search_all_total_profit = 0;
			$_search_all_total_comm             = $_search_all_total_net_worth = 0;
			$_search_all_total_noble_metal      = $_search_all_total_for_exca = 0;
			$_search_all_total_crud_oil         = $_search_all_total_index = 0;
			$_search_all_total_volume           = $_search_all_total_swaps = 0;
			
			if (is_array ($data_ary) && !empty($data_ary)) {
				foreach ($data_ary as $vdata) {
					//总入金
					$_search_all_total_yuerj                    += number_format ($vdata['all_total'][0]['all_total_yuerj'], 2, '.', '');
					//总出金
					$_search_all_total_yuecj                    += number_format ($vdata['all_total'][0]['all_total_yuecj'], 2, '.', '');
					//总返佣
					$_search_all_total_rebate                   += number_format ($vdata['all_total'][0]['all_total_rebate'], 2, '.', '');
					//总盈亏
					$_search_all_total_profit                   += number_format ($vdata['all_total'][0]['all_total_profit'], 2, '.', '');
					//总手续费
					$_search_all_total_comm                     += number_format ($vdata['all_total'][0]['all_total_comm'], 2, '.', '');
					//总净入金 = 入金 - 出金 + 返佣
					$_search_all_total_net_worth                += number_format(((number_format ($vdata['all_total'][0]['all_total_yuerj'], 2, '.', '') - abs(number_format ($vdata['all_total'][0]['all_total_yuecj'], 2, '.', ''))) + number_format ($vdata['all_total'][0]['all_total_rebate'], 2, '.', '')), 2, '.', '');
					//总贵金属
					$_search_all_total_noble_metal              +=  $vdata['all_total'][0]['all_total_noble_metal'] / 100;
					//总外汇
					$_search_all_total_for_exca                 +=  $vdata['all_total'][0]['all_total_for_exca'] / 100;
					//总原油
					$_search_all_total_crud_oil                 +=  $vdata['all_total'][0]['all_total_crud_oil'] / 100;
					//总指数
					$_search_all_total_index                    +=  $vdata['all_total'][0]['all_total_index'] / 100;
					//总手数
					$_search_all_total_volume                   +=  $vdata['all_total'][0]['all_total_volume'] / 100;
					//总利息
					$_search_all_total_swaps                    += number_format ($vdata['all_total'][0]['all_total_swaps'], 2, '.', '');
				}
				
				$_rs = array (
					'search_all_total_yuerj'                    => number_format($_search_all_total_yuerj, 2, '.', ''),
					'search_all_total_yuecj'                    => number_format($_search_all_total_yuecj, 2, '.', ''),
					'search_all_total_rebate'                   => number_format($_search_all_total_rebate, 2, '.', ''),
					'search_all_total_profit'                   => number_format($_search_all_total_profit, 2, '.', ''),
					'search_all_total_comm'                     => number_format($_search_all_total_comm, 2, '.', ''),
					'search_all_total_net_worth'                => number_format($_search_all_total_net_worth, 2, '.', ''),
					'search_all_total_noble_metal'              => $_search_all_total_noble_metal,
					'search_all_total_for_exca'                 => $_search_all_total_for_exca,
					'search_all_total_crud_oil'                 => $_search_all_total_crud_oil,
					'search_all_total_index'                    => $_search_all_total_index,
					'search_all_total_volume'                   => number_format($_search_all_total_volume, 2, '.', ''),
					'search_all_total_swaps'                    => number_format($_search_all_total_swaps, 2, '.', ''),
				);
			}
			
			return $_rs;
		}
	}