<?php
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/2/6
	 * Time: 11:22
	 */
	
	namespace App\Http\Controllers\User;
	
	use App\Model\User;
	use App\Model\Agents;
	use App\Model\AgentsGroup;
	use Illuminate\Http\Request;
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	
	class RegisterController extends Abstract_Mt4service_Controller
	{
		public function index() {
			return view ('user.register.register');
		}
		
		public function registerVerifyInfo(Request $request)
		{
			$_as ='';
			$_rs = array ();
			
			$_userIdcardNo          = $request->userIdcardNo;
			$_userphoneNo           = $request->userphoneNo;
			$_useremail             = $request->useremail;
			$_userInviterId         = $request->userInviterId;
			$_modules               = $request->modules;
			$_verifyType            = $request->verifyType;
			$_is_invite             = $request->is_invite;
			
			/*vierify*/
			$_ido                   = $this->_exte_verify_idno ($_userIdcardNo);
			$_tel                   = $this->_exte_verify_phone ($_modules . '-' . $_userphoneNo);
			$_eml                   = $this->_exte_verify_email ($_useremail);
			
			if ($_is_invite == "FALSE") {
				if (strpos($_userInviterId, 'A')) {
					$_userInviterId = substr($_userInviterId, 0, -1);
				}
				
				$_iId = $this->_exte_verify_intiveId ($_userInviterId);
			} else {
				$_iId = array ();
			}
			
			
			$_rs  = ($_ido) ? array ('_ido' => 'userIdcardNo') : array ();
			$_rs += ($_tel) ? array ('_tel' => 'userphoneNo') : array ();
			$_rs += ($_eml) ? array ('_eml' => 'useremail') : array ();
			$_rs += ($_iId) ? array ('_iId' => 'userInviterId') : array ();
			$_rs += !(empty($_rs)) ? array('status' => 'FAIL') : array ('status' => 'SUC');
			
			return response()->json($_rs);
		}
		
		public function registerSendCode(Request $request)
		{
			
			$_rs = false;
			
			$_userIdcardNo          = $request->userIdcardNo;
			$_userphoneNo           = $request->userphoneNo;
			$_useremail             = $request->useremail;
			$_userInviterId         = $request->userInviterId;
			$_modules               = $request->modules;
			$_verifyType            = $request->verifyType;
			$_is_invite             = $request->is_invite;
			$code                   = rand(123456, 999999);
			$request->session ()->flush ();
			if ($_verifyType == 'verifyphone') {
				$_rs = $this->_exte_send_phone_notify ($_userphoneNo, 'registerCode', array('code' => $code));
				if ($_rs) {
					$request->session ()->put ('verifyType', $_verifyType);
					$request->session ()->put ('verifyCode', $code);
					$request->session ()->put ('verifyphoneNo', $_userphoneNo);
				}
				
				return response()->json(['status' => $_rs]);
			} else if ($_verifyType == 'verifyemail') {
				$_rs = $this->_exte_send_email_notify ($_useremail, '注册验证码', $code, 'registerCode', $_verifyType);
				if ($_rs) {
					$request->session ()->put ('verifyType', $_verifyType);
					$request->session ()->put ('verifyCode', $code);
					$request->session ()->put ('verifyEmail', $_useremail);
				}
				
				return response()->json(['status' => $_rs]);
			}
		}
		
		public function registerinto (Request $request)
		{
			$_username              = $request->username;
			$_sex                   = $request->sex;
			$_userIdcardNo          = $request->userIdcardNo;
			$_modules               = $request->modules;
			$_userphoneNo           = $request->userphoneNo;
			$_useremail             = $request->useremail;
			$_userInviterId         = $request->userInviterId;
			$_parent_id             = $request->parent_id;
			$_parent_grpId          = $request->parent_grpId;
			$_register_type         = $request->register_type;
			$_comm_type             = $request->comm_type;
			$_parent_type           = $request->parent_type;
			$_is_invite             = $request->is_invite;
			$_verifyType            = $request->verifyType;
			$_userverfcode          = $request->userverfcode;
			$_password              = $request->password;
			$_againpassword         = $request->password;
			$_agreeRule             = $request->agreeRule;
			
			$_sverifyType           = $request->session ()->get ('verifyType');
			$_sverifyCode           = $request->session ()->get ('verifyCode');
			$_sverifyphoneNo        = $request->session ()->get ('verifyphoneNo');
			$_sverifyEmail          = $request->session ()->get ('verifyEmail');
			
			if ($_is_invite == 'FALSE') {
				$_table = new User(); $_pgrpId = 5;
				$_prop = 0; $_is_confirm_agents_lvg = '1';
				$_parent_id = $_userInviterId;
				$data['settlement_model'] = '';
				if((int)$_parent_id >= $this->_userIdIndex) {
					$_data = User::select('user_id', 'mt4_grp', 'comm_prop', 'trans_mode', 'group_id', 'is_confirm_agents_lvg', 'parent_id')->where('user_id', $_parent_id)->where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))->first();
					return response()->json([
						'status'		=> 'FAIL',
						'errorType'		=> 'TipsParentId', //该介绍人的上级代理是$_data['parent_id']
						'col'			=> $_data['parent_id'],
					]);
				}
				
				$_data = Agents::select('user_id', 'mt4_grp', 'comm_prop', 'trans_mode', 'group_id', 'is_confirm_agents_lvg', 'parent_id')->where('user_id', $_parent_id)->where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))->first();
				if ($_data == null) {
					return response()->json([
						'status'		=> 'FAIL',
						'errorType'		=> 'ErrorParentId',
						'col'			=> 'parent_id'
					]);
				} else if ((int)$_data->comm_prop <= 50 && strpos($_parent_type, 'A')) {
					return response()->json([
						'status'		=> 'FAIL',
						'errorType'		=> 'ErrorCommProp',
						'col'			=> $_data->comm_prop,
					]);
				} else if ($_data->is_confirm_agents_lvg == '0') {
					return response()->json([
						'status'		=> 'FAIL',
						'errorType'		=> 'ErrorParentIdNoConfirm',
						'col'			=> 'parent_id'
					]);
				}
				
				if(strpos($_data->mt4_grp, 'A')) {
					$strlen       = strpos($_data->mt4_grp, 'A');
					$_parent_type = substr($_data->mt4_grp, 0, $strlen - 1) . '-A';
					//$_parent_type = 'B' . substr($_data->mt4_grp, 1); //官网自己进来注册的用户 默认  就是输入的上级ID的组别
				} else {
					$_parent_type   = $_data->mt4_grp;
					//$strlen       = strpos($_data->mt4_grp, 'B');
					//$_parent_type = substr($_data->mt4_grp, 0, $strlen - 1) . '-B';
					//$_parent_type = 'A' . substr($_data->mt4_grp, 1); //官网自己进来注册的用户 默认  就是输入的上级ID的组别
				}
			} else {
				$_data = Agents::select('user_id', 'mt4_grp', 'comm_prop', 'trans_mode', 'group_id', 'is_confirm_agents_lvg', 'settlement_model', 'rights')->where('user_id', $_parent_id)->where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))->first();
				$commProp = AgentsGroup::select('group_id', 'agents_comm_prop', 'group_name')->whereIn('group_id', array(1, 2, 3, 4))->where('voided', '1')->get()->toArray();
				$_parent_type = $_comm_type;
				$data['settlement_model'] = $_data['settlement_model'];
				//是被邀请注册的
				if($_register_type == 'user') {
					$_table = new User();
					$_pgrpId = 5; //默认无佣金用户
					$_is_confirm_agents_lvg = '1';
					$_prop = 0;
				} else if($_register_type == 'agents') {
					$_table = new Agents();
					$_pgrpId = $_parent_grpId + 1; //默认
					$_is_confirm_agents_lvg = '0';
					
					$gid = ($_data['group_id'] == 0) ? $_data['group_id'] + 1 : $_data['group_id'];
					if ((int)$_data->comm_prop > $commProp[$gid]['agents_comm_prop']) {
						$_prop = $commProp[$gid]['agents_comm_prop'];
					} else if ((int)$_data->comm_prop == $commProp[$gid]['agents_comm_prop']) {
						$_prop = $commProp[$gid]['agents_comm_prop'];
					} else if ((int)$_data->comm_prop < $commProp[$gid]['agents_comm_prop']) {
						$_prop = $_data->comm_prop;
					}
					
					//如果当前上级代理商的代理模式为权益模式则计算下级代理商的权益比例值
					if ($_data['trans_mode'] == '1') {
						//当前上级代理商是权益模式，开始计算下级代理商权益比例
						if ($_data['rights'] >= $commProp[$gid]['agents_comm_prop']) {
							$rights = $commProp[$gid]['agents_comm_prop'];
						} else if ($_data['rights'] < $commProp[$gid]['agents_comm_prop']) {
							$rights = $_data['rights'];
						}
					}
				}
			}
			
			//验证发送验证码的的类型
			if($_userverfcode != $_sverifyCode) {
				return response()->json([
					'status'		=> 'FAIL',
					'errorType'		=> 'ErrorverifCode',
					'col'			=> 'userverfcode'
				]);
			}
			
			if($_useremail != $_sverifyEmail && $_sverifyType == 'verifyemail') {
				return response()->json([
					'status'		=> 'FAIL',
					'errorType'		=> 'ErrorEmail',
					'col'			=> 'email'
				]);
			}
			if($_userphoneNo != $_sverifyphoneNo && $_sverifyType == 'verifyphone') {
				return response()->json([
					'status'		=> 'FAIL',
					'errorType'		=> 'ErrorphoneNo',
					'col'			=> 'phone'
				]);
			}
			
			$_rs = $_table->create([
				'user_name'             => $_username,
				'password'              => base64_encode($_password),
				'sex'                   => $_sex,
				'phone'                 => $_modules . '-' . $_userphoneNo,
				'IDcard_no'			    => $_userIdcardNo,
				'email'				    => $_useremail,
				'group_id'              => $_pgrpId,
				'parent_id'             => $_parent_id, // 查找URL的编号的user_id, 0 属于平台的用户
				'user_money'		    => '0',
				'cust_eqy'              => '0',
				'effective_cdt'         => '0',
				'comm_prop'			    => $_prop,
				'mt4_grp'			    => $_parent_type,
				'trans_mode'            => $_data->trans_mode, // 交易模式，0 佣金模式，1 权益模式
				//'bond_money'          => '0/1', // 保证金金额
				'IDcard_status'		    => '0', // 默认 0 没通过审核，1 通过审核，2 正在审核中
				'user_status'           => '0', //0 未认证，1 已认证，-1 禁用
				'is_confirm_agents_lvg' => $_is_confirm_agents_lvg,
				'enable_readonly'       => '0', //默认， 能登录能交易(0=未勾上)，能登录 不能交易(1 = 只读 勾上)
				'is_out_money'          => '0', //default (0) 允许出金	1 不允许
				'enable'                => '1', //默认启用(能登录能交易, 1 = 勾上)， 不能登录(0 = 未勾上)
				'bank_status'           => '0',
				'IDcard_status'			=> '0',
				'cust_lvg'              => 100,
				'rights'                => ($_is_invite != 'FALSE' && $_data['trans_mode'] == '1' && $_register_type == 'agents') ? $rights : 0 , //权益比例
				'cycle'                 => ($_data->trans_mode == '1') ? 1 : 0,//结算周期
				'voided'                => '1', //注册后允许登录
				'settlement_model'      => $data['settlement_model'], //代理商结算模式，1 线上, 2 线下
				'rec_crt_date'          => date('Y-m-d H:i:s'),
				'rec_upd_date'          => date('Y-m-d H:i:s'),
				'rec_crt_user'          => $_username,
				'rec_upd_user'          => $_username,
			]);
			
			if(is_object($_rs) && isset($_rs->user_id)) {
				//$_register_type = agents or user or admin
				$_str_rala = $this->_exte_show_account_relationship_chain($_rs->user_id, '-', 'id', 'admin'); //用户关系链
				$num = $_table::find($_rs->user_id)->update(['mt4_code' => $_rs->user_id, 'country' => $_str_rala, 'rec_upd_date' => date('Y-m-d H:i:s')]);
				$data = $_table::where('user_id', $_rs->user_id)->first();
				$mt4_grpId = $this->_exte_get_mt4_grpId($data['mt4_grp']);
				$data['mt4_grpId'] = $mt4_grpId[0]['user_group_name'];
				$mt4 = $this->_exte_sync_mt4_reigster2($data);
				
				if(is_array($mt4) && $mt4['0'] == 'OK') {
					//$send_type = ($_sverifyType == 'verifyphone') ? 'registerSucInfo' : 'registerSucInfo2';
					if ($_sverifyType == 'verifyphone') {
						//手机接收的验证码，发送账号密码到手机
						$_phone = $this->_exte_send_phone_notify($_userphoneNo, 'registerSucInfo', array ('user_id' => $data->user_id, 'password' => base64_decode ($data->password)));
					} else if ($_sverifyType == 'verifyemail') {
						//邮箱接收的验证码，发送账号密码到邮箱
						$_email = $this->_exte_send_email_notify($_useremail, '注册成功', $data, 'registerSuc', 'verifyemail');
					}
					$request->session()->flush();
					$request->session()->put('suser', $data);
					return response()->json([
						'status'		=> 'SUCCESS',
						'errorType'     => 'NULL',
						'col'           => array('Uid' => $data->user_id, 'psw' => base64_decode ($data->password)),
					]);
				} else {
					//MT4账户注册同步失败
					//TODO 注册失败时，记录此用户为 注册MT4失败，更新该列 voided = 2，后台以后再做一个模块查看这些信息
					$num = $_table::find($data['user_id'])->update(['voided' => '2', 'rec_upd_user' => $data['user_name'], 'rec_upd_date' => date('Y-m-d H:i:s')]);
					return response()->json([ //$_mt4['err'] != '0'
						'status'        => 'FAIL',
						'errorType'     => 'ErrorMT4Async',
						'col'           => array('Uid' => $data->user_id, 'psw' => base64_decode ($data->password)),
					]);
				}
			}
		}
	}