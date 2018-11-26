<?php
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/3/15
	 * Time: 16:34
	 */
	
	namespace App\Http\Controllers\User;
	
	use App\Model\Mt4Users;
	use Illuminate\Http\Request;
	use App\Model\User;
	use App\Model\Agents;
	use App\Model\AgentsGroup;
	use App\Model\Mt4Trades;
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	
	class ProxyListController extends Abstract_Mt4service_Controller
	{
		
		public function proxy_list_browse ()
		{
			return view ('user.proxy_list.proxy_list_browse')->with (['_user_info' => $this->_user]);
		}
		
		public function proxy_confirm_browse ()
		{
			return view ('user.proxy_list.proxy_confirm_browse')->with (['_user_info' => $this->_user]);
		}
		
		public function proxy_direct_cust_detail ($puid)
		{
			return view ('user.proxy_list.proxy_direct_cust_detail')->with (['_user_info' => $this->_user, 'puid' => $puid]);
		}
		
		public function proxyListSearch (Request $request)
		{
			
			$result = array ('rows' => '', 'total' => '');
			
			$_rs = $this->get_current_agents_proxy_id_list ('page', $request);
			
			if (!empty($_rs)) {
				$_upd = $this->_exte_mt4_batch_update_user_info ($_rs, new Agents());
				$_ag_sumdadta = $this->get_current_page_agents_sumdata($_rs);
				$_datasum = $this->get_current_agents_proxy_sumdata($request);
				for ($i = 0; $i < count ($_rs); $i ++) {
					$_rs[$i]['agentsTotal']         = Agents::where('parent_id', $_rs[$i]['user_id'])->whereIn('voided', array ('1', '2'))->whereIn('user_status', array('0', '1', '2', '4'))->count();
					$_rs[$i]['accountTotal']        = User::where('parent_id', $_rs[$i]['user_id'])->whereIn('voided', array ('1', '2'))->whereIn('user_status', array('0', '1', '2', '4'))->count();;
					$_rs[$i]['fy_money']            = number_format ($_ag_sumdadta[$_rs[$i]['user_id']][0]['total_fy'], '2', '.', '');
					$_rs[$i]['rj_money']            = number_format ($_ag_sumdadta[$_rs[$i]['user_id']][0]['total_rj'], '2', '.', '');
					$_rs[$i]['qk_money']            = number_format ($_ag_sumdadta[$_rs[$i]['user_id']][0]['total_qk'], '2', '.', '');
				}
				
				$result['rows']                     = $_rs;
				$result['total']                    = $this->get_current_agents_proxy_id_list ('count', $request);
				$result['footer']                   = [[
					'user_id'               => '总计',
					'user_name'             => '',
					'agentsTotal'           => '',
					'accountTotal'          => '',
					'user_money'            => $_datasum['all_total_bal'],
					'cust_eqy'              => $_datasum['all_total_eqy'],
					'fy_money'              => $_datasum['all_total_fy'],
					'rj_money'              => $_datasum['all_total_rj'],
					'qk_money'              => $_datasum['all_total_qk'],
					'rec_crt_date'          => '',
					'comm_trans'            => '',
				]];
			}
			
			return json_encode ($result);
		}
		
		//待确认代理
		public function proxyConfirmSearch (Request $request)
		{
			$result = array ('rows' => '', 'total' => '');
			
			$_rs = $this->get_current_agents_proxy_confirm_id_list ('page', $request);
			
			if (!empty($_rs)) {
				
				$result['rows']                     = $_rs;
				$result['total']                    = $this->get_current_agents_proxy_confirm_id_list ('count', $request);
				$result['footer']                   = [[]];
			}
			
			return json_encode ($result);
		}
		
		public function confirmLevelChange (Request $request)
		{
			$userId         = $request->userId;
			$gId            = $request->gId;
			$gName          = $request->gName;
			
			//检查gId 的合法性
			$chk = AgentsGroup::where('voided', '1')->find($gId);
			
			if (!$chk) {
				return response ()->json ([
					'msg'       => 'FAIL',
					'err'       => 'NOTEXISTGID',
				]);
			}
			
			$_table = $this->_exte_get_table_obj($userId);
			
			$num = $_table::where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))
				->find($userId)->update([
					'is_confirm_agents_lvg'     => '1',
					'group_id'                  => $gId,
					'rec_upd_user'              => $this->_user['user_name'],
					'rec_upd_date'              => date('Y-m-d H:i:s'),
				]);
			
			if ($num) {
				return response ()->json ([
					'msg'       => 'SUC',
					'err'       => 'NOTERR',
				]);
			} else {
				return response ()->json ([
					'msg'       => 'FAIL',
					'err'       => 'CONFIRMERR',
				]);
			}
		}
		
		public function direct_cust_detail_list (Request $request)
		{
			
			$puid       = $request->puid;
			$searchtype = $request->searchtype;
			$result     = array ('rows' => '', 'total' => '');
			
			//先查出条件的数据，再将mt4_users相对应数据更新User表，然后再查出来
			$_rs = $this->get_agents_proxy_cust_id_list('page', $puid);
			if (!empty($_rs)) {
				$_upd = $this->_exte_mt4_batch_update_user_info ($_rs, new User());
				$_rs = $this->get_agents_proxy_cust_id_list('page', $puid);
				$_page_sumdata = $this->get_agents_proxy_direct_cust_sumdata($_rs, 'pageTotal');
				$_all_sumdata = $this->get_agents_proxy_direct_cust_sumdata($puid, 'allPageTotal');
				
				//整理 $_id_list 和 $_page_sumdata
				foreach ($_rs as $key => $vdata) {
					$_rs[$key]['total_yuerj']                 = $_page_sumdata[$_rs[$key]['user_id']]['total_yuerj'];
					$_rs[$key]['total_yuecj']                 = $_page_sumdata[$_rs[$key]['user_id']]['total_yuecj'];
					$_rs[$key]['total_profit']                = $_page_sumdata[$_rs[$key]['user_id']]['total_profit'];
					$_rs[$key]['total_comm']                  = $_page_sumdata[$_rs[$key]['user_id']]['total_comm'];
					$_rs[$key]['total_net_worth']             = $_page_sumdata[$_rs[$key]['user_id']]['total_net_worth'];
					$_rs[$key]['total_noble_metal']           = $_page_sumdata[$_rs[$key]['user_id']]['total_noble_metal'];
					$_rs[$key]['total_for_exca']              = $_page_sumdata[$_rs[$key]['user_id']]['total_for_exca'];
					$_rs[$key]['total_crud_oil']              = $_page_sumdata[$_rs[$key]['user_id']]['total_crud_oil'];
					$_rs[$key]['total_index']                 = $_page_sumdata[$_rs[$key]['user_id']]['total_index'];
					$_rs[$key]['total_volume']                = $_page_sumdata[$_rs[$key]['user_id']]['total_volume'];
					$_rs[$key]['total_swaps']                 = $_page_sumdata[$_rs[$key]['user_id']]['total_swaps'];
				}
				
				$result['rows'] = $_rs;
				$result['total'] = $this->get_agents_proxy_cust_id_list('count', $puid);
				$result['footer'] = [[
					'user_group'            => '总计',
					'user_id'               => '',
					'user_name'             => '',
					'user_money'            => $_all_sumdata['search_all_total_bal'], //总余额
					'cust_eqy'              => $_all_sumdata['search_all_total_eqy'], //总净值
					'total_yuerj'           => $_all_sumdata['search_all_total_yuerj'], //总入金
					'total_yuecj'           => $_all_sumdata['search_all_total_yuecj'], //总出金
					'total_net_worth'       => $_all_sumdata['search_total_net_worth'], //总净入金 = 入金 - 出金
					'total_comm'            => $_all_sumdata['search_all_total_comm'], //总手续费
					'total_profit'          => $_all_sumdata['search_all_total_profit'], //总盈亏
					'total_noble_metal'     => $_all_sumdata['search_all_total_noble_metal'], //总贵金属
					'total_for_exca'        => $_all_sumdata['search_all_total_for_exca'], //总外汇
					'total_crud_oil'        => $_all_sumdata['search_all_total_crud_oil'], //总原油
					'total_index'           => $_all_sumdata['search_all_total_index'], //总指数
					'total_volume'          => $_all_sumdata['search_all_total_volume'], //总手数
					'total_swaps'           => $_all_sumdata['search_all_total_swaps'], //总利息
				]];
			}
			
			return json_encode ($result);
		}
		
		//直属代理商和客户佣金转户
		public function direct_user_commTrans_browse ($uid) {
			
			return view ('user.proxy_list.direct_cust_commtrans_browse')->with (['_user_info' => $this->_user, 'uid' => $uid]);
		}
		
		//佣金转户
		public function directUserCommTrans(Request $request)
		{
			$depositId      = $request->depositId;
			$comm_money     = $request->comm_money;
			$password       = $request->password;
			
			if ($this->_user['enable_readonly'] == 1 || $this->_user['is_out_money'] == '1') {
				return response()->json([
					'msg'           => 'FAIL',
					'errorType'     => 'NOTALLOW',
				]);
			}
			
			$mt4 = $this->_exte_mt4_verify_password ($this->_user['user_id'], $password);
			$cmt = $this->_user['user_id'] . self::ZH;
			if (is_array($mt4) && $mt4['ret'] == '0') {
				$withdraw = $this->_exte_mt4_withdrawal_amount($this->_user['user_id'], $comm_money, $cmt);
				if(!is_array($withdraw)) { //TODO 连接失败，资金未出
					return response()->json([
						'msg'           => 'FAIL',
						'errorType'     => '_CONNECT_FAILED_',
					]);
				} else if (is_array($withdraw) && $withdraw['ret'] == '0') {
					$deposit = $this->_exte_mt4_deposit_amount($depositId, $comm_money, $cmt);
					if(is_array($deposit) && $deposit['ret'] == '0') {
						//MT4出入金成功
						return response()->json([
							'msg'        => 'SUCCESS',
						]);
					} else {
						//接收转入的 资金 失败， 转出资金 退回, 出金用户资金回流
						$withdrawal_cmt = $this->_user['user_id'] . '#' . $withdraw['order'] . self::TH;
						$withdrawal = $this->_exte_mt4_deposit_amount($this->_user['user_id'], $comm_money, $withdrawal_cmt);
						return response()->json([
							'msg'           => 'FAIL',
							'errorType'     => 'MT4_data_no_sync',
						]);
					}
				} else {
					// 如果 连接成功，但佣金转户过程中失败， 此时佣金未减去
					return response()->json([
						'msg'           => 'FAIL',
						'errorType'     => '_CONNECT_FAILED_',
					]);
				}
			} else {
				return response()->json([
					'msg'           => 'FAIL',
					'errorType'     => 'ErrorPassword',
				]);
			}
		}
		
		protected function get_current_agents_proxy_id_list ($search, $request)
		{
			
			$userId         = $request->userId;
			$username       = $request->username;
			$userstatus     = $request->userstatus;
			$userPId        = $request->userPId;
			$usertype       = $request->usertype;
			$searchtype     = $request->searchtype;
			$startdate      = $request->startdate;
			$enddate        = $request->enddate;
			$loginId        = $this->_user;
			$id_list        = array ();
			
			$query_sql = Agents::select(
				'user_id', 'user_name', 'parent_id', 'group_id', 'user_money',
				'rights as rights', 'comm_prop as commprop',
				'cust_eqy', 'user_status', 'IDcard_status', 'bank_status', 'rec_crt_date'
			)->whereIn('agents.voided', array ('1', '2'))
			->whereIn('agents.user_status', array('0', '1', '2', '4'))
			->where(function ($subWhere) use ($loginId, $userId, $username, $userPId, $userstatus, $startdate, $enddate, $searchtype) {
				if ($searchtype == 'autoSearch' || $searchtype == 'clickSearch') {
					if (!empty($startdate) && !empty($enddate) && $this->_exte_is_Date ($startdate) && $this->_exte_is_Date ($enddate)) {
						$subWhere->whereBetween('agents.rec_crt_date', [$startdate .' 00:00:00', $enddate . ' 23:59:59']);
					} else {
						if(!empty($startdate) && $this->_exte_is_Date ($startdate)) {
							$subWhere->where('agents.rec_crt_date',  '>= ', $startdate .' 23:59:59');
						}
						if(!empty($enddate) && $this->_exte_is_Date ($enddate)) {
							$subWhere->where('agents.rec_crt_date', '<', $enddate .' 00:00:00');
						}
					}
					
					if ($searchtype == 'autoSearch') {
						$subWhere->where('agents.parent_id', $loginId['user_id']);
					} else if ($searchtype == 'clickSearch') {
						if (!empty($userId)) {
							$subWhere->where('agents.user_id', 'like', '%' . $userId . '%');
						} else {
							$subWhere->where('agents.parent_id', $loginId['user_id']);
							/*$subWhere->whereIn('agents.user_id', function ($whereIn) use ($loginId) {
								$whereIn->selectRaw("
									agents.user_id from agents where agents.parent_id in (
										select agents.user_id  from agents where agents.parent_id in (
											select agents.user_id  from agents where agents.parent_id = " . intval ($loginId['user_id']) . " and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
										) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
									) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
								");
							});*/
						}
					}
					if (!empty($username)) {
						$subWhere->where('agents.user_name', 'like', '%' . $username . '%');
					}
					if ($userstatus != '') {
						$subWhere->where('agents.user_status', $userstatus);
					}
				}
				
				if (!empty($userPId) && $searchtype == 'subSearch') {
					$subWhere->where('agents.parent_id', $userPId);
				}
			});
			
			if ($search == 'page') {
				$id_list = $query_sql->skip($this->_offset)->take($this->_pageSize)->orderBy('rec_crt_date', 'desc')->get()->toArray();
			} else if ($search == 'count') {
				$id_list = $query_sql->count();
			} else if ($search == 'sum') {
				$id_list = $query_sql->get()->toArray();
			}
			
			return $id_list;
		}
		
		protected function get_current_page_agents_sumdata ($data) {
			
			$_sumdata = array ();
			
			foreach ($data as $key => $vdata) {
				//分页返佣，入金，出金
				$_sumdata[$vdata['user_id']] = Mt4Trades::selectRaw('
				/*返佣*/
				sum(case when mt4_trades.CMD = 6 and mt4_trades.COMMENT LIKE "%-FY" then mt4_trades.PROFIT else 0 end) as total_fy,
				/*入金*/
				sum(case when mt4_trades.PROFIT > 0 and mt4_trades.CMD = 6 and mt4_trades.COMMENT NOT LIKE "%-FY" and mt4_trades.COMMENT NOT LIKE "%Adj%" then mt4_trades.PROFIT else 0 end) as total_rj,
				/*取款 出金*/
				sum(case when mt4_trades.PROFIT < 0 and mt4_trades.CMD = 6 and mt4_trades.COMMENT NOT LIKE "%Adj%"  then mt4_trades.PROFIT else 0 end) as total_qk
				')->where('MT4_TRADES.LOGIN', $vdata['user_id'])->get()->toArray();
			}
			
			return $_sumdata;
		}
		
		protected function get_current_agents_proxy_sumdata ($request)
		{
			
			$userId         = $request->userId;
			$username       = $request->username;
			$userstatus     = $request->userstatus;
			$userPId        = $request->userPId;
			$usertype       = $request->usertype;
			$searchtype     = $request->searchtype;
			$startdate      = $request->startdate;
			$enddate        = $request->enddate;
			$loginId        = $this->_user;
			
			$_all_sumdata[$loginId['user_id']]['fy_rj_qk'] = Mt4Trades::selectRaw('
				/*返佣*/
				sum(case when mt4_trades.CMD = 6 and mt4_trades.COMMENT LIKE "%-FY" then mt4_trades.PROFIT else 0 end) as all_total_fy,
				/*入金*/
				sum(case when mt4_trades.PROFIT > 0 and mt4_trades.CMD = 6 and mt4_trades.COMMENT NOT LIKE "%-FY" and mt4_trades.COMMENT NOT LIKE "%Adj%"  then mt4_trades.PROFIT else 0 end) as all_total_rj,
				/*取款 出金*/
				sum(case when mt4_trades.PROFIT < 0 and mt4_trades.CMD = 6 and mt4_trades.COMMENT NOT LIKE "%Adj%"  then mt4_trades.PROFIT else 0 end) as all_total_qk
			')->whereIn('mt4_trades.LOGIN', function ($whereIn) use ($loginId, $userId, $username, $userstatus, $userPId, $startdate, $enddate, $searchtype) {
				$whereIn->select('agents.user_id')->from('agents')
					->where(function ($subWhere)use ($loginId, $userId, $username, $userstatus, $userPId, $startdate, $enddate, $searchtype) {
						if ($searchtype == 'autoSearch' || $searchtype == 'clickSearch') {
							if (!empty($startdate) && !empty($enddate) && $this->_exte_is_Date ($startdate) && $this->_exte_is_Date ($enddate)) {
								$subWhere->whereBetween('agents.rec_crt_date', [$startdate .' 00:00:00', $enddate . ' 23:59:59']);
							} else {
								if(!empty($startdate) && $this->_exte_is_Date ($startdate)) {
									$subWhere->where('agents.rec_crt_date',  '>= ', $startdate .' 23:59:59');
								}
								if(!empty($enddate) && $this->_exte_is_Date ($enddate)) {
									$subWhere->where('agents.rec_crt_date', '<', $enddate .' 00:00:00');
								}
							}
							
							if ($searchtype == 'autoSearch') {
								$subWhere->where('agents.parent_id', $loginId['user_id']);
							} else if ($searchtype == 'clickSearch') {
								if (!empty($userId)) {
									$subWhere->where('agents.user_id', 'like', '%' . $userId . '%');
								} else {
									$subWhere->where('agents.parent_id', $loginId['user_id']);
									/*$subWhere->whereIn('agents.user_id', function ($whereIn) use ($loginId) {
										$whereIn->selectRaw("
											agents.user_id from agents where agents.parent_id in (
												select agents.user_id  from agents where agents.parent_id in (
													select agents.user_id  from agents where agents.parent_id = " . intval ($loginId['user_id']) . " and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
												) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
											) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
										");
									});*/
								}
							}
							if (!empty($username)) {
								$subWhere->where('agents.user_name', 'like', '%' . $username . '%');
							}
							if ($userstatus != '') {
								$subWhere->where('agents.user_status', $userstatus);
							}
						}
						
						if (!empty($userPId) && $searchtype == 'subSearch') {
							$subWhere->where('agents.parent_id', $userPId);
						}
				})->whereIn('agents.voided', array ('1', '2'))
				->whereIn('agents.user_status', array('0', '1', '2', '4'));
			})->get()->toArray();
			
			//总余额，净值
			$_all_sumdata[$loginId['user_id']]['bal_eqy'] = Mt4Users::selectRaw('
				/*余额*/
				sum(mt4_users.BALANCE) as all_total_bal,
				/*净值*/
				sum(mt4_users.EQUITY) as all_total_eqy
			')->whereIn('mt4_users.LOGIN', function ($whereIn) use ($loginId, $userId, $username, $userstatus, $userPId, $startdate, $enddate, $searchtype) {
				$whereIn->select('agents.user_id')->from('agents')
					->where(function ($subWhere)use ($loginId, $userId, $username, $userstatus, $userPId, $startdate, $enddate, $searchtype) {
						if ($searchtype == 'autoSearch' || $searchtype == 'clickSearch') {
							if (!empty($startdate) && !empty($enddate) && $this->_exte_is_Date ($startdate) && $this->_exte_is_Date ($enddate)) {
								$subWhere->whereBetween('agents.rec_crt_date', [$startdate .' 00:00:00', $enddate . ' 23:59:59']);
							} else {
								if(!empty($startdate) && $this->_exte_is_Date ($startdate)) {
									$subWhere->where('agents.rec_crt_date',  '>= ', $startdate .' 23:59:59');
								}
								if(!empty($enddate) && $this->_exte_is_Date ($enddate)) {
									$subWhere->where('agents.rec_crt_date', '<', $enddate .' 00:00:00');
								}
							}
							
							if ($searchtype == 'autoSearch') {
								$subWhere->where('agents.parent_id', $loginId['user_id']);
							} else if ($searchtype == 'clickSearch') {
								if (!empty($userId)) {
									$subWhere->where('agents.user_id', 'like', '%' . $userId . '%');
								} else {
									$subWhere->where('agents.parent_id', $loginId['user_id']);
									/*$subWhere->whereIn('agents.user_id', function ($whereIn) use ($loginId) {
										$whereIn->selectRaw("
											agents.user_id from agents where agents.parent_id in (
												select agents.user_id  from agents where agents.parent_id in (
													select agents.user_id  from agents where agents.parent_id = " . intval ($loginId['user_id']) . " and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
												) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
											) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($loginId['user_id']) . "
										");
									});*/
								}
							}
							if (!empty($username)) {
								$subWhere->where('agents.user_name', 'like', '%' . $username . '%');
							}
							if ($userstatus != '') {
								$subWhere->where('agents.user_status', $userstatus);
							}
						}
						
						if (!empty($userPId) && $searchtype == 'subSearch') {
							$subWhere->where('agents.parent_id', $userPId);
						}
					})->whereIn('agents.voided', array ('1', '2'))
					->whereIn('agents.user_status', array('0', '1', '2', '4'));
			})->get()->toArray();
			
			$_all_sumdata = array (
				'all_total_fy'          => number_format ($_all_sumdata[$loginId['user_id']]['fy_rj_qk'][0]['all_total_fy'], '2', '.', ''),
				'all_total_rj'          => number_format ($_all_sumdata[$loginId['user_id']]['fy_rj_qk'][0]['all_total_rj'], '2', '.', ''),
				'all_total_qk'          => number_format ($_all_sumdata[$loginId['user_id']]['fy_rj_qk'][0]['all_total_qk'], '2', '.', ''),
				'all_total_bal'         => number_format ($_all_sumdata[$loginId['user_id']]['bal_eqy'][0]['all_total_bal'], '2', '.', ''),
				'all_total_eqy'         => number_format ($_all_sumdata[$loginId['user_id']]['bal_eqy'][0]['all_total_eqy'], '2', '.', ''),
			);
			
			return $_all_sumdata;
		}
		
		protected function get_agents_proxy_cust_id_list ($search, $puid)
		{
			
			$id_list = array ();
			
			$query_sql = User::select(
				'user.user_id', 'user.user_name', 'user.parent_id', 'user.group_id', 'user.user_money', 'user.cust_eqy', 'user.user_status',
				'user.IDcard_status', 'user.bank_status', 'user.mt4_grp as user_group', 'user.rec_crt_date'
			)->where('user.parent_id', $puid)->whereIn('user.voided', array ('1', '2'))->whereIn('user.user_status', array('0', '1', '2', '4'));
			
			if ($search == 'page') {
				$id_list = $query_sql->skip($this->_offset)->take($this->_pageSize)->orderBy('rec_crt_date', 'desc')->get()->toArray();
			} else if ($search == 'count') {
				$id_list = $query_sql->count();
			} else if ($search == 'sum') {
				$id_list = $query_sql->get()->toArray();
			}
			
			return $id_list;
		}
		
		protected function get_agents_proxy_direct_cust_sumdata ($id, $totalType)
		{
			
			if ($totalType == 'pageTotal') {
				foreach ($id as $pv => $pd) {
					$_rs[$pd['user_id']] = Mt4Trades::selectRaw('
						/*客户余额入金*/
						sum( case when mt4_trades.PROFIT > 0 and mt4_trades.cmd in (6) and mt4_trades.COMMENT NOT LIKE "%Adj%" then mt4_trades.PROFIT else 0 end ) as total_yuerj,
						/*客户余额出金*/
						sum( case when mt4_trades.PROFIT < 0 and mt4_trades.CMD in (6) and mt4_trades.COMMENT NOT LIKE "%Adj%" then mt4_trades.PROFIT else 0 end ) as total_yuecj,
						/*手续费*/
						sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.COMMISSION else 0 end ) as total_comm,
						/*盈亏*/
						sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.PROFIT else 0 end ) as total_profit,
						/*手数*/
						sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as total_volume,
						/*利息*/
						sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 and mt4_trades.SWAPS < 0 then mt4_trades.SWAPS else 0 end ) as total_swaps,
						/*贵金属*/
						sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 1 and voided = "1" ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as total_noble_metal,
						/*外汇*/
						sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 2 and voided = "1" ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as total_for_exca,
						/*原油*/
						sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 3 and voided = "1" ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as total_crud_oil,
						/*指数*/
						sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 4 and voided = "1" ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as total_index
					')->where('mt4_trades.LOGIN',$pd['user_id'])->get()->toArray();
				}
				
				return $this->again_modify_data_structure_currpage_total($id, $_rs);
			} else if ($totalType == 'allPageTotal') {
				$_rs[$id] = Mt4Trades::selectRaw('
						/*客户余额入金*/
						sum( case when mt4_trades.PROFIT > 0 and mt4_trades.cmd in (6) and mt4_trades.COMMENT NOT LIKE "%Adj%" then mt4_trades.PROFIT else 0 end ) as all_total_yuerj,
						/*客户余额出金*/
						sum( case when mt4_trades.PROFIT < 0 and mt4_trades.CMD in (6) and mt4_trades.COMMENT NOT LIKE "%Adj%" then mt4_trades.PROFIT else 0 end ) as all_total_yuecj,
						/*手续费*/
						sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.COMMISSION else 0 end ) as all_total_comm,
						/*盈亏*/
						sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.PROFIT else 0 end ) as all_total_profit,
						/*手数*/
						sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as all_total_volume,
						/*利息*/
						sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 and mt4_trades.SWAPS < 0 then mt4_trades.SWAPS else 0 end ) as all_total_swaps,
						/*贵金属*/
						sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 1 and voided = "1" ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as all_total_noble_metal,
						/*外汇*/
						sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 2 and voided = "1" ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as all_total_for_exca,
						/*原油*/
						sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 3 and voided = "1" ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as all_total_crud_oil,
						/*指数*/
						sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 4 and voided = "1" ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as all_total_index
				')->whereIn('mt4_trades.LOGIN',function ($whereIn) use ($id) {
						$whereIn->select('user.user_id')->from('user')->where('user.parent_id', $id)->whereIn('user.voided', array ('1', '2'))->whereIn('user.user_status', array('0', '1', '2', '4'));
				})->get()->toArray();
				
				$_rs[$id]['bal_eqy'] = Mt4Users::selectRaw('
					/*余额*/
					sum(mt4_users.BALANCE) as all_total_bal,
					/*净值*/
					sum(mt4_users.EQUITY) as all_total_eqy
				')->whereIn('mt4_users.LOGIN',function ($whereIn) use ($id) {
						$whereIn->select('user.user_id')->from('user')->where('user.parent_id', $id)->whereIn('user.voided', array ('1', '2'))->whereIn('user.user_status', array('0', '1', '2', '4'));
				})->get()->toArray();
				
				return $this->again_modify_data_structure_all_total($_rs);
			}
		}
		
		protected function get_current_agents_proxy_confirm_id_list ($search, $request)
		{
			$userId         = $request->userId;
			$startdate      = $request->startdate;
			$enddate        = $request->enddate;
			
			$query_sql = Agents::selectRaw("
				agents.user_id as userId,
				agents.user_name as userName,
				agents.sex as userSex,
				agents.user_status as userStatus,
				agents.IDcard_status as IdCardStatus,
				agents.bank_status as bankStatus,
				agents.email as userEmail,
				agents.phone as userPhone,
				agents.group_id as userGroupId,
				agents.rights as userRights,
				agents.rec_crt_date as rec_crt_date
			")
			->where('agents.parent_id', $this->_user['user_id'])
			->where('agents.voided', '1')
			->whereIn('agents.user_status', array('0', '1', '2', '4'))
			->where('agents.is_confirm_agents_lvg', '0')
			->where(function ($subWhere) use ($userId, $startdate, $enddate) {
				if (!empty($startdate) && !empty($enddate) && $this->_exte_is_Date ($startdate) && $this->_exte_is_Date ($enddate)) {
					$subWhere->whereBetween('agents.rec_crt_date', [$startdate .' 00:00:00', $enddate . ' 23:59:59']);
				} else {
					if(!empty($startdate) && $this->_exte_is_Date ($startdate)) {
						$subWhere->where('agents.rec_crt_date',  '>= ', $startdate .' 23:59:59');
					}
					if(!empty($enddate) && $this->_exte_is_Date ($enddate)) {
						$subWhere->where('agents.rec_crt_date', '<', $enddate .' 00:00:00');
					}
				}
				
				if (!empty($userId)) {
					$subWhere->where('agents.user_id', $userId);
				}
			});
			
			if ($search == 'page') {
				$id_list = $query_sql->skip($this->_offset)->take($this->_pageSize)->orderBy('agents.rec_crt_date', 'desc')->get()->toArray();
			} else if ($search == 'count') {
				$id_list = $query_sql->count();
			} else if ($search == 'sum') {
				$id_list = $query_sql->get()->toArray();
			}
			
			return $id_list;
		}
		
		protected function again_modify_data_structure_currpage_total ($init_data, $curr_data) {
			
			$_rs = array ();
			
			foreach ($init_data as $key => $vdata) {
				//总入金
				$_rs[$vdata['user_id']]['total_yuerj']                    = number_format ($curr_data[$vdata['user_id']][0]['total_yuerj'], 2, '.', '');
				//总出金
				$_rs[$vdata['user_id']]['total_yuecj']                    = number_format ($curr_data[$vdata['user_id']][0]['total_yuecj'], 2, '.', '');
				//总盈亏
				$_rs[$vdata['user_id']]['total_profit']                   = number_format ($curr_data[$vdata['user_id']][0]['total_profit'], 2, '.', '');
				//总手续费
				$_rs[$vdata['user_id']]['total_comm']                     = number_format ($curr_data[$vdata['user_id']][0]['total_comm'], 2, '.', '');
				//总净入金 = 入金 - 出金
				$_rs[$vdata['user_id']]['total_net_worth']                = number_format(((number_format ($curr_data[$vdata['user_id']][0]['total_yuerj'], 2, '.', '') - abs(number_format ($curr_data[$vdata['user_id']][0]['total_yuecj'], 2, '.', '')))), 2, '.', '');
				//总贵金属
				$_rs[$vdata['user_id']]['total_noble_metal']              =  $curr_data[$vdata['user_id']][0]['total_noble_metal'] / 100;
				//总外汇
				$_rs[$vdata['user_id']]['total_for_exca']                 =  $curr_data[$vdata['user_id']][0]['total_for_exca'] / 100;
				//总原油
				$_rs[$vdata['user_id']]['total_crud_oil']                 =  $curr_data[$vdata['user_id']][0]['total_crud_oil'] / 100;
				//总指数
				$_rs[$vdata['user_id']]['total_index']                    =  $curr_data[$vdata['user_id']][0]['total_index'] / 100;
				//总手数
				$_rs[$vdata['user_id']]['total_volume']                   =  $curr_data[$vdata['user_id']][0]['total_volume'] / 100;
				//总利息
				$_rs[$vdata['user_id']]['total_swaps']                    = number_format ($curr_data[$vdata['user_id']][0]['total_swaps'], 2, '.', '');
			}
			
			return $_rs;
		}
		
		protected function again_modify_data_structure_all_total ($data) {
			
			$_rs = array ();
			
			foreach ($data as $key => $val) {
				//总余额
				$_rs['search_all_total_bal']                        = number_format ($val['bal_eqy'][0]['all_total_bal'], 2, '.', '');
				//总净值
				$_rs['search_all_total_eqy']                        = number_format ($val['bal_eqy'][0]['all_total_eqy'], 2, '.', '');
				//总入金
				$_rs['search_all_total_yuerj']                      = number_format ($val[0]['all_total_yuerj'], 2, '.', '');
				//总出金
				$_rs['search_all_total_yuecj']                      = number_format ($val[0]['all_total_yuecj'], 2, '.', '');
				//总盈亏
				$_rs['search_all_total_profit']                     = number_format ($val[0]['all_total_profit'], 2, '.', '');
				//总手续费
				$_rs['search_all_total_comm']                       = number_format ($val[0]['all_total_comm'], 2, '.', '');
				//总净入金 = 入金 - 出金
				$_rs['search_total_net_worth']                      = number_format(((number_format ($val[0]['all_total_yuerj'], 2, '.', '') - abs(number_format ($val[0]['all_total_yuecj'], 2, '.', '')))), 2, '.', '');
				//总贵金属
				$_rs['search_all_total_noble_metal']                =  $val[0]['all_total_noble_metal'] / 100;
				//总外汇
				$_rs['search_all_total_for_exca']                   =  $val[0]['all_total_for_exca'] / 100;
				//总原油
				$_rs['search_all_total_crud_oil']                   =  $val[0]['all_total_crud_oil'] / 100;
				//总指数
				$_rs['search_all_total_index']                      =  $val[0]['all_total_index'] / 100;
				//总手数
				$_rs['search_all_total_volume']                     =  $val[0]['all_total_volume'] / 100;
				//总利息
				$_rs['search_all_total_swaps']                      = number_format ($val[0]['all_total_swaps'], 2, '.', '');
			}
			
			return $_rs;
		}
	}