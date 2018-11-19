<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018/6/26
	 * Time: 14:53
	 */
	
	namespace App\Http\Controllers\User;
	
	
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	use Illuminate\Http\Request;
	
	class UserForgetPswController extends Abstract_Mt4service_Controller
	{
		public function forget_password_browse ()
		{
			return view('user.forget_password.forget_password_browse');
		}
		
		public function checkUserInfo (Request $request)
		{
			$userId         = $request->userId;
			$uerphoneNo     = $request->userphoneNo;
			$chekc_status   = true;
			
			$_info          = $this->_exte_get_user_info($userId);
			$phone          = substr($_info['phone'], (stripos($_info['phone'], '-') + 1));
			
			if ($_info == null) {
				$chekc_status = false;
				return response()->json([
					'msg'    => 'FAIL',
					'err'    => 'IDerror',
					'col'    => 'userId',
				]);
			}
			
			if ($_info['enable'] == 0) {
				$chekc_status = false;
				return response()->json([
					'msg'    => 'FAIL',
					'err'    => 'UserDisable',
					'col'    => 'userId',
				]);
			}
			
			if ($phone != $uerphoneNo) {
				$chekc_status = false;
				return response()->json([
					'msg'    => 'FAIL',
					'err'    => 'phoneErr',
					'col'    => 'userphoneNo',
				]);
			}
			
			if ($chekc_status) {
				return response()->json([
					'msg'    => 'SUC',
					'err'    => 'noerr',
					'col'    => 'nocol',
				]);
			}
		}
		
		public function forgetpswSendCode(Request $request)
		{
			$userId         = $request->userId;
			$uerphoneNo     = $request->userphoneNo;
			$code           = rand(123456, 99999);
			$request->session()->forget('ResetPasswordCode');
			$request->session()->forget('ResetPasswordPhone');
			
			$_rs            = $this->_exte_send_phone_notify($uerphoneNo, 'changePassword', array('code' => $code));
			
			if ($_rs) {
				$request->session()->put('ResetPasswordCode', $code);
				$request->session()->put('ResetPasswordPhone', $uerphoneNo);
				return response()->json([
					'status' => true,
				]);
			} else {
				return response()->json([
					'status' => false,
				]);
			}
		}
		
		public function forgetPasswordInfoVerification (Request $request)
		{
			$userId         = $request->userId;
			$uerphoneNo     = $request->userphoneNo;
			$codedata       = $request->codedata;
			$check_status   = true;
			
			$ResetPasswordCode = $request->session()->get('ResetPasswordCode');
			$ResetPasswordPhone = $request->session()->get('ResetPasswordPhone');
			
			if ($ResetPasswordCode != $codedata) {
				$check_status = false;
				return response()->json([
					'msg'    => 'FAIL',
					'err'    => 'errorCodedate',
					'col'    => 'getVerifyCode',
				]);
			}
			
			if ($ResetPasswordPhone != $uerphoneNo) {
				$check_status = false;
				return response()->json([
					'msg'    => 'FAIL',
					'err'    => 'PhoneDiff',
					'col'    => 'userphoneNo',
				]);
			}
			
			if ($check_status) {
				return response()->json([
					'msg'    => 'SUC',
					'err'    => 'noerr',
					'col'    => 'nocol',
				]);
			}
		}
		
		public function saveChangePassword (Request $request)
		{
			$userId         = $request->userId;
			$newPsw         = $request->newPsw;
			
			$_table         = $this->_exte_get_table_obj($userId);
			
			//同步MT4更新密码
			$mt4            = $this->_exte_mt4_reset_user_pwd($userId, $newPsw);
			if ($mt4['ret'] == '0') {
				$num = $_table::where('user_id', $userId)
					->update([
						'password'      => base64_encode($newPsw),
						'rec_upd_date'  => date('Y-m-d H:i:s'),
					]);
				
				if ($num) {
					$request->session()->flush();
					return response()->json([
						'msg'    => 'SUC',
						'err'    => 'noerr',
						'col'    => 'nocol',
					]);
				} else {
					return response()->json([
						'msg'    => 'FAIL',
						'err'    => 'updateerr',
						'col'    => 'nocol',
					]);
				}
			} else {
				return response()->json([
					'msg'    => 'FAIL',
					'err'    => 'neterr',
					'col'    => 'nocol',
				]);
			}
			
		}
	}