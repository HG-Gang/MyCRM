<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018-05-16
	 * Time: 下午 4:31
	 */
	
	namespace App\Http\Controllers\Admin;
	
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	use App\Model\Mt4Users;
	use App\Model\WhsExpZero;
	use App\Model\SystemConfig;
	use App\Model\Mt4Trades;
	use Illuminate\Http\Request;
	
	class AdminWhsExpZeroController extends Abstract_Mt4service_Controller
	{
		public function whs_exp_zero_list ()
		{
			return view('admin.whs_exp_zero_list.whs_exp_zero_list_browse');
		}
		
		public function whs_cust_list_browse ()
		{
			return view('admin.whs_exp_zero_list.whs_cust_list_browse');
		}
		
		public function oneKeyZero(Request $request)
		{
			$userId         = $request->userId;
			$userName       = $request->userName;
			$balance        = $request->balance;
			$crdt           = $request->crdt;
			
			if ($crdt > abs ($balance)) {
				$dep_bal = $this->_exte_mt4_deposit_amount($userId, abs($balance),  $userId . self::BCQL);
			} else if ($crdt < abs ($balance)) {
				$dep_bal = $this->_exte_mt4_deposit_amount($userId, (abs($balance) - $crdt) , $userId . self::BCQL);
			}
			
			if ($dep_bal['ret'] == '0') {
				$rs = $this->_exte_whs_exp_zero_success_response($userId);
				//记录爆仓清零的人
				$_rs = WhsExpZero::where('whs_exp_zero.wez_userid', $userId)
						->where('whs_exp_zero.wez_status', '1')
						->where('whs_exp_zero.voided', '1')
						->update([
							'wez_status'            => 2,
							'rec_upd_user'          => $this->_auser['username'],
							'rec_upd_date'          => date ('Y-m-d H:i:s'),
						]);
				
				if(count($_rs) > 0) {
					return response()->json([
							'msg'       => 'SUC',
							'err'       => 'noerr',
							'col'       => 'enable',
					]);
				} else {
					return response()->json([
							'msg'       => 'FAIL',
							'err'       => 'crtfail',
							'col'       => 'nocol',
					]);
				}
			} else {
				return response()->json([
						'msg'       => 'FAIL',
						'err'       => 'zerofail', //mt4清零失败
						'col'       => 'nocol',
				]);
			}
		}
		
		public function oneKeySearch()
		{
			$_rs = Mt4Users::select('mt4_users.LOGIN as userId', 'mt4_users.NAME as userName', 'mt4_users.BALANCE as userBalance', 'mt4_users.CREDIT as userCredit')
					->leftjoin('user', function($leftjoin) {
						$leftjoin->on('mt4_users.LOGIN', '=', 'user.user_id');
					})->where('user.voided', '1')->where('user.group_id', '5')->where('mt4_users.BALANCE', '<', 0)->get()->toArray();
			
			$no = 0;
			if (!empty($_rs)) {
				for ($j = 0; $j < count($_rs); $j ++) {
					$_rs[$j]['vol'] = Mt4Trades::where('LOGIN', $_rs[$j]['userId'])->where('CLOSE_TIME', '1970-01-01 00:00:00')
							->whereIn('CMD', array(0, 1, 2, 3, 4, 5))
							->where('CONV_RATE1', '<>', 0)->count();
					
					$_exits[$j] = WhsExpZero::where('whs_exp_zero.wez_userid', $_rs[$j]['userId'])->where('wez_status', 1)->where('voided', '1')->where('wez_idmd5', strtoupper(md5($_rs[$j]['userId'])))->count();
					
					if($_rs[$j]['vol'] == 0 && $_exits[$j] == 0) {
						//记录爆仓清零的人
						$num[$j] = WhsExpZero::create([
								'wez_userid'            => $_rs[$j]['userId'],
								'wez_username'          => $_rs[$j]['userName'],
								'wez_userbal'           => $_rs[$j]['userBalance'],
								'wez_usercrt'           => $_rs[$j]['userCredit'],
								'wez_idmd5'             => strtoupper(md5($_rs[$j]['userId'])),
								'wez_status'            => 1,
								'voided'                => '1',
								'rec_crt_user'          => env('COMPANY_CODE'),
								'rec_upd_user'          => env('COMPANY_CODE'),
								'rec_crt_date'          => date ('Y-m-d H:i:s'),
								'rec_upd_date'          => date ('Y-m-d H:i:s'),
						]);
						$no ++;
					}
				}
				
				if($no > 0) {
					return response()->json([
							'msg'       => 'SUC',
							'err'       => 'noerr',
							'col'       => $no,
					]);
				} else {
					return response()->json([
							'msg'       => 'FAIL',
							'err'       => 'zerofail',
							'col'       => $no,
					]);
				}
			}
		}
		
		public function whsExpZeroListSearch (Request $request)
		{
			$data = array(
				'wez_userid'         => $request->wez_userid,
				'wez_username'       => $request->wez_username,
				'wez_status'        => $request->wez_status,
			//	'rec_crt_date_start' => $request->startdate,
			//	'rec_crt_date_end'   => $request->enddate,
			);
			
			$result = array ('rows' => '', 'total' => '');
			
			$_rs = $this->get_whs_exp_zero_id_list ('page', $data);
			
			if (!empty($_rs)) {
				$result['rows'] = $_rs;
				//$result['total'] = $this->get_whs_exp_zero_id_list ('count', $data);
			}
			
			return json_encode ($result);
		}
		
		//一键爆仓清理
		public function trades_whs_exp_zero()
		{
			
			$no = 0;
			$chk = SystemConfig::select('trades_whs_exp_zero')->first();
			if ($chk['trades_whs_exp_zero'] == '0') {
				$rs = Mt4Users::select('mt4_users.LOGIN', 'mt4_users.NAME', 'mt4_users.BALANCE', 'mt4_users.CREDIT')
					->where('LOGIN', '>=', 637001)->where('BALANCE', '<', 0)->limit(50)->get()->toArray();
				
				if (is_array ($rs) && !empty($rs)) {
					//有数据，开始计算
					$upd = SystemConfig::where('sys_id', 1)->update(['trades_whs_exp_zero' => '1']);
					for ($j = 0; $j < count($rs); $j ++) {
						$whs_exp_zero[$j]['vol'] = Mt4Trades::where('LOGIN', $rs[$j]['LOGIN'])->where('CLOSE_TIME', '1970-01-01 00:00:00')
							->whereIn('CMD', array(0, 1, 2, 3, 4, 5))
							->where('CONV_RATE1', '<>', 0)->count();
						
						if ($whs_exp_zero[$j]['vol'] == 0 && $rs[$j]['BALANCE'] < 0) {
							$_cmt = $rs[$j]['LOGIN'] . self::BCQL;
							//符合爆仓清零的ID
							if ($rs[$j]['CREDIT'] > abs ($rs[$j]['BALANCE'])) {
								//$dep_crt = $this->MT4_creditInOrOut_syncRequest($rs[$j]['LOGIN'], ($rs[$j]['CREDIT'] - abs ($rs[$j]['BALANCE'])), 'credit-out', $_cmt, ''); //出信用，信用抵扣金额
								$dep_bal = $this->_exte_mt4_deposit_amount($rs[$j]['LOGIN'], abs($rs[$j]['BALANCE']), $_cmt);
							} else if ($rs[$j]['CREDIT'] < abs ($rs[$j]['BALANCE'])) {
								//$dep_crt = $this->MT4_creditInOrOut_syncRequest($rs[$j]['LOGIN'], $rs[$j]['CREDIT'], 'credit-out', $_cmt, ''); //出信用，信用抵扣金额
								$dep_bal = $this->_exte_mt4_deposit_amount($rs[$j]['LOGIN'], (abs($rs[$j]['BALANCE']) - $rs[$j]['CREDIT']) , $_cmt);
							}
							
							if ($dep_bal['ret'] == '0') {
								$rs[$j] = $this->_exte_whs_exp_zero_success_response($rs[$j]['LOGIN']);
								//记录爆仓清零的人
								$num[$j] = WhsExpZero::create([
									'wez_userid'            => $rs[$j]['LOGIN'],
									'wez_username'          => $rs[$j]['NAME'],
									'wez_userbal'           => $rs[$j]['BALANCE'],
									'wez_usercrt'           => $rs[$j]['CREDIT'],
									'voided'                => '1',
									'rec_crt_user'          => env('COMPANY_CODE'),
									'rec_upd_user'          => env('COMPANY_CODE'),
									'rec_crt_date'          => date ('Y-m-d H:i:s'),
									'rec_upd_date'          => date ('Y-m-d H:i:s'),
								]);
							}
						}
						
						$no++;
					}
					
					if ((int)$no == count($rs)) {
						//单次请求处理的数据已经完成，更新列的值 为 0
						$upd = SystemConfig::where('sys_id', 1)->update(['trades_whs_exp_zero' => '0']);
					}
				} else {
					echo '没有符合条件的数据';
				}
			}
		}
		
		protected function get_whs_exp_zero_id_list($totalType, $data)
		{
			$query_sql = WhsExpZero::select(
					'whs_exp_zero.wez_userid as wezuserid', 'whs_exp_zero.wez_username as wezusername',
					'whs_exp_zero.wez_userbal as wezuserbal', 'whs_exp_zero.wez_usercrt as wezusercrt',
					'whs_exp_zero.wez_status as wezstatus',
					'whs_exp_zero.rec_crt_date as rec_crt_date'
				)->where('voided', '1')->where(function ($subWhere) use ($data) {
					$this->_exte_set_search_condition($subWhere, $data);
				});
			
			return $this->_exte_get_query_sql_data($query_sql, $totalType, 'whs_exp_zero.rec_crt_date');
		}
		
		//清零成功后，主动更新账户余额信息，并短信通知
		protected function _exte_whs_exp_zero_success_response($uid) {
			
			$_rs = false;
			
			if (env('SYNCMT4_UPDATEINFO')) {
				$num = $this->_exte_mt4_update_local_user_info($uid);
				
				if ($num) {
					$_rs = true;
				}
			}
			
			
			/*$num = $_table::where('voided', '1')->where('user_status', array('0', '1', '2', '4'))
				->find($uid)->update([
					'user_money'            => '0.00',
					'cust_eqy'              => '0.00',
					'rec_upd_date'          => date ('Y-m-d H:i:s'),
					'rec_upd_user'          => env('COMPANY_CODE'),
				]);
			
			if ($num) {
				//更新成功，短信通知
				$_info = $this->_exte_get_user_info($uid);
				$phone = substr($_info['phone'], (stripos($_info['phone'], '-') + 1));
				$_rs = $this->_exte_send_phone_notify($phone, 'WhsExpZeroSms', $_info);
			}*/
			
			return $_rs;
		}
		
		protected function _exte_get_query_sql_data($sql, $totalType, $col, $orderBy = 'desc')
		{
			$id_list		= array ();
			
			if ($totalType == 'page') {
				$id_list = $sql->orderBy($col, $orderBy)->get()->toArray();
			} else if ($totalType == 'count') {
				$id_list = $sql->count();
			} else if ($totalType == 'sum') {
				$id_list = $sql->get()->toArray();
			}
			
			return $id_list;
		}
		
		protected function _exte_set_search_condition($subWhere, $data)
		{
			if (!empty($data['rec_crt_date_start']) && !empty($data['rec_crt_date_end']) && $this->_exte_is_Date ($data['rec_crt_date_start']) && $this->_exte_is_Date ($data['rec_crt_date_end'])) {
				$subWhere->whereBetween('whs_exp_zero.rec_crt_date', [$data['rec_crt_date_start'] .' 00:00:00', $data['rec_crt_date_end'] . ' 23:59:59']);
			} else {
				if(!empty($data['rec_crt_date_start']) && $this->_exte_is_Date ($data['rec_crt_date_start'])) {
					$subWhere->where('whs_exp_zero.rec_crt_date',  '>= ', $data['rec_crt_date_start'] .' 23:59:59');
				}
				if(!empty($data['rec_crt_date_end']) && $this->_exte_is_Date ($data['rec_crt_date_end'])) {
					$subWhere->where('whs_exp_zero.rec_crt_date', '<', $data['rec_crt_date_end'] .' 00:00:00');
				}
			}
			
			if (!empty($data['wez_userid'])) {
				$subWhere->where('whs_exp_zero.wez_userid', 'like', '%' . $data['wez_userid'] . '%');
			}
			if (!empty($data['wez_username'])) {
				$subWhere->where('whs_exp_zero.wez_username', 'like', '%' . $data['wez_username'] . '%');
			}
			if ($data['wez_status'] != '') {
				$subWhere->where('whs_exp_zero.wez_status', $data['wez_status']);
			}
			
			return $subWhere;
		}
	}