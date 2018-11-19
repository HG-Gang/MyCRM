<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018-05-02
	 * Time: 下午 3:16
	 */
	
	namespace App\Http\Controllers\CommonController;
	
	use App\Model\Mt4Users;
	
	abstract class Abstract_Mtservice_Controller extends Abstract_Service_Controller
	{
		protected $_mt4_host_real			= '';
		
		protected $_mt4_host_demo           = '';
		
		protected $_mt4_company_code        = '';
		
		protected $_mt4_url                 = '';
		
		protected $_mt4_token               = '';
		
		protected $_mt4_crt_act             = '';
		
		protected $_mt4_upd_act             = '';
		
		protected $_mt4_del_act             = '';
		
		protected $_mt4_active_act          = '';
		
		protected $_mt4_reset_pwd           = '';
		
		protected $_mt4_verify_pwd          = '';
		
		protected $_mt4_upd_crdt            = '';
		
		protected $_mt4_get_act             = '';
		
		protected $_mt4_deposit_amt         = '';
		
		protected $_mt4_withdrawal_amt      = '';
		
		protected $_mt4_transfer_amt        = '';
		
		protected $_mt4_get_grp             = '';
		
		protected function _exte_mt4_host_real()
		{
			return $this->_mt4_host_real = 'REAL';
		}
		
		protected function _exte_mt4_host_demo()
		{
			return $this->_mt4_host_demo = 'DEMO';
		}
		
		protected function _exte_mt4_company_code()
		{
			//env('COMPANY_CODE')
			return $this->_mt4_company_code = 'manley';
		}
		
		protected function _exte_mt4_url()
		{
			return $this->_mt4_url = 'http://mt2.api.fxdns.net/mt4/query';
		}
		
		protected function _exte_mt4_token()
		{
			return $this->_mt4_token = 'Tcy2RRuQjTQikqb8BoOiUZfZ4mQf4PkW';
		}
		
		protected function _exte_mt4_crt_act()
		{
			return $this->_mt4_crt_act = 'account_add';
		}
		
		protected function _exte_mt4_upd_act()
		{
			return $this->_mt4_upd_act = 'account_update';
		}
		
		protected function _exte_mt4_del_act()
		{
			return $this->_mt4_del_act = 'account_delete';
		}
		
		protected function _exte_mt4_active_act()
		{
			return $this->_mt4_active_act = 'account_active';
		}
		
		protected function _exte_mt4_reset_pwd()
		{
			return $this->_mt4_reset_pwd = 'account_resetpwd';
		}
		
		protected function _exte_mt4_verify_pwd()
		{
			return $this->_mt4_verify_pwd = 'account_checkpwd';
		}
		
		protected function _exte_mt4_upd_crdt()
		{
			return $this->_mt4_upd_crdt = 'account_updatecredit';
		}
		
		protected function _exte_mt4_get_act()
		{
			return $this->_mt4_upd_crdt = 'account_get';
		}
		
		protected function _exte_mt4_deposit_amt()
		{
			return $this->_mt4_deposit_amt = 'deposit_add';
		}
		
		protected function _exte_mt4_withdrawal_amt()
		{
			return $this->_mt4_withdrawal_amt = 'deposit_minus';
		}
		
		protected function _exte_mt4_transfer_amt()
		{
			return $this->_mt4_transfer_amt = 'deposit_transfer';
		}
		
		protected function _exte_mt4_get_grp()
		{
			return $this->_mt4_get_grp = 'group_get';
		}
		
		protected function _exte_mt4_sprintf_url() {
			return sprintf("%s?token=%s", $this->_exte_mt4_url(), $this->_exte_mt4_token());
		}
		
		protected function _exte_mt4_main_param($opt, $param)
		{
			$arg = array(
				'company'                   => $this->_exte_mt4_company_code(),
				'params'                    => $param,
				'cmd'                       => $opt,
				'demo'                      => $this->_exte_mt4_host_demo()
			);
			
			return $this->_exte_mt4_query($arg);
		}
		
		protected function _exte_mt4_query($param,$post_file = false)
		{
			$oCurl = curl_init();
			
			if (stripos($this->_exte_mt4_sprintf_url(), "https://") !== FALSE) {
				curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
				curl_setopt($oCurl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.131 Safari/537.36');
			}
			
			if (is_string($param) || $post_file) {
				$strPOST = $param;
			} else {
				$strPOST = http_build_query($param);
			}
			
			curl_setopt($oCurl, CURLOPT_URL, $this->_exte_mt4_sprintf_url());
			curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($oCurl, CURLOPT_POST, true);
			curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
			curl_setopt($oCurl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.131 Safari/537.36');
			
			$sContent = curl_exec($oCurl);
			$aStatus = curl_getinfo($oCurl);
			curl_close($oCurl);
			
			/*$sContent = {"ret_code":0,"ret_msg":"ClientsAddUser invalid UserCountry type"}
				1:成功，0:失败
			*/
			if (intval($aStatus["http_code"]) == 200) {
				return json_decode($sContent, true);
				/*if(json_decode($sContent)){
					return json_decode($sContent, true);
				}else{
					return $sContent;
				}*/
			} else {
				
				//$this->fail(
				//	sprintf('Api Code %s ',$aStatus['http_code'])
				//);
				
				return sprintf('Error:Api Code %s ',$aStatus['http_code']);
			}
		}
		
		//该类一般用于子类重写，mt4不同操作需要的不同列
		protected function _exte_mt4_create_field_fill($param)
		{
			$data = array(
				'UserID'                => $param['user_id'],
				'User'                  => $param['user_name'],
				'UserGroupName'         => 'ML-DEMO',//$param['mt4_grp'],
				'UserPwd'               => base64_decode($param['password']),
				'UserInvestorpwd'       => strrev(base64_decode($param['password'])),
				'UserPhonepwd'          => strrev(base64_decode($param['password'])),
				'UserCountry'           => $param['country'],
				'UserCity'              => 'China',
				'UserState'             => 'guangdong',//$param->enable,
				'UserZipcode'           => $param['parent_id'],
				'UserAddress'           => 'China',
				'UserPhone'             => $param['phone'],
				'UserEmail'             => $param['email'],
				'UserStatus'            => '',
				'UserAgentAccount'      => $param['parent_id'],
				'UserLeverage'          => $param['cust_lvg'],
				'UserSendreports'       => 0,
				'UserComment'           => 'open by pccrm',
				'UserDeposit'           => 0,
				'UserIRD'               => $param['email'],
			);
			
			return $data;
		}
		
		protected function _exte_mt4_update_field_fill($param)
		{
			$data = array(
				'UserLoginID'               => $param['user_id'],
				'User'                      => $param['user_name'],
				'UserGroupName'             => $param['mt4_grp'],
				'UserPwd'                   => $param['password'],
				'UserInvestorpwd'           => strrev($param['password']),
				'UserPhonepwd'              => strrev($param['password']),
				'UserCountry'               => $param['country'],
				'UserCity'                  => $param['city'],
				'UserState'                 => $param['enable'],
				'UserZipcode'               => $param['parent_id'],
				'UserAddress'               => $param['address'],
				'UserPhone'                 => $param['phone'],
				'UserEmail'                 => $param['email'],
				'UserStatus'                => '',
				'UserAgentAccount'          => $param['parent_id'],
				'UserLeverage'              => $param['group_id'],
				'UserSendreports'           => 0,
				'UserComment'               => 'open by pccrm',
				'UserEnableChangePwd'       => 1,
				'UserEnableReadonly'        => 1, //只读，1 勾上，0 没有勾上
				'UserIRD'                   => $param['IDcard_no'],
			);
			
			return $data;
		}
		
		//开户
		protected function _exte_mt4_create_account($param, $debug = false)
		{
			if($debug){
				dd($param);
				exit('debug@25');
			}
			
			return $this->_exte_mt4_main_param($this->_exte_mt4_crt_act(), $this->_exte_mt4_create_field_fill($param));
		}
		
		//修改账号
		protected function _exte_mt4_update_account($param, $fill = true)
		{
			if ($fill) {
				$param = $this->_exte_mt4_update_field_fill($param);
			}
			
			return $this->_exte_mt4_main_param($this->_exte_mt4_upd_act(), $param);
		}
		
		//删除账号
		protected function _exte_mt4_del_user($uid)
		{
			$data = array('UserLoginID' => $uid);
			
			return $this->_exte_mt4_main_param($this->_exte_mt4_del_act(), $data);
		}
		
		//禁用账号
		protected function _exte_mt4_active_disable_user($uid)
		{
			$data = array(
				'UserLoginID'               => $uid,
				'UserEnable'                => 0, //不启用，没勾上
			);
			
			return $this->_exte_mt4_main_param($this->_exte_mt4_active_act(), $data);
		}
		
		//启用账号
		protected function _exte_mt4_active_enable_user($uid)
		{
			$data = array(
				'UserLoginID'               => $uid,
				'UserEnable'                => 1, //启用，勾上，default
			);
			
			return $this->_exte_mt4_main_param($this->_exte_mt4_active_act(), $data);
		}
		
		//修改主密码, 修改投资人密码 $investor = 1
		protected function _exte_mt4_reset_user_pwd($uid, $pwd, $investor = 0)
		{
			$data = array(
				'UserLoginID'               => $uid,
				'UserPwd'                   => $pwd,
				'UserInvestPwdCheck'        => $investor
			);
			
			return $this->_exte_mt4_main_param($this->_exte_mt4_reset_pwd(), $data);
		}
		
		//修改信用额度
		/*array:2 [▼
			"ret_code" => 1
			"ret_msg" => "O.K."
		]
		array:2 [▼
			"ret_code" => 0
			"ret_msg" => "ClientsCreditUpdate invalid UserCredit type"
		]
		 * */
		protected function _exte_mt4_update_credit($uid, $crdt, $expiretime = false)
		{
			$data = array(
				'UserLoginID'               => $uid,
				'UserCredit'                => $crdt,
				'UserCreditExpiration'      => $expiretime,
				'UserComment'               => $uid . self::XY,
			);
			
			return $this->_exte_mt4_main_param($this->_exte_mt4_upd_crdt(), $data);
		}
		
		//获取用户信息
		/*array:2 [▼
			"ret_code" => 1
			"ret_msg" => array:35 [▼
			"User" => "测试接口"
			"UserAddress" => "China"
			"UserAgentAccount" => 1001
			"UserBalance" => 0
			"UserCity" => "China1096-1001-1096"
			"UserColor" => -16777216
			"UserComment" => "open by pccrm"
			"UserCountry" => "1001-1096-1001-1096"
			"UserCredit" => 0
			"UserEmail" => "1165142535@qq.com"
			"UserEnableChangepwd" => 1
			"UserEnableReadonly" => 0
			"UserEnableStatus" => 1
			"UserEquit" => 0
			"UserFreeMargin" => 0
			"UserGroupName" => "ML-DEMO"
			"UserInterestrate" => 0
			"UserLastconnectdate" => 1525753839
			"UserLastconnectip" => 0
			"UserLeverage" => 10
			"UserLoginID" => 1096
			"UserMargin" => 0
			"UserMarginLever" => 0
			"UserMarginState" => 0
			"UserPhone" => "86-15935671364"
			"UserPredaybalance" => 0
			"UserPredayequitye" => 0
			"UserPremonthbalance" => 0
			"UserPrevmonthequitye" => 0
			"UserRegdate" => 1525753839
			"UserSendreports" => 0
			"UserState" => "guangdong"
			"UserStatus" => "guangdong"
			"UserTaxes" => 0
			"UserZipcode" => "1001"
			]
		]*/
		protected function _exte_mt4_get_user_info($uid)
		{
			$data = array(
				'UserLoginID'               => $uid,
			);
			
			return $this->_exte_mt4_main_param($this->_exte_mt4_get_act(), $data);
		}
		
		//入金
		/*array:2 [▼
			"ret_code" => 1
			"ret_msg" => "O.K."
		]*/
		protected function _exte_mt4_deposit_amount($uid, $amount, $cmt)
		{
			$data = array(
				'UserLoginID'               => $uid,
				'UserDeposit'               => $amount,
				'UserComment'               => $cmt,
			);
			
			return $this->_exte_mt4_main_param($this->_exte_mt4_deposit_amt(), $data);
		}
		
		//出金
		/*array:2 [▼
			"ret_code" => 1
			"ret_msg" => "O.K."
		]*/
		protected function _exte_mt4_withdrawal_amount($uid, $amount, $cmt)
		{
			$data = array(
				'UserLoginID'               => $uid,
				'UserDeposit'               => $amount,
				'UserComment'               => $cmt,
			);
			
			return $this->_exte_mt4_main_param($this->_exte_mt4_withdrawal_amt(), $data);
		}
		
		//转账
		/*array:2 [▼
			"ret_code" => 0
			"ret_msg" => "ClientsTransferBalance not Users not in the same Company err info: from group:demoforex to group:ML-DEMO"
		]
		array:2 [▼
			"ret_code" => 1
			"ret_msg" => "O.K."
		]*/
		protected function _exte_mt4_transfer_amount($uid, $toUid, $amount)
		{
			$data = array(
				'UserLoginIDFrom'           => $uid,
				'UserLoginIDTo'             => $toUid,
				'UserDeposit'               => $amount,
				'UserComment'               => $uid .'-' . $toUid . self::ZH,
			);
			return $this->_exte_mt4_main_param($this->_exte_mt4_transfer_amt(), $data);
		}
		
		//获取用户组
		protected function _exte_mt4_get_user_group()
		{
			$data = array(
				'Searchindex'               => 1,
				'SearchVolumn'              => 10
			);
			return $this->_exte_mt4_main_param($this->_exte_mt4_get_grp(), $data);
		}
		
		//验证密码
		/**
		 * @param $login
		 * @param $pwd
		 * @param int $isInvestPwd
		 * 0 验证主密码 1验证投资人密码
		 */
		protected function _exte_mt4_verify_password($uid, $psw, $isInvestPwd = 0)
		{
			$data = array(
				'UserLoginID'               => $uid,
				'UserPwd'                   => $psw,
				'UserInvestPwdCheck'        => $isInvestPwd,
			);
			
			return $this->_exte_mt4_main_param($this->_exte_mt4_verify_pwd(), $data);
		}
		
		//通过API更新本地表数据，达到和mt4_users,agents,user 三个表的同一个用户账户资金一样且是最新的
		protected function _exte_mt4_update_local_user_info($user_id)
		{
			$_upd_info = $this->_exte_mt4_get_user_info($user_id);
			
			$_table = $this->_exte_get_table_obj ($user_id);
			
			$num = $_table::where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))
				->find($_upd_info['ret_msg']['UserLoginID'])
				->update([
					'user_money'            => $_upd_info['ret_msg']['UserBalance'], //余额
					'effective_cdt'         => $_upd_info['ret_msg']['UserCredit'], //信用额
					'cust_lvg'              => $_upd_info['ret_msg']['UserLeverage'], //杠杆
					'cust_eqy'              => $_upd_info['ret_msg']['UserEquit'], //净值 = 账户余额 - 所有订单的获利与亏损，保证金
					//'cust_vol'              => $_upd_info['ret_msg']['vol'], //持仓总量/手数 TODO 未确定列
					'mt4_grp'               => $_upd_info['ret_msg']['UserGroupName'], //客户组
					'used_bond_money'       => $_upd_info['ret_msg']['UserMargin'], //已用保证金 == 已用预付款
					'available_bond_money'  => $_upd_info['ret_msg']['UserFreeMargin'], //可用保证金 == 可用余额
					'rec_upd_date'          => date('Y-m-d H:i:s'),
				]);
			
			//返回受影响的行数
			return $num;
		}
		
		//批量更新用户信息
		protected function _exte_mt4_batch_update_user_info($data, $_table)
		{
			$upd_num = array ();
			
			if(!empty($data)) {
				for($i = 0; $i < count($data); $i++) {
					$mt4_user[$i] = Mt4Users::select('LOGIN', 'LEVERAGE', 'BALANCE', 'EQUITY', 'CREDIT', 'MARGIN', 'MARGIN_FREE')->where('LOGIN', $data[$i]['user_id'])->first();
					if($mt4_user[$i] != null) {
						$upd_num[$i] = $_table::whereIn('voided', array ('1', '2'))->whereIn('user_status', array('0', '1', '2', '4'))
							->find($mt4_user[$i]['LOGIN'])
							->update([
								'cust_lvg'              => $mt4_user[$i]['LEVERAGE'],//杠杆水平
								'user_money'            => $mt4_user[$i]['BALANCE'], //帐户余额
								'cust_eqy'              => $mt4_user[$i]['EQUITY'], //帐户余额 = 净值
								'effective_cdt'         => $mt4_user[$i]['CREDIT'], //信用
								'bond_money'            => $mt4_user[$i]['MARGIN'], //保证金
								'available_bond_money'  => $mt4_user[$i]['MARGIN_FREE'], //可用保证金
								'rec_upd_date'          => date('Y-m-d H:i:s'),
							]);
					}
				}
			}
			
			return $upd_num;
		}
		
		//TODO 更新用户银行卡同步到MT4
	}