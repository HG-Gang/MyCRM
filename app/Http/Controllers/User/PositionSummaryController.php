<?php
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/3/3
	 * Time: 10:18
	 */
	
	namespace App\Http\Controllers\User;
	
	use Redirect, Excel;
	use Illuminate\Http\Request;
	use App\Model\Mt4Users;
	use App\Model\Agents;
	use App\Model\Mt4Trades;
	use App\Model\UserTrades;
	use App\Model\SystemConfig;
	use App\Model\UserGroup;
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	
	class PositionSummaryController extends Abstract_Mt4service_Controller
	{
		public function position_summary_browse() {
			return view ('user.position_summary.position_summary_browse')->with (['_user_info' => $this->_user]);
		}
		
		public function position_summary_detail($id) {
			return view ('user.position_summary.position_summary_browse')->with (['_user_info' => $this->_user, 'id' => $id, '_isSubDatil' => 'true']);
		}
		
		//仓位总结导出Excel
		public function position_summary_export(Request $request)
		{
			$listId             = $request->listId;
			$listIdn             = $request->listIdn;
			$rows               = $request->rowsData;
			$startdate          = $request->startdate;
			$enddate            = $request->enddate;
			$ary                = array();
			for ($i = 0; $i < count($listId); $i ++) {
				for ($j =0; $j < 1; $j ++) {
					$ary[$i]['user_id'] = $listId[$i];
					$ary[$i]['user_name'] = $listIdn[$i];
				}
			}
			dd(json_decode($rows, true));
			dd($this->test_export($listId, $request));
		}
		
		public function positionSummarySearch(Request $request) {
			
			$result = array('rows' => '', 'total' => '');
			
			$_agn_info = $this->get_current_loginId_agents_id_list ('page', $request);
			if (!empty($_agn_info)) {
				//获取查找条件的说要代理商ID, 并汇总每一个ID的各种金额
				$_sumdata = $this->get_current_loginIdorsearch_agents_sum_data ($_agn_info, $request);
				
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
				$result['total'] = $this->get_current_loginId_agents_id_list ('count', $request);
				$_all_agn_info = $this->get_current_loginId_agents_id_list ('sum', $request);
				$_all_datasum = $this->get_current_loginIdorsearch_all_agents_sum_data ($_all_agn_info, $request);
				
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
					'useredit'          => '',
				]];
			}
			
			//$result['footer'] = [['user_money' => 300, 'user_id' => '', 'user_name' => '总计']];
			
			return json_encode($result);
		}
		
		
		protected function get_current_loginId_agents_id_list($searcyType, $request) {
			
			$userId             = $request->userId;
			$userName           = $request->userName;
			$userPId            = $request->userPId;
			$searchtype         = $request->searchtype;
			$loginId            = $this->_user;
			
			$query_sql = Agents::selectRaw('agents.user_id, agents.user_name')
				->leftjoin('mt4_users', function ($leftjoin)  {
					$leftjoin->on('mt4_users.LOGIN', ' = ', 'agents.user_id');
				})
				/*->where(function ($subWhere) use ($loginId, $searchtype, $userId) {
					if ($searchtype == 'autoSearch') {
						$subWhere->where('agents.user_id', $loginId['user_id']);
					} else {
						if ($searchtype == 'clickSearch' && $loginId['user_id'] == $userId) {
							$subWhere->where('agents.user_id', $loginId['user_id']);
						} else if ($searchtype == 'clickSearch') {
							$subWhere->where('agents.parent_id', $userId);
							$subWhere->whereIn('agents.user_id', function ($query) use ($loginId, $searchtype) {
								//查直属和间接直属 parent_id = $loginId['user_id']
								$query->selectRaw("
									agents.user_id from agents where agents.parent_id in (
										select agents.user_id from agents where agents.parent_id in (
											select agents.user_id  from agents where agents.parent_id = " . intval ($loginId['user_id']) . " and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
										) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
									) and agents.voided = '1' and agents.user_status in ('0','1','2','4')
								");
							});
						}
					}
				})*/
				->whereRaw("agents.voided = '1' and agents.user_status in ('0', '1', '2', '4')")
				->where(function ($subQuery) use ($loginId, $userId, $userName, $userPId, $searchtype) {
					if ($searchtype == 'autoSearch') {
						$subQuery->where('agents.user_id', $loginId['user_id']);
					} else {
						if ($searchtype == 'clickSearch') {
							if (!empty($userId)) {
								$subQuery->where('agents.user_id', $userId)->whereIn('agents.user_id', function ($subQuery2) use ($loginId) {
									$subQuery2->selectRaw("
										agents.user_id from agents where agents.parent_id in (
											select agents.user_id from agents where agents.parent_id in (
												select agents.user_id  from agents where agents.parent_id = " . intval ($loginId['user_id']) . " and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
											) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
										) and agents.voided = '1' and agents.user_status in ('0','1','2','4')
									");
								});
							} else {
								$subQuery->where('agents.user_id', $loginId['user_id']);
							}
						}
						
						if (!empty($userName) && $searchtype == 'clickSearch') {
							$subQuery->where('agents.user_name', 'like',  '%'. $userName .'%');
						}
						
						if (!empty($userPId) && $searchtype == 'subAgentsSearch') {
							$subQuery->where('agents.parent_id', $userPId)/*->orWhere('agents.user_id', $userPId)*/;
						}
					}
				});
				
				if ($searcyType == 'page') {
					$data_list = $query_sql->skip($this->_offset)->take($this->_pageSize)->orderBy('agents.user_id', 'asc')->get()->toArray();
				} else if ($searcyType == 'count') {
					$data_list = $query_sql->count();
				} else if ($searcyType == 'sum') {
					$data_list = $query_sql->get()->toArray();
				}
			
			return $data_list;
		}
		
		protected function get_current_loginIdorsearch_agents_sum_data($id_list, $request) {
			
			$startdate          = $request->startdate;
			$enddate            = $request->enddate;
			$searchtype         = $request->searchtype;
			$loginId            = $this->_user;
			//得到每一个查找出的每一个代理商的所有相关各种金额汇总
			//汇总当前页的代理商各种金额
			foreach ($id_list as $key => $vdata) {
				//计算自己和自己的直属客户
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
					/*代理商能源*/
					 sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 2 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as total_for_exca,
					/*代理商外汇*/
					 sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 3 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as total_crud_oil,
					/*代理商指数*/
					 sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 4 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as total_index,
					/*代理商手数 == 代理商交易量*/
					sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as total_volume,
					/*代理商利息*/
					sum( case when mt4_trades.SWAPS < 0 and mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.SWAPS else 0 end ) as total_swaps
				")->where(function ($subQuery) use ($loginId, $searchtype, $vdata) {
					if ($loginId['user_id'] == $vdata['user_id'] && $searchtype == 'subAgentsSearch') {
						$subQuery->whereIn('mt4_trades.LOGIN',function ($whereIn1) use($loginId) {
							$whereIn1->selectRaw("
								/*当前登录ID*/
								agents.user_id from agents where agents.user_id = " . intval ($loginId['user_id']) . " and agents.voided = '1' and agents.user_status in ('0','1','2','4')
								UNION
								/*当前登录ID的直属客户*/
								select user.user_id from user where parent_id in (
									select agents.user_id from agents where agents.user_id = " . intval ($loginId['user_id']) . " and agents.voided = '1' and agents.user_status in ('0','1','2','4')
								)
							");
						});
					} else {
						$subQuery->whereIn('mt4_trades.LOGIN',function ($whereIn2) use($loginId, $vdata) {
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
					}
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
			$_currpage_total = $this->again_modify_data_structure_total ($id_list, $_one_sumdata);
			
			return $_currpage_total;
		}
		
		protected function get_current_loginIdorsearch_all_agents_sum_data ($id_list, $request)
		{
			$startdate          = $request->startdate;
			$enddate            = $request->enddate;
			
			foreach ($id_list as $key => $vdata) {
				//计算自己和自己的直属客户
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
					/*代理商能源*/
					 sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 2 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as all_total_for_exca,
					/*代理商外汇*/
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
							$query2->where('mt4_trades.CLOSE_TIME', '>=', $startdate . '  23:59:59');
						}
						if (!empty($enddate) && $this->_exte_is_Date ($enddate)) {
							$query2->where('mt4_trades.CLOSE_TIME', '<', $enddate . ' 00:00:00');
						}
					}
				})->get()->toArray();
			}
			
			//当前登录理商所有数据统计汇总
			$_all_total = $this->all_again_modify_data_structure_total ($_one_sumdata);
			
			return $_all_total;
		}
		
		//整理当前页数据汇总
		protected function again_modify_data_structure_total ($init_data, $curr_data_ary) {
			
			$_rs = array ();
			
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
				//总能源
				$_rs[$vdata['user_id']]['total_for_exca']                 =  $curr_data_ary[$vdata['user_id']][0]['total_for_exca'] / 100;
				//总外汇
				$_rs[$vdata['user_id']]['total_crud_oil']                 =  $curr_data_ary[$vdata['user_id']][0]['total_crud_oil'] / 100;
				//总指数
				$_rs[$vdata['user_id']]['total_index']                    =  $curr_data_ary[$vdata['user_id']][0]['total_index'] / 100;
				//总手数
				$_rs[$vdata['user_id']]['total_volume']                   =  $curr_data_ary[$vdata['user_id']][0]['total_volume'] / 100;
				//总利息
				$_rs[$vdata['user_id']]['total_swaps']                    = number_format ($curr_data_ary[$vdata['user_id']][0]['total_swaps'], 2, '.', '');
			}
			
			return $_rs;
		}
		
		protected function all_again_modify_data_structure_total ($data_ary)
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
		
		//TODO 等待测试, 定时计算返佣逻辑
		public function _exte_mt4_sync_comm_summary()
		{
			//先检查列是否为 1
			$no = 0;
			$chk = SystemConfig::select('trades_start')->first();
			
			if ($chk['trades_start'] == '0') {
				//上次请求处理已经完成或者上次请求没有数据
				$orderNo = Mt4Trades::select('TICKET', 'LOGIN', 'SYMBOL', 'CMD', 'VOLUME', 'PROFIT', 'COMMISSION', 'MODIFY_TIME')
					->whereIn('CMD',array(0, 1, 2, 3, 4, 5))->where('CLOSE_TIME', '>', '1970-01-01 00:00:00')
					->where('VOLUME', '<>', '0')->where('COMMISSION', '<>', 0)
					->where('CONV_RATE1', '<>', 0)->where('LOGIN', '>=', '200001')
					->whereNotIn('TICKET', function ($query) {
						$query->selectRaw("user_trades.ticket from user_trades where voided = '1'");
					})
					->limit(30)->get()->toArray();
				
				$data = $this->_exte_get_all_parentId($orderNo);
				$_order_info = $this->_exte_format_data($data);
				
				//$_info = $_deposit_amt = array();
				$_comm_amt = 0;
				if (!empty($_order_info)) {
					//有数据，开始计算返佣时间
					foreach ($_order_info as $k => $v) {
						if ($v['self']['group_id'] == '0') {
							$_info[$k] = $this->_exte_mt4_rebate_comp_record($v['self'], 'NOFY');
						} else if ($v['self']['parent_id'] == '0') {
							$_info[$k] = $this->_exte_mt4_rebate_comp_record($v['self'], 'NOFY' . '-Pid-' . $v['self']['parent_id']);
						} else {
							foreach ($v['perntdId'] as $kp => $pv) {
								if ($pv['settlement_model'] == '1') {
									$cmt_red = '#' . $pv['orderno'] . self::FY;
									if ($v['self']['group_id'] == '1') {
										//有佣金用户直属上级返佣结果
										$_comm_amt = ($pv['volume'] / 100) * ($pv['comm_prop_rs'] / 100) * $pv['grup_radix'];
									} else if ($v['self']['group_id'] == '0') {
										//无佣金用户直属上级返佣结果 -50
										$_comm_amt = ($pv['volume'] / 100) * (($pv['comm_prop_rs'] / 100) * $pv['grup_radix'] - 50);
									}
									
									/*$ary[$k][$kp] = $pv;
									$ary[$k][$kp]['comm_amt'] = $_comm_amt;
									$ary[$k][$kp]['self'] = $v['self'];*/
									if ($_comm_amt > 0) {
										//返佣前先检查当前订单是否已经返佣过
										$_chk_order = Mt4Trades::where('LOGIN', $pv['user_id'])->where('COMMENT', 'like', '%' . $cmt_red . '%')->where('CMD', 6)->first();
										if (empty($_chk_order)) {
											$_cmt = $v['self']['user_id'] . '-#' . $v['self']['orderno'] . self::FY;
											$_deposit_amt[$kp] = $this->_exte_mt4_deposit_amount($pv['user_id'], $_comm_amt, $_cmt);
										}
										
										$_results = $this->_handle_commission_deposit_amt_results($_deposit_amt);
										
										if (!in_array('NG', $_results, true)) {
											$_info[$k][$kp] = $this->_exte_mt4_rebate_comp_record($v['self']);
										}
									}
								} else if ($pv['settlement_model'] == '2') {
									$_info[$k][$kp] = $this->_exte_mt4_rebate_comp_record($v['self'],'NOFY' . '-'. $pv['settlement_model']);
								}
							}
						}
						
						$no ++;
					}
					
					if ((int)$no == count($_order_info)) {
						//单次请求处理的数据已经完成，更新列的值 为 0
						$upd = SystemConfig::where('sys_id', 1)->update(['trades_start' => '0']);
					}
					
					$this->debugfile($_info, '开始返佣', 'FanYong' . '-' . date('Ymd'));
				}
				//return $_info;
			}
		}
		
		protected function _exte_get_all_parentId ($data)
		{
			$_rs = [];
			if (!empty($data)) {
				foreach ($data as $k => $v) {
					$_table = $this->_exte_get_table_obj($data[$k]['LOGIN']);
					
					//先查出自己的信息
					$list[$k] = $_table::select('user_id', 'parent_id', 'mt4_grp', 'group_id', 'comm_prop')->where('voided', '1')->where('user_id', $data[$k]['LOGIN'])->get()->toArray();
					
					//得到自己的基数
					$grup_radix[$k] = UserGroup::select('user_group_name', 'user_grup_radix', 'group_id')->where('user_group_name',  $list[$k][0]['mt4_grp'])->where('voided', '1')->get()->toArray();
					$_rs[$k]['info']['login']           = $v['LOGIN'];
					$_rs[$k]['info']['symbol']          = $v['SYMBOL'];
					$_rs[$k]['info']['cmd']             = $v['CMD'];
					$_rs[$k]['info']['profit']          = $v['PROFIT'];
					$_rs[$k]['info']['commission']      = $v['COMMISSION'];
					$_rs[$k]['info']['modify_time']     = $v['MODIFY_TIME'];
					$_rs[$k]['info']['orderno']         = $v['TICKET'];
					$_rs[$k]['info']['volume']          = $v['VOLUME'];
					$_rs[$k]['self']['user_id']         = $list[$k][0]['user_id'];
					$_rs[$k]['self']['parent_id']       = $list[$k][0]['parent_id'];
					$_rs[$k]['self']['mt4_grp']         = $list[$k][0]['mt4_grp'];
					$_rs[$k]['self']['comm_prop']       = $list[$k][0]['comm_prop'];
					$_rs[$k]['self']['group_id']        = $grup_radix[$k][0]['group_id'];
					$_rs[$k]['self']['grup_radix']      = $grup_radix[$k][0]['user_grup_radix'];
					$_rs[$k]['parent_id']               = $this->_exte_get_all_subordinate11(($list[$k][0]['parent_id'] == 0) ? $list[$k][0]['user_id'] : $list[$k][0]['parent_id'], 0, $list[$k][0]['comm_prop']);
				}
			}
			
			return $_rs;
		}
		
		protected function _exte_get_all_subordinate11 ($pid, $k, $self_comm_prop)
		{
			global $_rsp;
			
			$_info = Agents::select('user_id', 'parent_id', 'mt4_grp', 'group_id', 'comm_prop', 'settlement_model')
			->where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))
			->where('user_id', $pid)->get()->toArray();
			//得到代理商自己的基数
			$grup_radix = UserGroup::select('user_group_name', 'user_grup_radix', 'group_id')->where('user_group_name', $_info[0]['mt4_grp'])->where('voided', '1')->get()->toArray();
			
			$_rsp[$k]['is_direct']				= ($k == 0) ? 'Yes' : 'No';
			$_rsp[$k]['user_id']				= $_info[0]['user_id'];
			$_rsp[$k]['parent_id']				= $_info[0]['parent_id'];
			$_rsp[$k]['mt4_grp']				= $_info[0]['mt4_grp'];
			$_rsp[$k]['group_id']				= $_info[0]['group_id'];
			$_rsp[$k]['comm_prop'] 				= $_info[0]['comm_prop'];
			$_rsp[$k]['settlement_model']		= $_info[0]['settlement_model'];
			$_rsp[$k]['comm_prop_rs']			= $_info[0]['comm_prop'] - $self_comm_prop;
			$_rsp[$k]['grup_radix']				= $grup_radix[0]['user_grup_radix'];
			
			if((int)$_info[0]['parent_id'] != 0) {
				self::_exte_get_all_subordinate11($_info[0]['parent_id'], $k + 1, $_info[0]['comm_prop']);
			}
			
			$rala = $_rsp;
			unset($GLOBALS['_rsp']);
			return $rala;
		}
		
		protected function _exte_format_data ($data)
		{
			$ary = array();
			
			foreach ($data as $k1 => $val) {
				if (!empty($val['parent_id'])) {
					foreach ($val['parent_id'] as $k => $v) {
						$ary[$k1]['self']['orderno']						= $val['info']['orderno'];
						$ary[$k1]['self']['volume']							= $val['info']['volume'];
						$ary[$k1]['self']['login']							= $val['info']['login'];
						$ary[$k1]['self']['symbol']							= $val['info']['symbol'];
						$ary[$k1]['self']['cmd']							= $val['info']['cmd'];
						$ary[$k1]['self']['profit']							= $val['info']['profit'];
						$ary[$k1]['self']['commission']						= $val['info']['commission'];
						$ary[$k1]['self']['modify_time']					= $val['info']['modify_time'];
						$ary[$k1]['self']['user_id']						= $val['self']['user_id'];
						$ary[$k1]['self']['parent_id']						= $val['self']['parent_id'];
						$ary[$k1]['self']['mt4_grp']						= $val['self']['mt4_grp'];
						$ary[$k1]['self']['comm_prop']						= $val['self']['comm_prop'];
						$ary[$k1]['self']['group_id']						= $val['self']['group_id'];
						$ary[$k1]['self']['grup_radix']						= $val['self']['grup_radix'];
						$ary[$k1]['perntdId'][$k]['is_direct']				= $v['is_direct'];
						$ary[$k1]['perntdId'][$k]['user_id']				= $v['user_id'];
						$ary[$k1]['perntdId'][$k]['parent_id']				= $v['parent_id'];
						$ary[$k1]['perntdId'][$k]['mt4_grp']				= $v['mt4_grp'];
						$ary[$k1]['perntdId'][$k]['group_id']				= $v['group_id'];
						$ary[$k1]['perntdId'][$k]['comm_prop']				= $v['comm_prop'];
						$ary[$k1]['perntdId'][$k]['comm_prop_rs']			= $v['comm_prop_rs'];
						$ary[$k1]['perntdId'][$k]['settlement_model']		= $v['settlement_model'];
						$ary[$k1]['perntdId'][$k]['grup_radix']				= $v['grup_radix'];
						$ary[$k1]['perntdId'][$k]['orderno']				= $val['info']['orderno'];
						$ary[$k1]['perntdId'][$k]['volume']					= $val['info']['volume'];
					}
				}
 			}
 			
 			return $ary;
		}
		
		protected function _exte_mt4_rebate_comp_record ($record, $comment = '')
		{
			
			$trades_upd = UserTrades::create([
				'user_id'				=> $record['login'],
				'ticket'				=> $record['orderno'],
				'symbol'				=> $record['symbol'],
				'cmd'					=> $record['cmd'],
				'volume'				=> $record['volume'],
				'profit'				=> $record['profit'],
				'commission'			=> $record['commission'],
				'comment'               => $comment,
				'modify_time'			=> $record['modify_time'],
				'voided'				=> '1',
				'rec_comp_date'			=> date('Y-m-d H:i:s'),
			]);
			
			return $trades_upd;
		}
		
		protected function _handle_commission_deposit_amt_results ($_rs_ary)
		{
			if (!empty($_rs_ary)) {
				foreach ($_rs_ary as $k => $v) {
					if ($v['ret'] != '0') {
						$_tmp_ary[] = 'NG';
					} else {
						$_tmp_ary[] = $v['msg'];
					}
				}
			}
			
			return $_tmp_ary;
		}
		
		protected function test_export($id_list, $request)
		{
			return $this->get_current_loginIdorsearch_all_agents_sum_data($id_list, $request);
		}
	}