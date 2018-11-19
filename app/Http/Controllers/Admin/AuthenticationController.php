<?php
	/**
	 * Created by PhpStorm.
	 * User: Hank
	 * Date: 2018/4/2
	 * Time: 17:04
	 */
	
	namespace App\Http\Controllers\admin;
	
	use Illuminate\Http\Request;
	use App\Model\DataList;
	use App\Model\UserImg;
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	
	class AuthenticationController extends Abstract_Mt4service_Controller
	{
		//待审核
		public function user_examine ()
		{
			return view ('admin.user_examin.user_examin_browse');
		}
		
		//已审核列表
		public function user_certified ()
		{
			return view ('admin.user_examin.user_certified_browse')->with(['role' => $this->Role()]);
		}
		
		//已审核人员信息详情
		public function userCertifiedDetail ($uid)
		{
			$_info = $this->_exte_get_user_info ($uid);
			$_info['img'] = UserImg::where('user_id', $uid)->where('voided', '1')->get()->toArray();
			
			return view ('admin.user_examin.user_certified_detail')->with (['_info' => $_info]);
		}
		
		//待审核列表
		public function userExaminSearch (Request $request)
		{
			$data = array(
				'userId'             => $request->userId,
				'userName'           => $request->username,
				'startdate'          => $request->startdate,
				'enddate'            => $request->enddate,
			);
			
			$result = array('rows' => '', 'total' => '');
			
			$_rs = $this->admin_get_current_user_examin_id_list('page', $data);
			if (!empty($_rs)) {
				$result['rows'] = $_rs;
				$result['total'] = $this->admin_get_current_user_examin_id_list ('count', $data);
			}
			
			return json_encode ($result);
		}
		
		//已经审核过的列表， bank_status = 2,IDcard_status = 2
		public function userCertifiedSearch (Request $request)
		{
			$data = array(
				'userId'             => $request->userId,
				'userName'           => $request->username,
				'startdate'          => $request->startdate,
				'enddate'            => $request->enddate,
			);
			
			$result = array('rows' => '', 'total' => '');
			
			$_rs = $this->admin_get_current_user_certified_id_list('page', $data);
			if (!empty($_rs)) {
				$result['rows'] = $_rs;
				$result['total'] = $this->admin_get_current_user_certified_id_list ('count', $data);
			}
			
			return json_encode ($result);
		}
		
		//审核TA
		public function user_examine_detail ($uid)
		{
			$_info = $this->_exte_get_user_info ($uid);
			$_info['img'] = UserImg::where('user_id', $uid)->where('voided', '1')->get()->toArray();
			
			return view ('admin.user_examin.user_examin_detail')->with (['_info' => $_info]);
		}
		
		//审核身份证，银行卡
		public function user_idcard_bank(Request $request)
		{
			$userId                 = $request->userId;
			$username               = $request->username;
			$idcard_auth            = $request->idcard_auth;
			$bank_auth              = $request->bank_auth;
			$userIdcard_status      = $request->userIdcard_status;
			$userbank_status        = $request->userbank_status;
			$idcard_reason          = $request->idcard_reason;
			$bank_reason            = $request->bank_reason;
			$_table                 = $this->_exte_get_table_obj($userId);
			$num                    = false;
			$num2                   = false;
			
			//身份证待审核状态
			if ($userIdcard_status == '1') {
				$num = $_table::where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))
					->find($userId)->update([
						'user_name'             => $username,
						'IDcard_status'         => ($idcard_auth == '0') ? '2' : '4', //审核通过、不通过
						'IDcard_remarks'        => ($idcard_auth == '0') ? '' : $idcard_reason,
						'rec_upd_user'          => $this->_auser['username'],
						'rec_upd_date'          => date('Y-m-d H:i:s'),
					]);
				
				if ($idcard_auth == '2') {
					$col_ary = array('login' => $userId, 'name' => $this->_exte_mt4_username_convert_encode($username));
					$mt4 = $this->_exte_mt4_update_user($col_ary);
				}
			}
			
			//银行卡待审核状态
			if ($userbank_status == '1') {
				$num2 = $_table::where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))
					->find($userId)->update([
						'user_name'             => $username,
						'bank_status'           => ($bank_auth == '0') ? '2' : '4', //审核通过、不通过
						'bank_remarks'          => ($bank_auth == '0') ? '' : $bank_reason,
						'rec_upd_user'          => $this->_auser['username'],
						'rec_upd_date'          => date('Y-m-d H:i:s'),
					]);
			}
			
			//更新完相应的列后检查当前用户身份证和银行卡认证状态，此时需要查找当前用户认证信息状态
			$_user_info             = $this->_exte_get_user_info($userId);
			if ($_user_info['IDcard_status'] == '2' && $_user_info['bank_status'] == '2') {
				//当身份证和银行卡同时审核通过，则更新 user_status = 1;
				$num3 = $_table::where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))
					->find($userId)->update([
						'user_name'             => $username,
						'user_status'           => '1',
						'bank_no'               => $_user_info['bank_no_tmp'],
						'bank_class'            => $_user_info['bank_class_tmp'],
						'bank_info'             => $_user_info['bank_info_tmp'],
						'rec_upd_user'          => $this->_auser['username'],
						'rec_upd_date'          => date('Y-m-d H:i:s'),
					]);
				
				/*$_user_info             = $this->_exte_get_user_info($userId);
				 if ($num3) {
					//更新成功当前用户认证信息后，将认证后的银行卡号和开户行支行地址同步更新到mt4_users.address
					$col_ary = array('login' => $userId, 'address' => $this->_exte_mt4_username_convert_encode($_user_info['bank_no'] . '|' . $_user_info['bank_info']));
					$mt4 = $this->_exte_mt4_update_user($col_ary);
				} */
			}
			
			if ($num || $num2) {
				return response()->json([
					'msg'               => 'SUC',
					'err'               => 'NOERR',
				]);
			} else {
				return response()->json([
					'msg'               => 'FAIL',
					'err'               => 'UPDATEFAIL',
				]);
			}
		}
		
		//待审核列表
		protected function admin_get_current_user_examin_id_list ($totalType, $data)
		{
			$query_sql = DataList::select(
				'data_list.user_id', 'data_list.user_name', 'data_list.parent_id', 'data_list.IDcard_status', 'data_list.bank_status', 'data_list.mt4_grp', 'data_list.rec_crt_date'
			)->where(function ($where) {
				$where->where('data_list.IDcard_status', '1')->whereNotIn('data_list.IDcard_status', array('0', '2', '4'))
					->Orwhere(function ($where) {
						$where->where('data_list.bank_status', '1')->whereNotIn('data_list.bank_status', array('0', '2', '4'));
					});
			})->whereIn('data_list.user_status', array('0', '1', '2', '4'))
			->where(function ($subWhere) use ($data) {
				$this->_exte_set_search_condition($subWhere, $data);
			});
			
			return $this->_exte_get_query_sql_data($query_sql, $totalType, 'data_list.rec_crt_date');
		}
		
		//已审核列表
		protected function admin_get_current_user_certified_id_list ($totalType, $data)
		{
			$query_sql = DataList::select(
				'data_list.user_id', 'data_list.user_name', 'data_list.parent_id', 'data_list.IDcard_status', 'data_list.bank_status', 'data_list.mt4_grp', 'data_list.rec_crt_date', 'data_list.rec_upd_date'
			)->where('data_list.IDcard_status', '2')->where('data_list.bank_status', '2')->whereIn('data_list.user_status', array('0', '1', '2', '4'))
				->where(function ($subWhere) use ($data) {
					$this->_exte_set_search_condition($subWhere, $data);
				});
			
			return $this->_exte_get_query_sql_data($query_sql, $totalType, 'data_list.rec_crt_date');
		}
		
		protected function _exte_set_search_condition($subWhere, $data)
		{
			if (!empty($data['startdate']) && !empty($data['enddate']) && $this->_exte_is_Date ($data['startdate']) && $this->_exte_is_Date ($data['enddate'])) {
				$subWhere->whereBetween('data_list.rec_crt_date', [$data['startdate'] .' 00:00:00', $data['enddate'] . ' 23:59:59']);
			} else {
				if(!empty($data['startdate']) && $this->_exte_is_Date ($data['startdate'])) {
					$subWhere->where('data_list.rec_crt_date',  '>= ', $data['stdwqartdate'] .' 23:59:59');
				}
				if(!empty($data['enddate']) && $this->_exte_is_Date ($data['enddate'])) {
					$subWhere->where('data_list.rec_crt_date', '<', $data['enddate'] .' 00:00:00');
				}
			}
			
			if (!empty($data['userId'])) {
				$subWhere->where('data_list.user_id', $data['userId']);
			}
			if (!empty($data['userName'])) {
				$subWhere->where('data_list.user_name', 'like', '%' . $data['userName'] . '%');
			}
		}
	}