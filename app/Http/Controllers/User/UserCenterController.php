<?php
	/**
	 * Created by PhpStorm.
	 * User: Hank
	 * Date: 2018/3/28
	 * Time: 17:21
	 */
	
	namespace App\Http\Controllers\User;
	
	use App\Model\Mt4Trades;
	use App\Model\Mt4Users;
	use Illuminate\Http\Request;
	use App\Model\CancelApply;
	use App\Model\DrawRecordLog;
	use App\Model\UserImg;
	use App\Model\DataList;
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	
	class UserCenterController extends Abstract_Mt4service_Controller
	{
		public function user_info_browse ()
		{
			$_info = $this->_exte_get_user_info ($this->_user['user_id']);
			
			//查找用户是否正在进行销户申请
			$_info['cancel_info'] = CancelApply::where('cancel_userid', $this->_user['user_id'])->where('voided', '1')->get()->toArray();
			$_info['img'] = UserImg::select('img_header_path')->where('user_id', $this->_user['user_id'])->where('voided', '1')->get()->toArray();
			
			//检查用户是否满足银行卡变更申请条件1. 没有正在出金, 2. 银行卡不在审核期间
			$_info['is_change'] = (DrawRecordLog::where('user_id', $this->_user['user_id'])->where('voided', '1')->whereIn('apply_status', array('0', '1'))->first() == null) ? 'allowChange' : 'notAllowChange';
			
			return view ('user.user_center.user_info')->with (['_info' => $_info]);
		}
		
		public function uploadIdCard_browse ()
		{
			return view ('user.user_center.user_uploadIdCard')->with (['_user_info' => $this->_user]);
		}
		
		public function uploadBank_browse ()
		{
			return view ('user.user_center.user_uploadBank')->with (['_user_info' => $this->_user]);
		}
		
		public function uploadChangeBank_browse ($type)
		{
			return view('user.user_center.user_uploadChangeBank')->with (['_user_info' => $this->_user, 'type' => $type]);
		}
		
		public function uploadHead_browse ()
		{
			return view('user.user_center.user_uploadhead_browse');
		}
		
		public function updPhoneEmail_browse ($type)
		{
			return view ('user.user_center.user_upd_phone_email')->with (['_user_info' => $this->_user, 'type' => $type]);
		}
		
		public function cancelAccount_browse ()
		{
			return view ('user.user_center.user_cancel_account')->with (['_user_info' => $this->_user]);
		}
		
		//修改密码
		public function user_editpsw_browse()
		{
			return view('user.user_center.user_editpsw_browse');
		}
		
		public function user_editpsw_save(Request $request)
		{
			
			$olduserpsw     =   $request->olduserpsw;
			$newuserpsw     =   $request->newuserpsw;
			$confirmuserpsw =   $request->confirmuserpsw;
			
			//先本地检查旧密码是否正确，在API检查是否和MT4一致
			$_curr_info     = $this->_exte_get_user_info($this->_user['user_id']);
			
			if (base64_decode($_curr_info['password']) != $olduserpsw) {
				return response()->json([
					'msg'           => 'FAIL',
					'err'           => 'localpswerr',
					'col'           => 'olduserpsw',
				]);
			}
			
			//远程API检查密码
			$mt4 = $this->_exte_mt4_verify_password($this->_user['user_id'], $olduserpsw);
			if ($mt4['ret'] != '0') {
				return response()->json([
					'msg'           => 'FAIL',
					'err'           => 'apipswerr',
					'col'           => 'olduserpsw',
				]);
			}
			
			//开始重置密码
			$rest_psw = $this->_exte_mt4_reset_user_pwd($this->_user['user_id'], $newuserpsw);
			if ($rest_psw['ret'] == '0') {
				//同步MT4密码成功, 开始更新本地表记录
				$_table = $this->_exte_get_table_obj($this->_user['user_id']);
				$num = $_table::where('voided', '1')
					->find($this->_user['user_id'])
					->update([
						'password'      => base64_encode($newuserpsw),
						'rec_upd_date'  => date('Y-m-d H:i:s'),
						'rec_upd_user'  => $this->_user['user_name'],
					]);
				
				if ($num) {
					//清除所有session 值
					$request->session()->flush();
					return response()->json([
						'msg'           => 'SUCCESS',
						'err'           => 'noerr',
						'col'           => 'nocol',
					]);
				} else {
					return response()->json([
						'msg'           => 'FAIL',
						'err'           => 'UPDATEFAIL',
						'col'           => 'nocol',
					]);
				}
			} else {
				return response()->json([
					'msg'              => 'FAIL',
					'err'              => 'FATALCANOTCONNECT',
					'col'              => 'nocol',
				]);
			}
		}
		
		public function uploadIdCard (Request $request)
		{
			// IdCardUpload, BankUpload
			$uploadType = $request->uploadType;
			$username = $request->username;
			$userIdcardNo = $request->userIdcardNo;
			$_table = $this->_exte_get_table_obj ($this->_user['user_id']);
			$_info = $this->_exte_get_user_info ($this->_user['user_id']);
			
			//检查身份证唯一性
			if($userIdcardNo != $_info['IDcard_no'] && $this->_exte_verify_idno ($userIdcardNo)) {
				return response()->json([
					'msg'       => 'FAIL',
					'col'       => 'userIdcardNo',
					'err'       => 'IdcardNoExiste',
				]);
			};
			
			//同步MT4更新用户名和身份证号码
			$col_ary = array('login' => $this->_user['user_id'], 'name' => $this->_exte_mt4_username_convert_encode($username), /* 'id' => $userIdcardNo */);
			$mt4 = $this->_exte_mt4_update_user ($col_ary);
			if(!is_array($mt4)) {
				return response()->json([
					'msg'       => 'FAIL',
					'err'       => 'FATALCANOTCONNECT',
					'col'       => 'mt4',
				]);
			} else if (is_array($mt4) && $mt4['ret'] == '0') {
				//开始上传图片
				$upload = $this->_exte_upload_file_idCardPhoto($request);
				
				if ($upload['msg'] == 'FAIL') {
					return response()->json([
						'msg'       => $upload['msg'],
						'err'       => $upload['err'],
						'col'       => $upload['col'],
					]);
				} else if ($upload['msg'] == 'SUC') {
					//上传成功更新用户认证状态
					$upd_num = $_table::where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))
						->find($this->_user['user_id'])->update([
							'IDcard_no'         => $userIdcardNo,
							'user_name'         => $username,
							//'IDcard_img'        => $upload['photo1path'], //正面
							//'IDcard_negative'   => $upload['photo2path'], //反面
							'IDcard_status'     => '1', //身份证审核状态 默认 0 待上传，1 正在审核中，2 审核通过
							'rec_upd_date'      => date('Y-m-d H:i:s'),
							'rec_upd_user'      => $username,
						]);
					
					
					if(count($upd_num) > 0 ) {
						return response()->json([
							'msg'               => 'SUC',
							'err'               => 'NOTERROR',
						]);
					} else {
						return response()->json([
							'msg'               => 'FAIL',
							'err'               => 'UPDATEFAIL',
						]);
					}
				}
			}
		}
		
		public function uploadBankCard (Request $request)
		{
			$uploadType     = $request->uploadType;
			$username       = $request->username;
			$bankclass      = $request->bankclass;
			$bankinfo       = $request->bankinfo;
			$bankno         = $request->bankno;
			
			$_table = $this->_exte_get_table_obj ($this->_user['user_id']);
			$upload = $this->_exte_upload_file_bankPhoto($request);
			if ($upload['msg'] == 'FAIL') {
				return response()->json([
					'msg'       => $upload['msg'],
					'err'       => $upload['err'],
					'col'       => $upload['col'],
				]);
			} else if ($upload['msg'] == 'SUC') {
				//上传成功更新用户认证状态
				$upd_num = $_table::where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))
					->find($this->_user['user_id'])->update([
						'bank_no_tmp'           => $bankno,
						'bank_class_tmp'        => $bankclass,
						'bank_info_tmp'         => $bankinfo,
						'bank_status'           => '1', //银行卡审核状态 0 带上传，1 正在审核中...，2 审核通过
						'rec_upd_date'          => date('Y-m-d H:i:s'),
						'rec_upd_user'          => $this->_user['user_name'],
					]);
				
				if(count($upd_num) > 0 ) {
					return response()->json([
						'msg'               => 'SUC',
						'err'               => 'NOTERROR',
					]);
				} else {
					return response()->json([
						'msg'               => 'FAIL',
						'err'               => 'UPDATEFAIL',
					]);
				}
			}
		}
		
		public function uploadChangeBankCard (Request $request)
		{
			$bankclass      = $request->bankclass;
			$bankinfo       = $request->bankinfo;
			$bankno         = $request->bankno;
			$userphoneNo    = $request->userphoneNo;
			$userverfcode   = $request->userverfcode;
			$password       = $request->password;
			
			$changeCode     = $request->session ()->get('changeCode');
			$changePhoneNo  = $request->session ()->get('changePhoneNo');
			//检查用户是否满足银行卡变更申请条件1. 没有正在出金, 2. 银行卡不在审核期间
			$_info = $this->_exte_get_user_info ($this->_user['user_id']);
			$_info['chk_isChange'] = DrawRecordLog::select('apply_status')->where('user_id', $this->_user['user_id'])->where('voided', '1')->whereIn('apply_status', array('0', '1'))->first();
			
			if ($_info['bank_status'] != '2') {
				return response()->json([
					'msg'               => 'FAIL',
					'err'               => 'errbankpendingauth',
					'col'               => 'nocol',
				]);
			}
			
			if (!empty($_info['chk_isChange'])) {
				return response()->json([
					'msg'               => 'FAIL',
					'err'               => 'errisapplying',
					'col'               => 'nocol',
				]);
			}
			
			//上传前验证密码
			$mt4 = $this->_exte_mt4_verify_password ($this->_user['user_id'], $password);
			if (is_array($mt4) && $mt4['ret'] != '0') {
				return response()->json([
					'msg'               => 'FAIL',
					'err'               => 'errpassword',
					'col'               => 'password',
				]);
			}
			
			if ($userphoneNo != $changePhoneNo) {
				return response()->json([
					'msg'               => 'FAIL',
					'err'               => 'erruserphoneNo',
					'col'               => 'userphoneNo',
				]);
			}
			
			if ($userverfcode != $changeCode) {
				return response()->json([
					'msg'               => 'FAIL',
					'err'               => 'erruserverfcode',
					'col'               => 'userverfcode',
				]);
			}
			
			$_table = $this->_exte_get_table_obj ($this->_user['user_id']);
			$upload = $this->_exte_upload_file_bankPhoto($request);
			if ($upload['msg'] == 'FAIL') {
				return response()->json([
					'msg'       => $upload['msg'],
					'err'       => $upload['err'],
					'col'       => $upload['col'],
				]);
			} else if ($upload['msg'] == 'SUC') {
				//上传成功更新用户认证状态
				$upd_num = $_table::where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))
					->find($this->_user['user_id'])->update([
						'bank_no_tmp'           => $bankno,
						'bank_class_tmp'        => $bankclass,
						'bank_info_tmp'         => $bankinfo,
						'bank_status'           => '1', //银行卡审核状态 0 带上传，1 正在审核中...，2 审核通过
						'rec_upd_date'          => date('Y-m-d H:i:s'),
						'rec_upd_user'          => $this->_user['user_name'],
					]);
				
				if(count($upd_num) > 0 ) {
					return response()->json([
						'msg'               => 'SUC',
						'err'               => 'NOTERROR',
					]);
				} else {
					return response()->json([
						'msg'               => 'FAIL',
						'err'               => 'UPDATEFAIL',
					]);
				}
			}
		}
		
		public function uploadHeadImg (Request $request)
		{
			$upload = $this->_exte_upload_file_head_img($request);
			
			if ($upload['msg'] == 'FAIL') {
				return response()->json([
					'msg'       => $upload['msg'],
					'err'       => $upload['err'],
					'col'       => $upload['col'],
				]);
			} else if ($upload['msg'] == 'SUC') {
				//上传成功更新用户认证状态
				return response()->json([
					'msg'               => 'SUC',
					'err'               => 'NOTERROR',
				]);
			} else {
				return response()->json([
					'msg'               => 'FAIL',
					'err'               => 'UPDATEFAIL',
				]);
			}
		}
		
		public function changeBankCardVerifyCode(Request $request)
		{
			$userphoneNo        = $request->userphoneNo;
			
			$_user_info = $this->_exte_get_user_info($this->_user['user_id']);
			$phone = substr($_user_info['phone'], (stripos($_user_info['phone'], '-') + 1));
			
			if ($userphoneNo != $phone) {
				return response()->json([
					'msg'       => 'FAIL',
					'err'       => 'errphoneno',
					'col'       => 'userphoneNo',
				]);
			} else {
				return response()->json([
					'msg'       => 'SUC',
					'err'       => 'noerr',
					'col'       => 'nocol',
				]);
			}
		}
		
		public function updateVerifyInfo (Request $request)
		{
			$userphoneNo    = $request->userphoneNo;
			$useremail      = $request->useremail;
			$type           = $request->type;
			$_modules       = '86';
			
			$_tel           = $this->_exte_verify_phone ($_modules . '-' . $userphoneNo);
			$_eml           = $this->_exte_verify_email ($useremail);
			
			if ($type == 'phone') {
				if ($_tel) {
					return response()->json([
						'msg'        => 'FAIL',
						'_tel'       => 'userphoneNo'
					]);
				} else {
					return response()->json([
						'msg'        => 'SUC',
					]);
				}
			} else if ($type == 'email'){
				if ($_eml){
					return response()->json([
						'msg'        => 'FAIL',
						'_eml'       => 'useremail'
					]);
				} else {
					return response()->json([
						'msg'        => 'SUC',
					]);
				}
			}
		}
		
		public function cancelVerifyInfo (Request $request)
		{
			$userphoneNo    = $request->userphoneNo;
			$useremail      = $request->useremail;
			$userIdcardNo   = $request->userIdcardNo;
			
			$_info          = $this->_exte_get_user_info($this->_user['user_id']);
			if ($userphoneNo != substr($_info['phone'], (stripos($_info['phone'], '-') + 1))) {
				return response()->json([
					'msg'       => 'FAIL',
					'err'       => 'phoneErr',
					'col'       => 'userphoneNo',
				]);
			} else if ($useremail != $_info['email']) {
				return response()->json([
					'msg'       => 'FAIL',
					'err'       => 'emailErr',
					'col'       => 'useremail',
				]);
			} else if ($userIdcardNo != $_info['IDcard_no']) {
				return response()->json([
					'msg'       => 'FAIL',
					'err'       => 'IDcardnoErr',
					'col'       => 'IDcard_no',
				]);
			} else {
				return response()->json([
					'msg'       => 'SUC',
					'err'       => 'NOErr',
					'col'       => 'NOCOL',
				]);
			}
		}
		
		public function ajaxCancelAccount (Request $request)
		{
			$userphoneNo    = $request->userphoneNo;
			$useremail      = $request->useremail;
			$userIdcardNo   = $request->userIdcardNo;
			$password       = $request->password;
			$userverfcode   = $request->userverfcode;
			$_modules       = '86';
			$_table         = $this->_exte_get_table_obj($this->_user['user_id']);
			$_subUser       = DataList::where('parent_id', $this->_user['user_id'])->where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))->count();

			//先获得当前客户余额和持仓总量及检查是否有正在等待处理的出金申请订单
			$_bal = Mt4Users::select('LOGIN', 'NAME', 'BALANCE', 'CREDIT')->where('LOGIN', $this->_user['user_id'])->get()->toArray();
			$_vol = Mt4Trades::where('LOGIN', $this->_user['user_id'])->where('CLOSE_TIME', '1970-01-01 00:00:00')->whereIn('CMD', array(0, 1, 2, 3, 4, 5))->where('CONV_RATE1', '<>', 0)->count();
			$_isOrder = DrawRecordLog::where('user_id', $this->_user['user_id'])->whereIn('apply_status', array('0', '1'))->where('voided', '1')->first();


			if ($_bal[0]['BALANCE'] < 0) {
				//余额小于       0
				return response()->json([
					'msg'       => 'FAIL',
					'err'       => 'ERRBALANCE',
					'col'       => 'NOCOL',
				]);
			}
			
			if ($_vol > 0) {
				//持仓单
				return response()->json([
					'msg'       => 'FAIL',
					'err'       => 'ERRVOL',
					'col'       => 'NOCOL',
				]);
			}

			if ($_subUser > 0) {
				return response()->json([
						'msg'       => 'FAIL',
						'err'       => 'existSubUser',
						'col'       => 'userId',
				]);
			}

			if ($_isOrder != null) {
				//有正在等待处理的出金订单，不能销户
				return response()->json([
					'msg'       => 'FAIL',
					'err'       => 'UnfinishedOrder',
					'col'       => 'NOCOL',
				]);
			}

			if ($userverfcode != $request->session()->get('cancelCode')) {
				return response()->json([
					'msg'       => 'FAIL',
					'err'       => 'codeErr',
					'col'       => 'userverfcode',
				]);
			}
			
			//远程验证MT4密码
			$verify_psw = $this->_exte_mt4_verify_password($this->_user['user_id'], $password);
			if(!is_array($verify_psw)) {
				return response()->json([
					'msg'           => 'FAIL',
					'err'           => 'NETWORKFAIL', //网络故障
					'col'           => 'FATALCANOTCONNECT',
				]);
			}
			if (is_array($verify_psw) && $verify_psw['ret'] != '0') {
				return response()->json([
					'msg'           => 'FAIL',
					'err'			=> 'passwordErr',
					'col'			=> 'password',
				]);
			}
			
			//更新当前账户信息，enable_readonly = 1
			$param = array('login' => $this->_user['user_id'], 'enable_read_only' => 1);
			$mt4 = $this->_exte_mt4_update_user($param);
			if(!is_array($mt4)) {
				return response()->json([
					'msg'           => 'FAIL',
					'err'           => 'MT4SYNCUPDATAFAIL',
					'col'           => 'NOCOL',
				]);
			} else if (is_array($mt4) && $mt4['ret'] == '0') {
				$upd = $_table::where('user_id', $this->_user['user_id'])->where('voided', '1')->whereIn('user_status', array ('0', '1', '2', '4'))
					->update([
						'enable_readonly'       => 1, //同步MT4数据
						'is_out_money'          => '1', //不允许出金
						'rec_upd_date'          => date ('Y-m-d H:i:s'),
						'rec_upd_user'          => $this->_user['user_name'],
					]);
				
				$_rs = CancelApply::create([
					'cancel_userid'         => $this->_user['user_id'],
					'cancel_username'       => $this->_user['user_name'],
					'cancel_status'         => '0', // 默认0， 0 注销待审核， 1 注销成功，-1注销申请被拒绝
					'cancel_remark'         => '',
					'voided'                => '1',
					'rec_crt_date'          => date ('Y-m-d H:i:s'),
					'rec_upd_date'          => date ('Y-m-d H:i:s'),
					'rec_crt_user'          => $this->_user['user_name'],
					'rec_upd_user'          => $this->_user['user_name'],
				]);
				
				if ($upd && $_rs) {
					return response()->json([
						'msg'           => 'SUC',
						'err'			=> 'NOErr',
						'col'			=> 'NOCOL',
					]);
				} else {
					return response()->json([
						'msg'           => 'FAIL',
						'err'			=> 'cancelApplyErr',
						'col'			=> 'NOCOL',
					]);
				}
			}
		}
		
		public function updatePhoneEmailInfo (Request $request) {
			
			$userphoneNo    = $request->userphoneNo;
			$useremail      = $request->useremail;
			$updVerifyCode  = $request->updVerifyCode;
			$password       = $request->password;
			$type           = $request->type;
			$_modules       = '86';
			$table          = $this->_exte_get_table_obj($this->_user['user_id']);
			
			if ($updVerifyCode != $request->session ()->get('updverifyCode')) {
				return response()->json([
					'msg'           => 'FAIL',
					'err'           => 'codeErr',
					'col'           => 'verfyCode',
				]);
			}
			
			if ($type == 'phone' && ($userphoneNo != $request->session ()->get('updverifyphoneNo'))) {
					return response()->json([
						'msg'           => 'FAIL',
						'err'           => 'phoneErr',
						'col'           => 'userphoneNo',
					]);
			}
			
			if ($type == 'email' && ($useremail != $request->session ()->get('updverifyEmail'))) {
				return response()->json([
					'msg'           => 'FAIL',
					'err'           => 'emailErr',
					'col'           => 'useremail',
				]);
			}
			
			//远程验证MT4密码
			$verify_psw = $this->_exte_mt4_verify_password($this->_user['user_id'], $password);
			
			if(!is_array($verify_psw)) {
				return response()->json([
					'msg'           => 'FAIL',
					'err'           => 'NETWORKFAIL', //网络故障
					'col'           => 'FATALCANOTCONNECT',
				]);
			} else if (is_array($verify_psw) && $verify_psw['ret'] != '0') {
				return response()->json([
					'msg'           => 'FAIL',
					'err'			=> 'pswErr',
					'col'			=> 'password',
				]);
			} else {
				//mt4密码验证成功，开始同步MT4数据
				/* $data = array(
					'login'         => $this->_user['user_id'],
				);
				
				if ($type == 'phone') {
					$data['phone'] = '86-' . $userphoneNo;
				} else if ($type == 'email') {
					$data['email'] = $useremail;
				}
				
				$rs = $this->_exte_mt4_update_user($data);
				
				if (is_array($rs) && $rs['ret'] == '0') {*/
					if ($type == 'phone') {
						$num = $table::where('user_id', $this->_user['user_id'])->where('voided', '1')
							->whereIn('user_status', array('0', '1', '2', '4'))
							->update([
								'phone'         => $_modules . '-' . $userphoneNo,
								'rec_upd_date'  => date('Y-m-d H:i:s'),
								'rec_upd_user'  => $this->_user['user_name'],
							]);
					} else if ($type == 'email') {
						$num = $table::where('user_id', $this->_user['user_id'])->where('voided', '1')
							->whereIn('user_status', array('0', '1', '2', '4'))
							->update([
								'email'         => $useremail,
								'rec_upd_date'  => date('Y-m-d H:i:s'),
								'rec_upd_user'  => $this->_user['user_name'],
							]);
					}
					
					if ($num) {
						return response()->json([
							'msg'           => 'SUC',
							'err'           => 'SUC',
							'col'           => 'SUC',
						]);
					} else {
						return response()->json([
							'msg'           => 'FAIL',
							'err'			=> 'UPDATEFAIL',
							'col'			=> 'NOTCOL',
						]);
				}
			}
		}
		
		public function updVerifyPassSendCode (Request $request)
		{
			$userphoneNo    = $request->userphoneNo;
			$useremail      = $request->useremail;
			$type           = $request->type;
			$code           = rand(123456, 999999);
			
			$request->session ()->forget('updverifyType');
			$request->session ()->forget('updverifyCode');
			$request->session ()->forget('updverifyphoneNo');
			$request->session ()->forget('updverifyEmail');
			
			if ($type == 'phone') {
				$_rs = $this->_exte_send_phone_notify ($userphoneNo, 'modifyPhone', array('code' => $code));
				
				if ($_rs) {
					$request->session ()->put ('updverifyType', $type);
					$request->session ()->put ('updverifyCode', $code);
					$request->session ()->put ('updverifyphoneNo', $userphoneNo);
				}
				
				return response()->json(['status' => $_rs]);
			} else if ($type == 'email') {
				$_rs = $this->_exte_send_email_notify ($useremail, '更改邮件验证码', $code, 'registerCode', $type);
				if ($_rs) {
					$request->session ()->put ('updverifyType', $type);
					$request->session ()->put ('updverifyCode', $code);
					$request->session ()->put ('updverifyEmail', $useremail);
				}
				
				return response()->json(['status' => $_rs]);
			}
		}
		
		public function changeBankCardSendCode (Request $request)
		{
			$userphoneNo        = $request->userphoneNo;
			$type               = $request->type;
			$code               = rand(123456, 999999);
			
			$request->session ()->forget('changeCode');
			$request->session ()->forget('changePhoneNo');
			
			$_rs = $this->_exte_send_phone_notify ($userphoneNo, 'changeBankCard', array('code' => $code));
			
			if ($_rs) {
				$request->session ()->put ('changeCode', $code);
				$request->session ()->put ('changePhoneNo', $userphoneNo);
			}
			
			return response()->json(['status' => $_rs]);
		}
		
		public function cancelVerifyPassSendCode (Request $request)
		{
			$userphoneNo    = $request->userphoneNo;
			$useremail      = $request->useremail;
			$userIdcardNo   = $request->userIdcardNo;
			$password       = $request->password;
			$userverfcode   = $request->userverfcode;
			$code           = rand(123456, 999999);
			
			$request->session ()->forget('cancelCode');
			$_rs = $this->_exte_send_phone_notify ($userphoneNo, 'cancellAccount', array('code' => $code));
			if ($_rs) {
				$request->session()->put('cancelCode', $code);
				$request->session()->put('cancelverifyphoneNo', $userphoneNo);
			}
			
			return response()->json(['status' => $_rs]);
		}
		
		public function relationShip(Request $request)
		{
			$uid = $request->userId;
			$_info = $this->_exte_show_account_relationship_chain($uid, ' -> ', 'idname', $request->role);
			
			return response ()->json (['real' => $_info]);
		}
		
		public function relationShipHtml(Request $request) {
			$uid = $request->userId;
			$_info = $this->_exte_get_mylocal_html($uid, $request->fname, '->', $request->role);
			
			return response ()->json (['real' => $_info]);
		}
	}