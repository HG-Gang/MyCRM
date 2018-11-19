<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018-05-16
	 * Time: 下午 5:55
	 */
	
	namespace App\Http\Controllers\CommonController;
	
	use App\Model\Mt4Users;
	
	abstract class Abstract_Mt4service_Controller extends Abstract_Basic_Controller
	{
		protected $_mt4_host                = '';
		
		protected $_mt4_port                = '';
		
		protected $_mt4_timeout             = '';
		
		//其他操作
		/*=======================start=========================*/
		protected $_mt4_url                 = '';
		
		protected $_mt4_crt_user            = '';
		
		protected $_mt4_upd_user            = '';
		
		protected $_mt4_balance             = '';
		
		protected $_mt4_user_info           = '';
		
		protected $_getgroup                = '';
		
		protected $_mt4_deposit_amt         = '';
		
		protected $_mt4_withdrawal_amt      = '';
		
		protected $_mt4_upd_crdt_in         = '';
		
		protected $_mt4_upd_crdt_out        = '';
		
		protected $_mt4_verify_pwd          = '';
		
		/*=======================end=========================*/
		
		protected function _exte_mt4_host()
		{
			//测试 http://120.79.221.105/, st.titanera.com, 47.75.157.81, 47.91.219.55, 47.91.219.55, http://47.75.246.177/MT4API/RaiseAccount
			return $this->_mt4_host = '47.91.219.55';
		}
		
		protected function _exte_mt4_port()
		{
			//10522
			return $this->_mt4_port = '443';
		}
		
		protected function _exte_mt4_timeout()
		{
			return $this->_mt4_timeout = 5;
		}
		
		protected function _exte_mt4_query_request ($query)
		{
			//TCP数据样本
			//NEWACCOUNT MASTER=password|IP=103.193.174.54|GROUP=2|LOGIN=81052|NAME=test|PASSWORD=mk12345678|INVESTOR=|EMAIL=|COUNTRY=China|STATE=|CITY=|ADDRESS=|COMMENT=|PHONE=|PHONE_PASSWORD=|STATUS=|ZIPCODE=|ID=|LEVERAGE=100|AGENT=|SEND_REPORTS=1|DEPOSIT=50000[0A]QUIT[0A]
			//---- open socket
			$connectMT4 = @fsockopen($this->_exte_mt4_host(), $this->_exte_mt4_port(), $errno='', $errstr='', $this->_exte_mt4_timeout());
			
			if($connectMT4)
			{
				//---- send request
				if(fputs($connectMT4,"W$query\nQUIT\n")!=FALSE)
				{
					//---- receive answer
					/*$rs = array();*/
					/*while(!feof($connectMT4))
					{
						$line=fgets($connectMT4,128);
						if($line=="end\r\n") break;
						$rs[] = str_replace("\r\n", '', $line);
					}*/
					
					for ($line = fgets($connectMT4); !feof($connectMT4); $line = fgets($connectMT4)) {
						if($line=="end\r\n") break;
						if ($line === false) {
							$str = 'error';
						} else {
							$str[] = str_replace("\r\n", '', $line);
						}
					}
				} else {
					$str = 'mt4_query_error';
				}
				fclose($connectMT4);
			} else {
				$str = 'mt4_conn_error';
			}
			
			if (is_array($str)) {
				for ($i = 0; $i < count($str); $i ++) {
					if (strpos($str[$i], '=') !== false) {
						$newstr = explode('=', $str[$i]);
						$newrs[$newstr[0]] = $newstr[1];
					} else {
						$newrs[$i] = $str[$i];
					}
				}
				return $newrs;
			} else {
				return $str;
			}
		}
		
		protected function _exte_mt4_same($act)
		{
			return $act . ' MASTER=jja123|IP='. $this->_exte_get_user_loginIp() .'|';
		}
		
		/*
		 * array:2 [▼
			0 => "OK"
			"LOGIN" => "81097"
		]
		array:2 [▼
			0 => "ERROR"
			1 => "account create failed"
		]
		*/
		protected function _exte_sync_mt4_reigster ($user, $act='NEWACCOUNT')
		{
			$query = $this->_exte_mt4_same ($act) . $this->_exte_mt4_create_field_fill($user);
			
			return $this->_exte_mt4_query_request($query);
		}
		
		protected function _exte_sync_mt4_reigster2 ($user)
		{
			$ret = $this->_exte_mt4_main_param($this->_exte_mt4_options_crt_user(), $this->_exte_mt4_create_field_fill($user));
			
			if ($ret['ret'] == 0) {
				return array('0' => 'OK', 'Msg' => $ret['msg'], 'Result' => $ret['login']);
			} else {
				return array('0' => $ret['ret'], 'Msg' => $ret['msg'], 'Result' => $ret['login']);
			}
		}
		
		protected function _exte_sync_mt4_reigster_RaiseAccount($data)
		{
			$request = curl_init();
			curl_setopt($request, CURLOPT_URL, $this->_exte_mt4_host());
			curl_setopt($request, CURLOPT_HEADER, false);
			curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($request, CURLOPT_POST, true);
			curl_setopt($request, CURLOPT_POSTFIELDS, $this->_exte_mt4_create_field_fill2($data));
			
			$response = curl_exec($request);
			curl_close($request);
			
			$ret = json_decode($response, true);
			if ($ret['Status'] == '1') {
				return array('0' => 'OK', 'Msg' => $ret['Msg'], 'Result' => $ret['Result']);
			} else {
				return array('0' => $ret['Status'], 'Msg' => $ret['Msg'], 'Result' => $ret['Result']);
			}
		}
		
		//该类一般用于子类重写，mt4不同操作需要的不同列
		protected function _exte_mt4_create_field_fill($param, $fill = true)
		{
			//todo,注册时，组别需要重新设定
			$string = '';
			if ($fill) {
				$data = array(
					'group'                 => $param['mt4_grpId'],
					'login'                 => $param['user_id'],
					'name'                  => $this->_exte_mt4_username_convert_encode($param['user_name']),
					'password'              => base64_decode($param['password']),
					'investor'              => '',
					'email'                 => rand(123456789,999999999) . '@qq.com', //$param['email']
					'country'               => 'cn', //$param['country']
					'state'                 => 'sz',
					'city'                  => 'cn.sz',
					'address'               => '', //todo 用于记录当用户身份证 银行卡审核通过后，将银行卡开户行支行地址和银行卡号传递给mt4
					'comment'               => 'open by pccrm',
					'phone'                 => '86' . '-' . '159'. rand(12345678, 99999999), //$param['phone']
					'phone_password'        => '',
					'status'                => '',
					'zipcode'               => $param['parent_id'],
					'id'                    => '', //$param['IDcard_no']
					'leverage'              => 100,
					'agent'                 => '',
					'send_reports'          => 1,
					'deposit'               => '0',
				);
			} else {
				$data = $param;
			}
			
			/*foreach ($data as $k => $v) {
				$string .= $k.'='.$v.'|';
			}
			
			$string = rtrim($string,'|');
			$str = 'GROUP=2|LOGIN='.$param['user_id'].'|NAME=' . $this->_exte_mt4_username_convert_encode($param['user_name']) . '|PASSWORD=' . base64_decode($param['password']) . '|INVESTOR=|EMAIL=' . $param['email'] .
				'|COUNTRY=' . $param['country'] . '|STATE=SZ|CITY=CN.SZ|ADDRESS=|COMMENT=open by pccrm|PHONE=' . $param['phone'] . '|PHONE_PASSWORD=|STATUS=|ZIPCODE=' . $param['parent_id'] .
				'|ID=' . $param['IDcard_no'] . '|LEVERAGE=100|AGENT=|SEND_REPORTS=1|DEPOSIT=0';
			return $str;*/
			return $data;
		}
		
		protected function _exte_mt4_create_field_fill2($param, $fill = true)
		{
			
			$string = '';
			if ($fill) {
				$data = array(
					'login'                 => $param['user_id'],
					'Name'                  => $param['user_name'],//$this->_exte_mt4_username_convert_encode($param['user_name']),
					'Email'                 => rand(123456789,999999999) . '@qq.com',
					'Group'                 => $param['mt4_grpId'],
					'PassWord'              => base64_decode($param['password']),
					'PassWordDinvestor'     => base64_decode($param['password']),
					'leverage'              => 100,
					'Phone'                 => '86' . '-' . '159'. rand(12345678, 99999999),
					'AgentLogin'            => '',
					'Country'               => 'CN',
					'City'                  => 'SZ',
					'ZipCode'               => $param['parent_id'],
					'PasswordPhone'         => '',
					'Enable'                => 1, //非0表示启用账号
					'EnableChangePassword'  => 1, //非0表示启用变更密码
					'SendReports'           => 1, //非0启用
					'EnableOTP'             => 1, //非0表示启用一次性密码
					'Comment'               => 'open by pccrm', // len = 64
					'Address'               => '', //备注信息 len = 96
				);
			} else {
				$data = $param;
			}
			
			return $data;
			/*foreach ($data as $k => $v) {
				$string .= $k.'='.$v.'|';
			}
			
			$string = rtrim($string,'|');
			$str = 'GROUP=2|LOGIN='.$param['user_id'].'|NAME=' . $this->_exte_mt4_username_convert_encode($param['user_name']) . '|PASSWORD=' . base64_decode($param['password']) . '|INVESTOR=|EMAIL=' . $param['email'] .
				'|COUNTRY=' . $param['country'] . '|STATE=SZ|CITY=CN.SZ|ADDRESS=|COMMENT=open by pccrm|PHONE=' . $param['phone'] . '|PHONE_PASSWORD=|STATUS=|ZIPCODE=' . $param['parent_id'] .
				'|ID=' . $param['IDcard_no'] . '|LEVERAGE=100|AGENT=|SEND_REPORTS=1|DEPOSIT=0';
			return $str;*/
		}
		
		protected function _exte_mt4_username_convert_encode($name) {
			
			$encoding = mb_detect_encoding($name);
			$user_name_GBK = mb_convert_encoding($name, 'GBK', $encoding);
			return $user_name_GBK;
		}
		
		/*其他操作API*/
		protected function _exte_mt4_url()
		{
			//alisz.titanera.com, 47.75.157.81, http://47.75.123.86:9997/api
			return $this->_mt4_url = 'http://47.75.246.177:9997/api';
		}
		
		protected function _exte_mt4_options_crt_user()
		{
			return $this->_mt4_crt_user = 'createaccount';
		}
		
		protected function _exte_mt4_options_upd_user()
		{
			return $this->_mt4_upd_user = 'userupdate';
		}
		
		protected function _exte_mt4_options_get_balance()
		{
			return $this->_mt4_balance = 'getbalance';
		}
	
		protected function _exte_mt4_options_get_userinfo()
		{
			return $this->_mt4_user_info = 'getuserinfo';
		}
		
		protected function _exte_mt4_options_get_grp()
		{
			return $this->_mt4_user_info = 'getgroup';
		}
		
		protected function _exte_mt4_options_deposit_amt()
		{
			return $this->_mt4_deposit_amt = 'deposit';
		}
		
		protected function _exte_mt4_options_withdrawal_amt()
		{
			return $this->_mt4_withdrawal_amt = 'withdraw';
		}
		
		protected function _exte_mt4_options_upd_crdt_in()
		{
			return $this->_mt4_upd_crdt_in = 'creditin';
		}
		
		protected function _exte_mt4_options_upd_crdt_out()
		{
			return $this->_mt4_upd_crdt_in = 'creditout';
		}
		
		protected function _exte_mt4_options_verify_pwd()
		{
			return $this->_mt4_verify_pwd = 'checkpassword';
		}
		
		//该类一般用于子类重写，mt4不同操作需要的不同列
		protected function _exte_mt4_update_field_fill($param, $fill = false)
		{
			if ($fill) {
				$data = array(
					'login'                 => $param['user_id'],
					'group'                 => $param['group_id'],
					'name'                  => $this->_exte_mt4_username_convert_encode($param['user_name']),
					'password'              => base64_decode($param['password']),
					'password_investor'     => '',
					'email'                 => $param['email'],
					'country'               => $param['country'],
					'state'                 => 'SZ',
					'city'                  => 'CN.SZ',
					'address'               => '', //TODO 用于记录当用户身份证 银行卡审核通过后，将银行卡开户行支行地址和银行卡号传递给MT4
					'comment'               => 'open by pccrm', //默认
					'phone'                 => $param['phone'],
					'password_phone'        => '',
					'status'                => '',
					'zipcode'               => $param['parent_id'],
					'id'                    => $param['IDcard_no'],
					'leverage'              => 100,
					'agent_account'         => '',
					'send_reports'          => 1,
					'lead_source'           => '',
					'opt_secret'            => '',
					'enable'                => 1, // 1 勾上启用，0没勾上不启用
					'enable_read_only'      => 0, //默认 0， 没有勾上， 1勾上
					'enable_change_password'=> 1,
					'enable_otp'            => 0,
					'user_color'            => '',
				);
			} else {
				$data = $param;
			}
			
			return $data;
		}
		
		protected function _exte_mt4_ohter_query_request ($data)
		{
			$request = curl_init();
			curl_setopt($request, CURLOPT_URL, $this->_exte_mt4_url());
			curl_setopt($request, CURLOPT_HEADER, false);
			curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($request, CURLOPT_POST, true);
			curl_setopt($request, CURLOPT_POSTFIELDS, $data);
			
			$response = curl_exec($request);
			curl_close($request);
			
			return json_decode($response, true);
		}
		
		protected function _exte_mt4_main_param($opt, $param)
		{
			$string = '';
			$arg = array_merge(array('cmd'=> $opt), $param);
			/*foreach ($arg as $k => $v) {
				$string .= $k.'='.$v.'&';
			}
			
			$string = rtrim($string,'&');*/
			//var_dump($string);
			
			return $this->_exte_mt4_ohter_query_request(http_build_query($arg));
		}
		
		//修改账户信息
		protected function _exte_mt4_update_user($param, $fill = false)
		{
			return $this->_exte_mt4_main_param($this->_exte_mt4_options_upd_user(), $this->_exte_mt4_update_field_fill($param, $fill));
		}
		
		//修改账户信息, 一般用于子类重写使用的
		protected function _exte_mt4_update_user2($param, $fill = false)
		{
			return '';
		}
		
		//入金
		/*array:4 [▼
			"msg" => "OK"
			"op" => "balance"
			"order" => 308696
			"ret" => 0
		]*/
		protected function _exte_mt4_deposit_amount($uid, $amount, $cmt)
		{
			$data = array(
				'login'                     => $uid,
				'price'                     => $amount,
				'comment'                   => $cmt,
			);
			
			return $this->_exte_mt4_main_param($this->_exte_mt4_options_deposit_amt(), $data);
		}
		
		//出金
		protected function _exte_mt4_withdrawal_amount($uid, $amount, $cmt)
		{
			$data = array(
				'login'                     => $uid,
				'price'                     => $amount,
				'comment'                   => $cmt,
			);
			
			return $this->_exte_mt4_main_param($this->_exte_mt4_options_withdrawal_amt(), $data);
		}
		
		//修改信用额度 加信用
		protected function _exte_mt4_update_credit_in($uid, $crdtIn, $cmt)
		{
			$data = array(
				'login'                     => $uid,
				'price'                     => $crdtIn,
				'comment'                   => $cmt,
			);
			
			return $this->_exte_mt4_main_param($this->_exte_mt4_options_upd_crdt_in(), $data);
		}
		
		//修改信用额度，出信用
		protected function _exte_mt4_update_credit_out($uid, $crdtIn, $cmt)
		{
			$data = array(
				'login'                     => $uid,
				'price'                     => $crdtIn,
				'comment'                   => $cmt,
			);
			
			return $this->_exte_mt4_main_param($this->_exte_mt4_options_upd_crdt_out(), $data);
		}
		
		//修改主密码, 修改投资人密码 $investor = 1
		protected function _exte_mt4_reset_user_pwd($uid, $pwd, $investor = 0)
		{
			$data = array(
				'login'                     => $uid,
				'password'                  => $pwd,
				'password_investor'         => $investor
			);
			
			return $this->_exte_mt4_main_param($this->_exte_mt4_options_upd_user(), $data);
		}
		
		//验证密码
		/*
		array:3 [▼
			"msg" => "OK"
			"op" => "check_password"
			"ret" => 0
		]
		array:3 [▼
			"msg" => "Invalid account"
			"op" => "check_password"
			"ret" => 65
		]
		 */
		protected function _exte_mt4_verify_password($uid, $psw, $isInvestPwd = 0)
		{
			$data = array(
				'login'                     => $uid,
				'password'                  => $psw,
				'password_investor'         => $isInvestPwd,
			);
			
			return $this->_exte_mt4_main_param($this->_exte_mt4_options_verify_pwd(), $data);
		}
		
		//获取用户金额信息
		/*array:9 [▼
			"balance" => 20
			"equity" => 20
			"group" => "MK"
			"leverage" => 50
			"margin" => 0
			"margin_free" => 20
			"msg" => "OK"
			"op" => "balance"
			"ret" => 0
		]
		*/
		protected function _exte_mt4_get_balance($uid)
		{
			$data = array(
				'login'               => $uid,
			);
			
			return $this->_exte_mt4_main_param($this->_exte_mt4_options_get_balance(), $data);
		}
		
		//获取用户信息
		/*array:24 [▼
			"address" => ""
			"agent_account" => 0
			"balance" => 0
			"city" => ""
			"comment" => ""
			"country" => "China"
			"credit" => 0
			"email" => ""
			"group" => "MK"
			"id" => ""
			"interestrate" => 0
			"lead_source" => ""
			"leverage" => 0
			"login" => 81060
			"msg" => "OK"
			"name" => "哈哈哈测试3"
			"op" => "getuserinfo"
			"phone" => ""
			"prevequity" => 0
			"prevmonthequity" => 0
			"ret" => 0
			"state" => ""
			"status" => ""
			"taxes" => 0
		]*/
		protected function _exte_mt4_get_user_info($uid)
		{
			$data = array(
				'login'               => $uid,
			);
			
			return $this->_exte_mt4_main_param($this->_exte_mt4_options_get_userinfo(), $data);
		}
		
		//获取组别信息
		protected function _exte_mt4_get_user_group_info($uid)
		{
			$data = array(
				'login'                     => $uid,
			);
			
			return $this->_exte_mt4_main_param($this->_exte_mt4_options_get_grp(), $data);
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
		
		//通过API更新本地表数据，达到和mt4_users,agents,user 三个表的同一个用户账户资金一样且是最新的
		protected function _exte_mt4_update_local_user_info($user_id)
		{
			$_upd_info          = $this->_exte_mt4_get_user_info($user_id);
			$_upd_info_money    = $this->_exte_mt4_get_balance($user_id);
			
			$_table = $this->_exte_get_table_obj ($user_id);
			
			$num = $_table::where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))
				->find($_upd_info['login'])
				->update([
					'user_money'            => $_upd_info['balance'], //余额
					'effective_cdt'         => $_upd_info['credit'], //信用额
					'cust_lvg'              => $_upd_info['leverage'], //杠杆
					'cust_eqy'              => $_upd_info_money['equity'], //净值 = 账户余额 - 所有订单的获利与亏损，保证金
					'mt4_grp'               => $_upd_info['group'], //客户组
					'used_bond_money'       => $_upd_info_money['margin'], //已用保证金 == 已用预付款
					'available_bond_money'  => $_upd_info_money['margin_free'], //可用保证金 == 可用余额
					'rec_upd_date'          => date('Y-m-d H:i:s'),
				]);
				
			//返回受影响的行数
			return $num;
		}
	}