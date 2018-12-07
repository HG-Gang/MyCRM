<?php
/**
 * Created by PhpStorm.
 * User: I5
 * Date: 2018/11/8
 * Time: 18:11
 */

namespace App\Http\Controllers\admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\Mt4Trades;
use App\Model\User;
use App\Model\SystemLoginLog;

use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;

class FengXianManageController extends Abstract_Mt4service_Controller
{
	public function fengXian_profit_browse()
	{
		return view('admin.fengXian.fengXian_profit_browse')->with(['_role' =>$this->Role()]);
	}
	
	public function fengXian_position_browse()
	{
		return view('admin.fengXian.fengXian_position_browse');
	}
	
	public function fengXian_Ipaddress_browse()
	{
		return view('admin.fengXian.fengXian_Ipaddress_browse');
	}
	
	public function fengXian_profit_list(Request $request)
	{
		$data = array(
			'userId'            => $request->userId,
			'username'          => $request->username,
		//	'fengxian'          => $request->fengxian,
			'startdate'         => $request->startdate,
			'enddate'           => $request->enddate,
		);

		$result = array('rows' => '', 'total' => '');
		$_rs = $this->get_all_list ('page', $data);

		if (!empty($_rs)) {
			//统计汇总当前页各种资金且盈亏都是整数的用户信息
			$_sumdata = $this->get_current_page_sumdata($_rs, $data, 'pageTotal');

			//对查询结果和汇总结果再次重新整理数据结构
			foreach ($_rs as $key => $_info) {
				if(($s[$key] =count($_sumdata[$key])) > 0 && $_info['parent_id'] != $this->_agentsIdIndex) {
					$_rs_final[$key]['user_id']                 = $_info['user_id'];
					$_rs_final[$key]['user_name']               = $_info['user_name'];
					$_rs_final[$key]['parent_id']               = $_info['parent_id'];
					$_rs_final[$key]['trans_mode']              = $_info['trans_mode'];
					$_rs_final[$key]['mt4_code']                = $_info['mt4_code'];
					$_rs_final[$key]['cust_eqy']                = number_format($_info['cust_eqy'], 2, '.', '');
					$_rs_final[$key]['mt4_grp']                 = $_info['mt4_grp'];
					$_rs_final[$key]['user_status']             = $_info['user_status'];
					$_rs_final[$key]['voided']                  = $_info['voided'];
					$_rs_final[$key]['IDcard_status']           = $_info['IDcard_status'];
					$_rs_final[$key]['bank_status']             = $_info['bank_status'];
					$_rs_final[$key]['mt4_login']               = $_info['mt4_login'];
					$_rs_final[$key]['mt4_name']                = $_info['mt4_name'];
					$_rs_final[$key]['mt4_balance']             = number_format($_info['mt4_balance'], 2, '.', '');
					$_rs_final[$key]['mt4_equity']              = number_format($_info['mt4_equity'], 2, '.', '');
					$_rs_final[$key]['mt4_regdate']             = $_info['mt4_regdate'];
					$_rs_final[$key]['total_comm']              = number_format($_sumdata[$key][0]->total_comm, 2, '.', '');
					$_rs_final[$key]['total_yuerj']             = number_format($_sumdata[$key][0]->total_yuerj, 2, '.', '');
					$_rs_final[$key]['total_yuecj']             = number_format($_sumdata[$key][0]->total_yuecj, 2, '.', '');
					$_rs_final[$key]['total_volume']            = number_format(($_sumdata[$key][0]->total_volume / 100), 2, '.', '');
					$_rs_final[$key]['total_swaps']             = number_format($_sumdata[$key][0]->total_swaps, 2, '.', '');
					$_rs_final[$key]['total_profit']            = number_format($_sumdata[$key][0]->total_profit, 2, '.', '');
					$_rs_final[$key]['total_net_worth']         = number_format(($_sumdata[$key][0]->total_yuerj- abs($_sumdata[$key][0]->total_yuecj)), 2, '.', '');
					$_rs_final[$key]['feng_xian_val']           = ($_sumdata[$key][0]->total_yuerj) ? number_format((($_sumdata[$key][0]->total_profit - $_sumdata[$key][0]->total_comm) / $_sumdata[$key][0]->total_yuerj) * 100, '2', '.', '') : '0.00';
				}
			}
			
			$result['rows'] = array_values($_rs_final);
		}
		
		return json_encode($result);
	}
	
	public function fengXian_position_list(Request $request)
	{
		$data = array(
				'userId'            => $request->userId,
				'orderId'           => $request->orderId,
				'orderType'         => $request->orderType,
				'startdate'         => $request->startdate,
				'enddate'           => $request->enddate,
		);
		
		$result = array('rows' => '', 'total' => '');
		$_rs = $this->get_open_list('page', $data);
		
		if (!empty($_rs)) {
			foreach ($_rs as $key => $_info) {
				if(($_info['abs_comm'] == 0)) {
					$_rs[$key]['feng_xian_positionval'] = number_format(($_info['profit'] / 1), '2', '.', '');
				} else {
					$_rs[$key]['feng_xian_positionval'] = number_format((($_info['profit'] - $_info['abs_comm']) / $_info['abs_comm']) * 100, '2', '.', '');
				}
			}
			
			$result['rows'] = $_rs;
			$result['total'] = $this->get_open_list('count', $data);
		}
		
		return json_encode($result);
	}
	
	public function fengXian_Ipaddress_list(Request $request)
	{
		$data = array(
			'userId'            => $request->userId,
			'startdate'         => $request->startdate,
			'enddate'           => $request->enddate,
		);
		
		$result = array('rows' => '', 'total' => '');
		
		$_rs = $this->get_ipaddress_list('page', $data);
		
		if (!empty($_rs)) {
			$result['rows']     = $_rs;
			$result['total']    = count($_rs);
		}
		
		return $result;//json_encode($result);
	}
	
	public function fengXian_Ipaddress_detail ($idaddr)
	{
		/*$data = array(
				'startdate'         => $request->startdate,
				'enddate'           => $request->enddate,
				'idaddr'            => $idaddr,
		);*/
		//dd($data);exit();
		$result = array('rows' => '', 'total' => '');
		ob_clean();
		$_rs = $this->fengXian_Ipaddress_detail_list('page', $idaddr);
		
		if (!empty($_rs)) {
			$result['rows']     = $_rs;
		}
		
		return json_encode($result);
	}
	
	protected function get_all_list($totalType, $data)
	{
		$query_sql = User::selectRaw("
				user.user_id, user.user_name, user.parent_id,
				user.trans_mode, user.mt4_code, user.user_money, user.cust_eqy, user.mt4_grp,
				user.user_status, user.voided, user.IDcard_status, user.bank_status,
				mt4_users.LOGIN as mt4_login, mt4_users.NAME as mt4_name, mt4_users.BALANCE as mt4_balance,
				mt4_users.EQUITY as mt4_equity, mt4_users.REGDATE as mt4_regdate
			")->join('mt4_users', 'mt4_users.LOGIN', ' = ', 'user.user_id')
				->whereIn('user.voided', array ('1', '2'))->whereIn('user.user_status', array('0', '1', '2', '4'))
				->where(function ($subWhere) use ($data) {
					$this->_exte_set_search_condition($subWhere, $data);
				});
		
		return $this->_exte_get_query_sql_data($query_sql, $totalType, 'user.rec_crt_date');
	}
	
	protected function get_open_list($totalType, $data)
	{
		$id_list        = array ();
		
		$query_sql = Mt4Trades::selectRaw("
				MT4_TRADES.TICKET as ticket, MT4_TRADES.LOGIN as login, MT4_TRADES.SYMBOL as symbol,
				MT4_TRADES.CMD as cmd, MT4_TRADES.VOLUME as volume, MT4_TRADES.SL as sl, MT4_TRADES.TP as tp,
				MT4_TRADES.COMMISSION as commission, MT4_TRADES.PROFIT as profit, MT4_TRADES.SWAPS as swaps,
				MT4_TRADES.OPEN_PRICE as open_price, MT4_TRADES.OPEN_TIME as open_time, abs(mt4_trades.COMMISSION) as abs_comm
			")->whereIn('mt4_trades.CMD', array(0, 1, 2, 3, 4, 5))
				->where('mt4_trades.CLOSE_TIME', '1970-01-01 00:00:00')
				->where('mt4_trades.CONV_RATE1', '<>', 0)
				->whereRaw("(mt4_trades.profit - abs(mt4_trades.COMMISSION)) > 0")
				->where(function ($subWhere) use ($data) {
					$this->_open_list_set_search_condition($subWhere, $data);
				});
		
		if ($totalType == 'page') {
			$id_list = $query_sql->skip($this->_offset)->take($this->_pageSize)->orderBy('mt4_trades.OPEN_TIME', 'desc')->get()->toArray();
		} else if ($totalType == 'count') {
			$id_list = $query_sql->count();
		} else if ($totalType == 'sum') {
			$id_list = $query_sql->get()->toArray();
		}
		
		return $id_list;
		
		return $this->_exte_get_query_sql_data($query_sql, $totalType, 'mt4_trades.OPEN_TIME');
	}
	
	protected function get_ipaddress_list($totalType, $data)
	{
		$newrs = array();
		
		//得到每一个不同IP的登录次数大于1次的所有收据
		$rs = SystemLoginLog::selectRaw("
			login_ip, login_id, count(*) as 'count'
		")->where('system_login_log.login_id', '<>', $this->_agentsIdIndex)
				->where('system_login_log.voided', '1')
				->whereNotIn('system_login_log.login_id', function($whereNotIn) {
					$whereNotIn->selectRaw("data_list.user_id from data_list where data_list.parent_id = " . intval($this->_agentsIdIndex));
				})
				/*->where(function ($subWhere) use ($data) {
					$this->_fengXian_list_set_search_condition($subWhere, $data);
				})*/
				->groupBy('system_login_log.login_ip')->havingRaw("count(system_login_log.login_ip) > 1")
				->orderBy('system_login_log.login_id', 'asc')
				->get()->toArray();
		
		//通过分组得出每一个IP所有不相同的登录账户数据
		foreach($rs as $key => $val) {
			$count[$key] = SystemLoginLog::selectRaw("sys_id, login_id, login_ip, login_id_desc")
					->where('system_login_log.login_id', '<>', $this->_agentsIdIndex)
					->where('system_login_log.voided', '1')
					->whereNotIn('system_login_log.login_id', function($whereNotIn) {
						$whereNotIn->selectRaw("data_list.user_id from data_list where data_list.parent_id = " . intval($this->_agentsIdIndex));
					})
					/*->where(function ($subWhere) use ($data) {
						$this->_fengXian_list_set_search_condition($subWhere, $data);
					})*/
					->where('system_login_log.login_ip', $val['login_ip'])
					->groupBy('system_login_log.login_id')->get()->toArray();
			
			//userId基本信息， 账户资金，账户状态，账户类别
			$_table = $this->_exte_get_table_obj ($val['login_id']);
			$info[$key] = $_table::select('user_id', 'user_name')->where('user_id', $val['login_id'])->get()->toArray();
			
			//如果 同一个IP分组后得到的数据大于1次的显示出来
			if(count($count[$key]) > 1) {
				$newrs[$key]['sys_id']              = $count[$key][0]['sys_id'];
				$newrs[$key]['login_id']            = $count[$key][0]['login_id'];
				$newrs[$key]['login_name']          = $info[$key][0]['user_name'];
				$newrs[$key]['login_ip']            = $count[$key][0]['login_ip'];
				$newrs[$key]['login_id_desc']       = $count[$key][0]['login_id_desc'];
				$newrs[$key]['login_count']         = $rs[$key]['count'];
			}
		}
		
		return array_values($newrs);
	}
	
	protected function fengXian_Ipaddress_detail_list($totalType, $idaddr)
	{
		$data = array(
				'startdate' => date('Y-m-d', strtotime('-4 weeks')),
				'enddate'   => date('Y-m-d'),
		);
		
		$_rs = SystemLoginLog::selectRaw("sys_id, login_id, login_ip, login_id_desc, login_date")
				->where('system_login_log.login_id', '<>', $this->_agentsIdIndex)
				->where('system_login_log.voided', '1')
				->where('system_login_log.login_ip', $idaddr)
				->whereNotIn('system_login_log.login_id', function($whereNotIn) {
					$whereNotIn->selectRaw("data_list.user_id from data_list where data_list.parent_id = " . intval($this->_agentsIdIndex));
				})
				/*->where(function ($subWhere) use ($data) {
					$subWhere->whereBetween('system_login_log.login_date', [$data['startdate'] .' 00:00:00', $data['enddate'] . ' 23:59:59']);
				})*/
				->get()->toArray();
		
		foreach ($_rs as $key => $val) {
			$_id_list[] = $val['login_id'];
		}
		
		$_new_id_list = array_values(array_unique($_id_list));
		for($i = 0; $i < count($_new_id_list); $i++) {
			if (env('SYNCMT4_UPDATEINFO')) {
				$_upd = $this->_exte_mt4_update_local_user_info ($_new_id_list[$i]);
			}
			
			//userId基本信息， 账户资金，账户状态，账户类别
			$_table = $this->_exte_get_table_obj ($_new_id_list[$i]);
			$_user_info[$_new_id_list[$i]]['info'] = $_table::select('user_id', 'user_name', 'parent_id', 'rec_crt_date')->find($_new_id_list[$i]);
			
			//持仓单情况 已平、未平仓单
			$_user_info[$_new_id_list[$i]]['close'] = Mt4Trades::where('LOGIN', $_new_id_list[$i])->where('CLOSE_TIME', '>', '1970-01-01 00:00:00')->whereIn('CMD', array(0, 1, 2, 3, 4, 5))->where('CONV_RATE1', '<>', 0)->count();
			$_user_info[$_new_id_list[$i]]['open'] = Mt4Trades::where('LOGIN', $_new_id_list[$i])->where('CLOSE_TIME', '1970-01-01 00:00:00')->whereIn('CMD', array(0, 1, 2, 3, 4, 5))->where('CONV_RATE1', '<>', 0)->count();
			
			$_user_info[$_new_id_list[$i]]['amount'] = Mt4Trades::selectRaw ("
						/*客户余额入金*/
						sum( case when mt4_trades.profit > 0 and mt4_trades.CMD in (6) and mt4_trades.COMMENT NOT LIKE '%Adj%' then mt4_trades.profit else 0 end ) as total_yuerj,
						/*客户余额出金*/
						sum( case when mt4_trades.profit < 0 and mt4_trades.CMD in (6) and mt4_trades.COMMENT NOT LIKE '%Adj%' then mt4_trades.profit else 0 end ) as total_yuecj
						")->where('LOGIN', $_new_id_list[$i])->get()->toArray();
		}
		
		foreach($_rs as $k => $v) {
			$_rs[$k]['login_name']      = $_user_info[$v['login_id']]['info']['user_name'];
			$_rs[$k]['parent_id']       = $_user_info[$v['login_id']]['info']['parent_id'];
			$_rs[$k]['rec_crt_date']    = $_user_info[$v['login_id']]['info']['rec_crt_date'];
			$_rs[$k]['close']           = $_user_info[$v['login_id']]['close'];
			$_rs[$k]['open']            = $_user_info[$v['login_id']]['open'];
			$_rs[$k]['amount_rj']       = number_format($_user_info[$v['login_id']]['amount'][0]['total_yuerj'], '2', '.', '');
			$_rs[$k]['amount_cj']       = number_format($_user_info[$v['login_id']]['amount'][0]['total_yuecj'], '2', '.', '');
		}
		
		return $_rs;
	}
	
	protected function get_current_page_sumdata($id_list, $data, $totalType)
	{
		$_rs            = array ();
		$_porfit_int    = array ();
		
		if ($totalType == 'pageTotal') {
			foreach ($id_list as $key => $vdata) {
				$subQuery = DB::table('mt4_trades')->selectRaw("
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
						sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.profit else 0 end ) as total_profit
				")->whereRaw("mt4_trades.LOGIN =" . $vdata['user_id']);
				
				$query = DB::table(DB::raw("({$subQuery->toSql()}) as sub"))
						->selectRaw("*")
						->whereRaw("(total_profit - total_comm) > 0");
						/*->where(function($subWhere) use ($vdata) {
					$subWhere->whereRaw("(sub.total_profit / sub.total_yuerj) * 100 <= " . 80);
				})->get()->toArray();*/
				
				$_one_sumdata[] = $query->mergeBindings($subQuery)->get();
			}
		}
		
		return $_one_sumdata;
	}
	
	protected function _exte_set_search_condition($subWhere, $data)
	{
		if (!empty($data['startdate']) && !empty($data['enddate']) && $this->_exte_is_Date ($data['startdate']) && $this->_exte_is_Date ($data['enddate'])) {
			$subWhere->whereBetween('mt4_users.REGDATE', [$data['startdate'] .' 00:00:00', $data['enddate'] . ' 23:59:59']);
		} else {
			if(!empty($data['startdate']) && $this->_exte_is_Date ($data['startdate'])) {
				$subWhere->where('mt4_users.REGDATE',  '>= ', $data['startdate'] .' 23:59:59');
			}
			if(!empty($data['enddate']) && $this->_exte_is_Date ($data['enddate'])) {
				$subWhere->where('mt4_users.REGDATE', '<', $data['enddate'] .' 00:00:00');
			}
		}
		
		if (!empty($data['userId'])) {
			$subWhere->where(function ($subOrWhere) use ($data) {
				$subOrWhere->where('mt4_users.LOGIN', 'like', '%' . $data['userId'] . '%')->orWhere('mt4_users.ID', 'like', '%' . $data['userId'] . '%');
			});
		}
		if (!empty($data['username'])) {
			$subWhere->where('mt4_users.NAME', 'like', '%' . $data['username'] . '%');
		}
		
		return $subWhere;
	}
	
	protected function _exte_get_query_sql_data($sql, $totalType, $col, $orderBy = 'desc')
	{
		$id_list        = array ();
		
		if ($totalType == 'page') {
			$id_list = $sql->orderBy($col, $orderBy)->get()->toArray();
		} else if ($totalType == 'count') {
			$id_list = $sql->count();
		} else if ($totalType == 'sum') {
			$id_list = $sql->get()->toArray();
		}
		
		return $id_list;
	}
	
	protected function _open_list_set_search_condition($subWhere, $data)
	{
		if (!empty($data['startdate']) && !empty($data['enddate']) && $this->_exte_is_Date($data['startdate']) && $this->_exte_is_Date($data['enddate'])) {
			$subWhere->whereBetween('mt4_trades.OPEN_TIME', [$data['startdate'] . ' 00:00:00', $data['enddate'] . ' 23:59:59']);
		} else {
			if (!empty($data['startdate']) && $this->_exte_is_Date($data['startdate'])) {
				$subWhere->where('mt4_trades.OPEN_TIME', '>= ', $data['startdate'] . ' 23:59:59');
			}
			if (!empty($data['enddate']) && $this->_exte_is_Date($data['enddate'])) {
				$subWhere->where('mt4_trades.OPEN_TIME', '<', $data['enddate'] . ' 00:00:00');
			}
		}
		
		if (!empty($data['userId'])) {
			$subWhere->where('mt4_trades.LOGIN', 'like', '%' . $data['userId'] . '%');
		}
		if (!empty($data['orderId'])) {
			$subWhere->where('mt4_trades.TICKET', 'like', '%' . $data['orderId'] . '%');
		}
		
		if ($data['orderType'] != '' && $data['orderType'] == 'real_disk') {
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
		} else if ($data['orderType'] != '' && $data['orderType'] == 'test_disk') {
			$subWhere->whereIn('mt4_trades.LOGIN',function ($subWhere2) {
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
		}
		
		return $subWhere;
	}
	
	protected function _fengXian_list_set_search_condition($subWhere, $data)
	{
		if (!empty($data['startdate']) && !empty($data['enddate']) && $this->_exte_is_Date($data['startdate']) && $this->_exte_is_Date($data['enddate'])) {
			$subWhere->whereBetween('system_login_log.login_date', [$data['startdate'] . ' 00:00:00', $data['enddate'] . ' 23:59:59']);
		} else {
			if (!empty($data['startdate']) && $this->_exte_is_Date($data['startdate'])) {
				$subWhere->where('system_login_log.login_date', '>= ', $data['startdate'] . ' 23:59:59');
			}
			if (!empty($data['enddate']) && $this->_exte_is_Date($data['enddate'])) {
				$subWhere->where('system_login_log.login_date', '<', $data['enddate'] . ' 00:00:00');
			}
		}
		
		if (!empty($data['userId'])) {
			$subWhere->where('system_login_log.login_id', $data['userId']);
		}
		
		return $subWhere;
	}
}