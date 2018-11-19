<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018-05-21
	 * Time: 下午 5:23
	 */
	
	namespace App\Http\Controllers\Admin;
	
	use App\Model\Mt4Trades;
	use App\Model\Mt4Users;
	use App\Model\CancelApply;
	use App\Model\OperationLog;
	use Illuminate\Http\Request;
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	
	class CancellationController extends Abstract_Mt4service_Controller
	{
		public function cancel_user_list ()
		{
			return view('admin.cancel_list.cancel_list_brose');
		}
		
		public function userlistSearch (Request $request)
		{
			$data = array(
				'userId'             => $request->userId,
				'status'            => $request->cancel_status,
				'startdate'          => $request->startdate,
				'enddate'            => $request->enddate,
			);
			
			$result = array('rows' => '', 'total' => '');
			
			$_rs = $this->get_all_cancel_list ('page', $data);
			
			if (!empty($_rs)) {
				$_bal_vol = $this->get_cancel_apply_info ($_rs);
				
				if(!empty($_bal_vol)) {
					for ($i = 0; $i < count($_rs); $i ++) {
						if (!empty($_bal_vol[$_rs[$i]['cancel_userid']]['bal'])) {
							$_rs[$i]['bal'] = number_format($_bal_vol[$_rs[$i]['cancel_userid']]['bal'][0]['BALANCE'], 2, '.', '');
						} else {
							$_rs[$i]['bal'] = number_format(0, 2, '.', '');
						}
						
						$_rs[$i]['vol'] = $_bal_vol[$_rs[$i]['cancel_userid']]['vol'];
					}
				}
				
				$result['rows'] = $_rs;
				$result['total'] = $this->get_all_cancel_list ('count', $data);
			}
			
			return json_encode ($result);
		}
		
		public function cancel_apply_pass(Request $request) {
			
			$_cancel_userid = $request->cancel_userid;
			$chk_cancel_info = CancelApply::where('cancel_userid', $_cancel_userid)->where('cancel_status', '0')->where('voided', '1')->first();
			$_table = $this->_exte_get_table_obj($_cancel_userid);
			$cancel_cust_info = $this->_exte_get_user_info($_cancel_userid);
			
			if($chk_cancel_info == null) {
				return response()->json([
					'msg'                   => 'FAIL',
					'col'                   => 'INVALIDUSER',
				]);
			} else {
				//禁止客户登录及交易
				$params = array('login' => $_cancel_userid, 'enable' => 0, 'enable_read_only' => 1);
				$mt4_data = $this->_exte_mt4_update_user($params);
				
				if(is_array($mt4_data) && $mt4_data['ret'] != '0') {
					return response()->json([
						'msg'                   => 'FAIL',
						'col'                   => 'FATALCANOTCONNECT',
					]);
				} else if ($mt4_data['ret'] == '0') {
					$upd_cust = $_table::where('user_id', $_cancel_userid)->where('voided', '1')
						->whereIn('user_status', array ('0', '1', '2', '4'))
						->update([
							'enable'                => 0,
							'voided'                => '-1',
							'user_status'           => '-1',
							'rec_upd_date'          => date ('Y-m-d H:i:s'),
							'rec_upd_user'          => $this->_auser['username'],
						]);
					
					$upd_cancel = CancelApply::where('cancel_userid', $_cancel_userid)->where('cancel_status', '0')
						->where('voided', '1')
						->update([
							'cancel_status'         => '1', //通过销户申请
							'rec_upd_date'          => date ('Y-m-d H:i:s'),
							'rec_upd_user'          => $this->_auser['username'],
						]);
					$ip = $this->_exte_get_user_loginIp();
					$crt_log = OperationLog::create([
						'name'                  => $this->_auser['username'],
						'user_id'               => $_cancel_userid,
						'order_number'          => 0,
						'content'               => '[' . $this->_auser['username'] . '] ' . ' 接受了 ' . $chk_cancel_info['cancel_username'] . '[' . $chk_cancel_info['cancel_userid'] . '] ' . ', '. '账户注销申请',
						'handle_ip'             => $this->_exte_get_user_loginIpCity($ip),
						'created_on'            => time(),
						'type'                  => '0',
						'role_class'			=> $this->_auser['username'],
					]);
					
					if($upd_cust && $upd_cancel && $crt_log) {
						//短信通知
						$phone = substr($cancel_cust_info['phone'], (stripos($cancel_cust_info['phone'], '-') + 1));
						$msg = $this->_exte_send_phone_notify($phone, 'accept', $cancel_cust_info);
						return response()->json([
							'msg'               => 'SUCCESS',
							'col'               => 'UPDATESUC',
						]);
					} else {
						return response()->json([
							'msg'               => 'FAIL',
							'col'               => 'UPDATEFAIL',
						]);
					}
				}
			}
		}
		
		public function cancel_apply_nopass(Request $request) {
			
			$_cancel_remark = $request->cancel_remark;
			$_cancel_userid = $request->cancel_userid;
			$chk_cancel_info = CancelApply::where('cancel_userid', $_cancel_userid)->where('cancel_status', '0')->where('voided', '1')->first();
			
			$_table = $this->_exte_get_table_obj($_cancel_userid);
			$cancel_cust_info = $this->_exte_get_user_info($_cancel_userid);
			
			if($chk_cancel_info == null) {
				return response()->json([
					'msg'                       => 'FAIL',
					'col'                       => 'INVALIDUSER',
				]);
			} else {
				//关闭用只读录状态
				$params = array('login' => $_cancel_userid, 'enable' => 1, 'enable_read_only' => 0);
				$mt4_data = $this->_exte_mt4_update_user($params);
				if(is_array($mt4_data) && $mt4_data['ret'] != '0') {
					return response()->json([
						'msg'                   => 'FAIL',
						'col'                   => 'FATALCANOTCONNECT',
					]);
				} else if ($mt4_data['ret'] == '0') {
					$upd_cust = $_table::where('user_id', $_cancel_userid)->where('voided', '1')
						->whereIn('user_status', array ('0', '1', '2', '4'))
						->update([
							'enable_readonly'       => 0, //同步MT4数据
							'is_out_money'          => '0', //0允许出金, 1不允许出金
							'user_status'           => $cancel_cust_info['user_status'],
							'rec_upd_date'          => date ('Y-m-d H:i:s'),
							'rec_upd_user'          => $this->_auser['username'],
						]);
					
					$upd_cancel = CancelApply::where('cancel_userid', $_cancel_userid)->where('cancel_status', '0')
						->where('voided', '1')
						->update([
							'cancel_status'         => '-1',//拒绝销户申请
							'cancel_remark'         => $_cancel_remark,
							'rec_upd_date'          => date ('Y-m-d H:i:s'),
							'rec_upd_user'          => $this->_auser['username'],
						]);
					
					$ip = $this->_exte_get_user_loginIp();
					$crt_log = OperationLog::create([
						'name'                  => $this->_auser['username'],
						'user_id'               => $_cancel_userid,
						'order_number'          => 0,
						'content'               => '[' . $this->_auser['username'] . '] ' . ' 拒绝了 ' . $chk_cancel_info['cancel_username'] . '[' . $chk_cancel_info['cancel_userid'] . '] ' . ', '. '账户注销申请',
						'handle_ip'             => $this->_exte_get_user_loginIpCity($ip),
						'created_on'            => time(),
						'type'                  => '0',
						'role_class'			=> $this->_auser['username'],
					]);
					
					if($upd_cust && $upd_cancel && $crt_log) {
						//短信通知
						$phone = substr($cancel_cust_info['phone'], (stripos($cancel_cust_info['phone'], '-') + 1));
						$msg = $this->_exte_send_phone_notify($phone, 'refuse', $cancel_cust_info);
						return response()->json([
							'msg'              => 'SUCCESS',
							'col'              => 'UPDATESUC',
						]);
					} else {
						return response()->json([
							'msg'              => 'FAIL',
							'col'              => 'UPDATEFAIL',
						]);
					}
				}
			}
		}
		
		protected function get_all_cancel_list($totalType, $data)
		{
			$query_sql = CancelApply::whereIn('voided', array('0', '1'))
				->where(function ($subWhere) use ($data) {
					$this->_exte_set_search_condition($subWhere, $data);
				});
			
			return $this->_exte_get_query_sql_data($query_sql, $totalType, 'cancel_apply.rec_crt_date');
		}
		
		protected function get_cancel_apply_info($list) {
			
			$_rs = array();
			
			if(!empty($list)) {
				foreach ($list as $k => $v) {
					$_rs[$v['cancel_userid']]['bal'] = Mt4Users::select('LOGIN', 'NAME', 'BALANCE', 'CREDIT')->where('LOGIN', $v['cancel_userid'])->get()->toArray();
					$_rs[$v['cancel_userid']]['vol'] = Mt4Trades::where('LOGIN', $v['cancel_userid'])->where('CLOSE_TIME', '1970-01-01 00:00:00')->whereIn('CMD', array(0, 1, 2, 3, 4, 5))->where('CONV_RATE1', '<>', 0)->count();
				}
			}
			
			return $_rs;
			
		}
		
		protected function _exte_set_search_condition($subWhere, $data)
		{
			if (!empty($data['startdate']) && !empty($data['enddate']) && $this->_exte_is_Date ($data['startdate']) && $this->_exte_is_Date ($data['enddate'])) {
				$subWhere->whereBetween('cancel_apply.rec_crt_date', [$data['startdate'] .' 00:00:00', $data['enddate'] . ' 23:59:59']);
			} else {
				if(!empty($data['startdate']) && $this->_exte_is_Date ($data['startdate'])) {
					$subWhere->where('cancel_apply.rec_crt_date',  '>=', $data['startdate'] .' 23:59:59');
				}
				if(!empty($data['enddate']) && $this->_exte_is_Date ($data['enddate'])) {
					$subWhere->where('cancel_apply.rec_crt_date', '<', $data['enddate'] .' 00:00:00');
				}
			}
			
			if (!empty($data['userId'])) {
				$subWhere->where('cancel_apply.cancel_userid', $data['userId']);
			}
			
			if(!empty($data['status'])) {
				$subWhere->where('cancel_apply.cancel_status', 'like', '%' . $data['status'] . '%');
			}
			
			return $subWhere;
		}
	}