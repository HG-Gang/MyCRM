<?php
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/3/17
	 * Time: 17:24
	 */
	
	namespace App\Http\Controllers\User;
	
	use Illuminate\Http\Request;
	use App\Model\User;
	use App\Model\Mt4Users;
	use App\Model\Mt4Trades;
	use App\Model\TransApplyLog;
	use App\Model\UserGroup;
	use App\Model\SystemLoginLog;
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	
	class DirectCustomerController extends Abstract_Mt4service_Controller
	{
		
		public function cust_list_browse () {
			return view ('user.customer_list.customer_list_browse')->with (['_user_info' => $this->_user]);
		}
		
		public function cust_list_chang_group_browse ()
		{
			return view ('user.customer_list.customer_list_change_group_browse')->with (['_user_info' => $this->_user]);
		}
		
		public function show_direct_cust_info ($role, $uid)
		{
			$_info = $this->_exte_get_user_info($uid);
			
			return view ('user.user_detail.show_direct_cust_info')->with (['_info' => $_info, 'role' => $role, 'permit' => $this->Role()]);
		}
		
		public function loginHistorySearch($uid)
		{
			//查出当前客户最近15日内的登录历史记录
			$data = array(
				'user_id'   => $uid,
				'startdate' => date('Y-m-d', strtotime('-4 weeks')),
				'enddate'   => date('Y-m-d'),
			);
			
			$result     = array ('rows' => '', 'total' => '');
			$_rs = $this->get_user_login_history_list ('page', $data);
			
			if (!empty($_rs)) {
				$result['rows'] = $_rs;
				$result['total'] = $this->get_user_login_history_list('count', $data);
			}
			
			return json_encode ($result);
		}
		
		public function directCustListSearch(Request $request)
		{
			
			$result     = array ('rows' => '', 'total' => '');
			$_rs = $this->get_current_agents_direct_id_list ('page', $request);
			
			if (!empty($_rs)) {
				//统计汇总当前页各种资金
				$_sumdata = $this->get_current_agents_direct_page_sumdata($_rs, $request, 'pageTotal');
				$_search_sumdata = $this->get_current_agents_direct_all_page_sumdata($request);
				
				//对查询结果和汇总结果再次重新整理数据结构
				foreach ($_rs as $key => $_info) {
					$_rs[$key]['total_comm']              = $_sumdata[$_info['user_id']]['total_comm'];
					$_rs[$key]['total_yuerj']             = $_sumdata[$_info['user_id']]['total_yuerj'];
					$_rs[$key]['total_yuecj']             = $_sumdata[$_info['user_id']]['total_yuecj'];
					$_rs[$key]['total_volume']            = $_sumdata[$_info['user_id']]['total_volume'];
					$_rs[$key]['total_swaps']             = $_sumdata[$_info['user_id']]['total_swaps'];
					$_rs[$key]['total_profit']            = $_sumdata[$_info['user_id']]['total_profit'];
					$_rs[$key]['total_noble_metal']       = $_sumdata[$_info['user_id']]['total_noble_metal'];
					$_rs[$key]['total_for_exca']          = $_sumdata[$_info['user_id']]['total_for_exca'];
					$_rs[$key]['total_crud_oil']          = $_sumdata[$_info['user_id']]['total_crud_oil'];
					$_rs[$key]['total_index']             = $_sumdata[$_info['user_id']]['total_index'];
					$_rs[$key]['total_net_worth']         = $_sumdata[$_info['user_id']]['total_net_worth'];
					$_rs[$key]['mt4MarginLevel']         = number_format($_rs[$key]['mt4MarginLevel'], '2', '.', '');

				}
				
				$result['rows'] = $_rs;
				$result['total'] = $this->get_current_agents_direct_id_list('count', $request);
				$result['footer'] = [[
					'mt4_login'         => '总计',
					'user_name'         => '',
					'mt4MarginLevel'    => '',
					'mt4_balance'       => $_search_sumdata['search_total_bal'],
					'mt4_equity'        => $_search_sumdata['search_total_eqy'],
					'total_yuerj'       => $_search_sumdata['search_total_yuerj'],
					'total_yuecj'       => $_search_sumdata['search_total_yuecj'],
					'total_net_worth'   => $_search_sumdata['search_total_net_worth'],
					'total_comm'        => $_search_sumdata['search_total_comm'],
					'total_profit'      => $_search_sumdata['search_total_profit'],
					'total_noble_metal' => $_search_sumdata['search_total_noble_metal'],
					'total_for_exca'    => $_search_sumdata['search_total_for_exca'],
					'total_crud_oil'    => $_search_sumdata['search_total_crud_oil'],
					'total_index'       => $_search_sumdata['search_total_index'],
					'total_volume'      => $_search_sumdata['search_total_volume'],
					'total_swaps'       => $_search_sumdata['search_total_swaps'],
					'mt4_regdate'       => '',
				]];
			}
			
			return json_encode ($result);
		}
		
		//客户变更列表
		public function directCustChangeListSearch(Request $request)
		{
			$result     = array ('rows' => '', 'total' => '');
			$_rs = $this->get_current_agents_direct_change_id_list ('page', $request);
			
			if (!empty($_rs)) {
				$result['rows'] = $_rs;
				$result['total'] = $this->get_current_agents_direct_change_id_list('count', $request);
			}
			
			return json_encode ($result);
		}
		
		//查出需要更改UID的组别信息
		public function changeDirectCustGroupInfo($uid)
		{
			
			$_rs = User::select(
				'user.user_id', 'user.user_name', 'user.mt4_grp',
				'user_group.user_group_id as usergrpid', 'user_group.user_group_name as usergrpname',
				'user_group.group_id as usergroupid'
			)->leftjoin('user_group', function ($leftjoin) {
				$leftjoin->on('user_group.user_group_name', ' = ', 'user.mt4_grp')->where('user_group.voided', ' = ', '1');
			})->where('user_id', $uid)->whereIn('user.voided', array ('1', '2'))->whereIn('user.user_status', array('0', '1', '2', '4'))->get()->toArray();
			
			//检查是否已经存在待更改的申请变更组别记录
			$chk_trans = TransApplyLog::where('trans_uid', $uid)->where('trans_apply_status', '0')->where('voided', '1')->first();
			if($chk_trans == null) {
				$is_trans = 'NO'; //没有申请记录
			} else {
				$is_trans = 'YES'; //有申请记录
			}
			//查找当前ID是否有持仓单
			$is_orderno = Mt4Trades::where('LOGIN', $uid)->where('CLOSE_TIME', '1970-01-01 00:00:00')->where('CONV_RATE1', '<>', 0)->whereIn('CMD', array(0, 1, 2, 3, 4, 5))->count();
			
			//得到客户UID的上级PID组别信息，用于变更
			$grp_type = UserGroup::select('user_group_id', 'user_group_name', 'user_grup_radix', 'group_id', 'voided')
				->where('user_group_name', 'like', '%' . substr($this->_user['mt4_grp'], 1))
				->where('voided', '1')
				->get()->toArray();
			
			$_rs['chk_trans'] = $chk_trans;
			$_rs['isExistsTrans'] = $is_trans;
			$_rs['isExistsOrder'] = $is_orderno;
			$_rs['grp_type'] = $grp_type;
			
			return view ('user.customer_list.customer_change_group')->with (['_rs' => $_rs]);
		}
		
		public function changeDirectCustGroupEdit(Request $request)
		{
			$userId     = $request->userId;
			$grpName    = $request->grpName;
			
			$chk_grp = UserGroup::where('user_group_name', $grpName)->where('voided', '1')->first();
			if($chk_grp == null) {
				return response()->json([
					'msg'   => 'CLASSINVALID',
				]);
			}
			
			//开始生成申请记录
			$num = TransApplyLog::create([
				'trans_uid'                 => $userId,
				'trans_type_gid'            => $chk_grp['group_id'],
				'trans_type_name'           => $grpName,
				'trans_apply_uid'           => $this->_user['user_id'],
				'trans_apply_uname'         => $this->_user['user_name'],
				'trans_apply_status'        => 0, //等待变更 0=等待变更，1=已确认变更，-1申请变更失败
				'trans_apply_reason'        => '', //变更失败原因，当申请状态trans_apply_status = -1 时，此列必须填写值
				'voided'                    => '1',
				'rec_crt_user'              => $this->_user['user_name'],
				'rec_upd_user'              => $this->_user['user_name'],
				'rec_crt_date'              => date('Y-m-d H:i:s'),
				'rec_upd_date'              => date('Y-m-d H:i:s'),
			]);
			
			if ($num) {
				return response()->json([
					'msg'   => 'SUCCESS',
				]);
			} else {
				return response()->json([
					'msg'   => 'FAIL',
				]);
			}
		}
		
		protected function get_current_agents_direct_id_list ($search, $request)
		{
			
			$userId         = $request->userId;
			$username       = $request->username;
			$userstatus     = $request->userstatus;
			$startdate      = $request->startdate;
			$enddate        = $request->enddate;
			$loginId        = $this->_user;
			$id_list        = array ();
			
			$query_sql = User::selectRaw("
				user.user_id, user.user_name, user.parent_id,
				user.trans_mode, user.mt4_code, user.user_money, user.cust_eqy, user.mt4_grp,
				user.user_status, user.voided, user.IDcard_status, user.bank_status,
				mt4_users.LOGIN as mt4_login, mt4_users.NAME as mt4_name, mt4_users.BALANCE as mt4_balance,
				mt4_users.EQUITY as mt4_equity, mt4_users.REGDATE as mt4_regdate, mt4_users.MARGIN_LEVEL as mt4MarginLevel
			")->leftjoin('mt4_users', 'mt4_users.LOGIN', ' = ', 'user.user_id')
			->whereIn('user.voided', array ('1', '2'))->whereIn('user.user_status', array('0', '1', '2', '4'))
			->where('user.parent_id', $loginId['user_id'])
			->where(function ($subWhere) use ($loginId, $userId, $username, $userstatus, $startdate, $enddate) {
				if (!empty($startdate) && !empty($enddate) && $this->_exte_is_Date ($startdate) && $this->_exte_is_Date ($enddate)) {
					$subWhere->whereBetween('mt4_users.REGDATE', [$startdate .' 00:00:00', $enddate . ' 23:59:59']);
				} else {
					if(!empty($startdate) && $this->_exte_is_Date ($startdate)) {
						$subWhere->where('mt4_users.REGDATE',  '>= ', $startdate .' 23:59:59');
					}
					if(!empty($enddate) && $this->_exte_is_Date ($enddate)) {
						$subWhere->where('mt4_users.REGDATE', '<', $enddate .' 00:00:00');
					}
				}
				
				if (!empty($userId)) {
					$subWhere->where('mt4_users.LOGIN', 'like', '%' . $userId . '%');
				}
				if (!empty($username)) {
					$subWhere->where('mt4_users.NAME', 'like', '%' . $username . '%');
				}
				if ($userstatus != '') {
					$subWhere->where('user.user_status', $userstatus);
				}
			});
			
			if ($search == 'page') {
				$id_list = $query_sql->skip($this->_offset)->take($this->_pageSize)->orderBy('user.rec_crt_date', 'desc')->get()->toArray();
			} else if ($search == 'count') {
				$id_list = $query_sql->count();
			} else if ($search == 'sum') {
				$id_list = $query_sql->get()->toArray();
			}
			
			return $id_list;
		}
		
		protected function get_current_agents_direct_change_id_list ($search, $request)
		{
			
			$userId         = $request->userId;
			$groupId        = $request->groupId;
			$startdate      = $request->startdate;
			$enddate        = $request->enddate;
			$loginId        = $this->_user;
			$id_list        = array ();
			
			$query_sql = TransApplyLog::where('trans_apply_uid', $loginId['user_id'])->where('voided', '1')
				->where(function($where) use($userId, $groupId, $startdate, $enddate) {
					if (!empty($startdate) && !empty($enddate) && $this->_exte_is_Date ($startdate) && $this->_exte_is_Date ($enddate)) {
						$where->whereBetween('rec_crt_date', [$startdate .' 00:00:00', $enddate . ' 23:59:59']);
					} else {
						if(!empty($startdate) && $this->_exte_is_Date ($startdate)) {
							$where->where('rec_crt_date',  '>= ', $startdate .' 23:59:59');
						}
						if(!empty($enddate) && $this->_exte_is_Date ($enddate)) {
							$where->where('rec_crt_date', '<', $enddate .' 00:00:00');
						}
					}
					
					if (!empty($userId)) {
						$where->where('trans_uid', 'like', '%' . $userId . '%');
					}
					if ($groupId != '') {
						$where->where('trans_type_gid', $groupId);
					}
				});
			
			if ($search == 'page') {
				$id_list = $query_sql->skip($this->_offset)->take($this->_pageSize)->orderBy('rec_crt_date', 'desc')->get()->toArray();
			} else if ($search == 'count') {
				$id_list = $query_sql->count();
			}
			
			return $id_list;
		}
		
		protected function get_user_login_history_list($totalType, $data)
		{
			$query_sql = SystemLoginLog::where('system_login_log.login_id', $data['user_id'])
						->where('system_login_log.voided', '1')
						->where(function ($subWhere) use ($data) {
							$this->_exte_set_search_condition($subWhere, $data);
						});
			
			return $this->_exte_get_query_sql_data($query_sql, $totalType, 'system_login_log.login_date');
		}
		
		protected function get_current_agents_direct_page_sumdata ($id_list, $request, $totalType)
		{
			
			$userId         = $request->userId;
			$username       = $request->username;
			$userstatus     = $request->userstatus;
			$startdate      = $request->startdate;
			$enddate        = $request->enddate;
			$loginId        = $this->_user;
			$_rs            = array ();
			
			if ($totalType == 'pageTotal') {
				foreach ($id_list as $key => $vdata) {
					$_one_sumdata[$vdata['user_id']] = Mt4Trades::selectRaw ("
						/*手续费*/
						abs( sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.commission else 0 end ) ) as total_comm,
						/*客户余额入金*/
						sum( case when mt4_trades.profit > 0 and mt4_trades.CMD in (6) and mt4_trades.COMMENT NOT LIKE '%Adj%' then mt4_trades.profit else 0 end ) as total_yuerj,
						/*客户余额出金*/
						sum( case when mt4_trades.profit < 0 and mt4_trades.CMD in (6) and mt4_trades.COMMENT NOT LIKE '%Adj%' then mt4_trades.profit else 0 end ) as total_yuecj,
						/*手数*/
						sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.volume else 0 end ) as total_volume,
						/*利息*/
						abs( sum( case when mt4_trades.swaps < 0 and mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.swaps else 0 end ) ) as total_swaps,
						/*盈亏*/
						sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.profit else 0 end ) as total_profit,
						/*贵金属*/
					     sum( case when mt4_trades.symbol in ( select sym_symbol from symbol_prices where sym_grp_id = 1 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.volume else 0 end ) as total_noble_metal,
					    /*外汇*/
					     sum( case when mt4_trades.symbol in ( select sym_symbol from symbol_prices where sym_grp_id = 2 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.volume else 0 end ) as total_for_exca,
					    /*原油*/
					     sum( case when mt4_trades.symbol in ( select sym_symbol from symbol_prices where sym_grp_id = 3 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.volume else 0 end ) as total_crud_oil,
					    /*指数*/
					     sum( case when mt4_trades.symbol in ( select sym_symbol from symbol_prices where sym_grp_id = 4 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.volume else 0 end ) as total_index
					")->where('LOGIN', $vdata['user_id'])->get()->toArray();
				}
				
				for ($i = 0; $i < count($_one_sumdata); $i ++) {
					/*手续费*/
					$_rs[$id_list[$i]['user_id']]['total_comm']                 = number_format ($_one_sumdata[$id_list[$i]['user_id']][0]['total_comm'], 2, '.', '');
					/*客户余额入金*/
					$_rs[$id_list[$i]['user_id']]['total_yuerj']                = number_format ($_one_sumdata[$id_list[$i]['user_id']][0]['total_yuerj'], 2, '.', '');
					/*客户余额出金*/
					$_rs[$id_list[$i]['user_id']]['total_yuecj']                = number_format ($_one_sumdata[$id_list[$i]['user_id']][0]['total_yuecj'], 2, '.', '');
					/*手数*/
					$_rs[$id_list[$i]['user_id']]['total_volume']               = $_one_sumdata[$id_list[$i]['user_id']][0]['total_volume'] / 100;
					/*利息*/
					$_rs[$id_list[$i]['user_id']]['total_swaps']                = number_format ($_one_sumdata[$id_list[$i]['user_id']][0]['total_swaps'], 2, '.', '');
					/*盈亏*/
					$_rs[$id_list[$i]['user_id']]['total_profit']               = number_format ($_one_sumdata[$id_list[$i]['user_id']][0]['total_profit'], 2, '.', '');
					/*贵金属*/
					$_rs[$id_list[$i]['user_id']]['total_noble_metal']          = $_one_sumdata[$id_list[$i]['user_id']][0]['total_noble_metal'] / 100;
					/*外汇*/
					$_rs[$id_list[$i]['user_id']]['total_for_exca']             = $_one_sumdata[$id_list[$i]['user_id']][0]['total_for_exca'] / 100;
					/*原油*/
					$_rs[$id_list[$i]['user_id']]['total_crud_oil']             = $_one_sumdata[$id_list[$i]['user_id']][0]['total_crud_oil'] / 100;
					/*指数*/
					$_rs[$id_list[$i]['user_id']]['total_index']                = $_one_sumdata[$id_list[$i]['user_id']][0]['total_index'] / 100;
					/*净入金 = 入金 - 出金*/
					$_rs[$id_list[$i]['user_id']]['total_net_worth']            = number_format(($_one_sumdata[$id_list[$i]['user_id']][0]['total_yuerj']- abs($_one_sumdata[$id_list[$i]['user_id']][0]['total_yuecj'])), 2, '.', '');
				}
			}
			
			return $_rs;
		}
		
		protected function get_current_agents_direct_all_page_sumdata ($request)
		{
			
			$userId         = $request->userId;
			$username       = $request->username;
			$userstatus     = $request->userstatus;
			$startdate      = $request->startdate;
			$enddate        = $request->enddate;
			$loginId        = $this->_user;
			$_all_rs        = array ();
			
			$_allsumdata[$loginId['user_id']]['search_total'] = Mt4Trades::selectRaw ("
					/*总手续费*/
					abs( sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.commission else 0 end ) ) as total_comm,
					/*客户总余额入金*/
					sum( case when mt4_trades.profit > 0 and mt4_trades.CMD in (6) and mt4_trades.COMMENT NOT LIKE '%Adj%' then mt4_trades.profit else 0 end ) as total_yuerj,
					/*客户总余额出金*/
					sum( case when mt4_trades.profit < 0 and mt4_trades.CMD in (6) and mt4_trades.COMMENT NOT LIKE '%Adj%' then mt4_trades.profit else 0 end ) as total_yuecj,
					/*总手数 == 总交易量*/
					sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.volume else 0 end ) as total_volume,
					/*总利息*/
					abs( sum( case when mt4_trades.swaps < 0 and mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.swaps else 0 end ) ) as total_swaps,
					/*总盈亏*/
					sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.profit else 0 end ) as total_profit,
					/*总贵金属*/
					 sum( case when mt4_trades.symbol in ( select sym_symbol from symbol_prices where sym_grp_id = 1 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.volume else 0 end ) as total_noble_metal,
					/*总外汇*/
					 sum( case when mt4_trades.symbol in ( select sym_symbol from symbol_prices where sym_grp_id = 2 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.volume else 0 end ) as total_for_exca,
					/*总原油*/
					 sum( case when mt4_trades.symbol in ( select sym_symbol from symbol_prices where sym_grp_id = 3 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.volume else 0 end ) as total_crud_oil,
					/*总指数*/
					 sum( case when mt4_trades.symbol in ( select sym_symbol from symbol_prices where sym_grp_id = 4 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.volume else 0 end ) as total_index
				")->whereIn('mt4_trades.LOGIN', function ($whereIn) use ($loginId, $userId, $username, $userstatus, $startdate, $enddate) {
					$whereIn->select('user.user_id')->from('user')
						->join('mt4_users', 'mt4_users.LOGIN', ' = ', 'user.user_id')
						->whereIn('user.voided', array ('1', '2'))->whereIn('user.user_status', array('0', '1', '2', '4'))
						->where('user.parent_id', $loginId['user_id'])
						->where(function ($subWhere) use ($loginId, $userId, $username, $userstatus, $startdate, $enddate) {
							if (!empty($startdate) && !empty($enddate) && $this->_exte_is_Date ($startdate) && $this->_exte_is_Date ($enddate)) {
								$subWhere->whereBetween('mt4_users.REGDATE', [$startdate .' 00:00:00', $enddate . ' 23:59:59']);
							} else {
								if(!empty($startdate) && $this->_exte_is_Date ($startdate)) {
									$subWhere->where('mt4_users.REGDATE',  '>= ', $startdate .' 23:59:59');
								}
								if(!empty($enddate) && $this->_exte_is_Date ($enddate)) {
									$subWhere->where('mt4_users.REGDATE', '<', $enddate .' 00:00:00');
								}
							}
							
							if (!empty($userId)) {
								$subWhere->where('mt4_users.LOGIN', 'like', '%' . $userId . '%');
							}
							if (!empty($username)) {
								$subWhere->where('mt4_users.NAME', 'like', '%' . $username . '%');
							}
							if ($userstatus != '') {
								$subWhere->where('user.user_status', $userstatus);
							}
						});
				})->get()->toArray();
			
			//总余额，净值
			$_allsumdata[$loginId['user_id']]['search_bal_eqy'] = Mt4Users::selectRaw('
				/*余额*/
				sum(mt4_users.BALANCE) as all_total_bal,
				/*净值*/
				sum(mt4_users.EQUITY) as all_total_eqy
			')->whereIn('mt4_users.LOGIN', function ($whereIn) use ($loginId, $userId, $username, $userstatus, $startdate, $enddate) {
				$whereIn->select('user.user_id')->from('user')
					->join('mt4_users', 'mt4_users.LOGIN', ' = ', 'user.user_id')
					->whereIn('user.voided', array ('1', '2'))->whereIn('user.user_status', array('0', '1', '2', '4'))
					->where('user.parent_id', $loginId['user_id'])
					->where(function ($subWhere) use ($loginId, $userId, $username, $userstatus, $startdate, $enddate) {
						if (!empty($startdate) && !empty($enddate) && $this->_exte_is_Date ($startdate) && $this->_exte_is_Date ($enddate)) {
							$subWhere->whereBetween('mt4_users.REGDATE', [$startdate .' 00:00:00', $enddate . ' 23:59:59']);
						} else {
							if(!empty($startdate) && $this->_exte_is_Date ($startdate)) {
								$subWhere->where('mt4_users.REGDATE',  '>= ', $startdate .' 23:59:59');
							}
							if(!empty($enddate) && $this->_exte_is_Date ($enddate)) {
								$subWhere->where('mt4_users.REGDATE', '<', $enddate .' 00:00:00');
							}
						}
						
						if (!empty($userId)) {
							$subWhere->where('mt4_users.LOGIN', 'like', '%' . $userId . '%');
						}
						if (!empty($username)) {
							$subWhere->where('mt4_users.NAME', 'like', '%' . $username . '%');
						}
						if ($userstatus != '') {
							$subWhere->where('user.user_status', $userstatus);
						}
					});
			})->get()->toArray();
			
			foreach ($_allsumdata as $vdata) {
				/*手续费*/
				$_all_rs['search_total_comm']               = number_format($vdata['search_total'][0]['total_comm'], 2, '.', '');
				/*客户余额入金*/
				$_all_rs['search_total_yuerj']              = number_format($vdata['search_total'][0]['total_yuerj'], 2, '.', '');
				/*客户余额出金*/
				$_all_rs['search_total_yuecj']              = number_format($vdata['search_total'][0]['total_yuecj'], 2, '.', '');
				/*手数*/
				$_all_rs['search_total_volume']             = $vdata['search_total'][0]['total_volume'] / 100;
				/*利息*/
				$_all_rs['search_total_swaps']              = number_format($vdata['search_total'][0]['total_swaps'], 2, '.', '');
				/*盈亏*/
				$_all_rs['search_total_profit']             = number_format($vdata['search_total'][0]['total_profit'], 2, '.', '');
				/*贵金属*/
				$_all_rs['search_total_noble_metal']        = $vdata['search_total'][0]['total_noble_metal'] / 100;
				/*外汇*/
				$_all_rs['search_total_for_exca']           = $vdata['search_total'][0]['total_for_exca'] / 100;
				/*原油*/
				$_all_rs['search_total_crud_oil']           = $vdata['search_total'][0]['total_crud_oil'] / 100;
				/*指数*/
				$_all_rs['search_total_index']              = $vdata['search_total'][0]['total_index'] / 100;
				/*净入金 = 入金 - 出金*/
				$_all_rs['search_total_net_worth']          = number_format(($vdata['search_total'][0]['total_yuerj'] - abs($vdata['search_total'][0]['total_yuecj'])), 2, '.', '');
				/*总余额*/
				$_all_rs['search_total_bal']                = number_format($vdata['search_bal_eqy'][0]['all_total_bal'], 2, '.', '');
				/*总净值*/
				$_all_rs['search_total_eqy']                = number_format($vdata['search_bal_eqy'][0]['all_total_bal'], 2, '.', '');
			}
			
			return $_all_rs;
		}
		
		protected function _exte_set_search_condition($subWhere, $data)
		{
			return $subWhere->whereBetween('system_login_log.login_date', [$data['startdate'] .' 00:00:00', $data['enddate'] . ' 23:59:59']);
		}
	}