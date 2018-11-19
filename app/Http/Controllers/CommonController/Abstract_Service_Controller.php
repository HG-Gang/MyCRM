<?php
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/1/23
	 * Time: 18:10
	 */
	
	namespace App\Http\Controllers\CommonController;
	
	use App\Http\Controllers\CommonController\Abstract_Basic_Controller;
	use App\Model\Mt4Users;
	use Illuminate\Http\Request;
/*	use App\Models\MT4_TRADES;
	use App\Models\MT4_USERS;
	use App\Models\SystemConfig;*/
	
	abstract class Abstract_Service_Controller extends Abstract_Basic_Controller
	{
		/*
		 * 本类主要处理 同步MT4数据操作, 及其他数据查找
		 * */
		
		protected $_mt4_host                = '';
		
		protected $_mt4_port                = '';
		
		protected $_mt4_timeout             = '';
		
		protected $_mt4_ver                 = '';
		
		protected $_mt4_key                 = '';
		
		protected function _exte_enable_mt4()
		{
			return true;
		}
		
		protected function _exte_mt4_host()
		{
			return $this->_mt4_host = '47.75.55.124';
		}
		
		protected function _exte_mt4_port()
		{
			return $this->_mt4_port = '3390';
		}
		
		protected function _exte_mt4_timeout()
		{
			return $this->_mt4_timeout = 300;
		}
		
		protected function _exte_mt4_ver()
		{
			return $this->_mt4_ver = '000004';
		}
		
		protected function _exte_mt4_key()
		{
			return $this->_mt4_key = 'FFKkd7XU';
		}
		
		protected function _exte_mt4_query_request ($query)
		{
			if (env('SYNCMT4_UPDATEINFO')) {
				$Mt4Date = '';
				$newary = array();
				//$newary['mt4_connect'] = 'OK';
				
				$connectMT4 = @fsockopen($this->_exte_mt4_host(), $this->_exte_mt4_port(), $errno='', $errstr='', $this->_exte_mt4_timeout());
				
				if ($connectMT4) {
					if (fputs($connectMT4, "E$query\r\nQUIT\r\n") != FALSE)
						stream_set_timeout($connectMT4, $this->_exte_mt4_timeout());
					while (!feof($connectMT4)) {
						if (($line = fgets($connectMT4, 128)) == "end\r\n") break;
						$Mt4Date .= $line;
					}
					
					if($Mt4Date == "\r\n") {
						$Mt4Date = 'fatal_err=CANNOT_CONNECT';
					}
					
					fclose($connectMT4);
				} else {
					$Mt4Date = 'mt4_connect=_CONNECT_FAILED_';
				}
				
				if($Mt4Date == 'mt4_connect=_CONNECT_FAILED_') {
					$newary['mt4_connect'] = '_CONNECT_FAILED_';
				} else {
					foreach (explode('&', $Mt4Date) as $val) { //分割MT4返回的数据，并格式成数组
						$kv = explode('=', $val);
						if(in_array('fatal_err', $kv, true)) {
							$newary[$kv[0]] = $kv[1];
							break;
						} else {
							if(empty($kv)) {
								$newary['mt4_connect'] = '_CONNECT_FAILED_';
								break;
							} else {
								$newary[$kv[0]] = $kv[1];
							}
						}
					}
				}
				
				return $newary;
			}
		}
		
		protected function _exte_mt4_same($act)
		{
			return 'act=' . $act . '&ver=' . $this->_exte_mt4_ver () . '&key=' . $this->_exte_mt4_key ();
		}
		
		/**
		 *从MT4获取用户财务信息
		 * @key accountInfo
		 * return act=accountinfo&ver=000002&err=0&des=OK&acc=1001&bal=10000.00&cdt=0.00&lvg=100&eqy=10000.00&grp=demoforex&vol=0&umg=0.00&fmg=10000.00&nam=eddy
		 *
		 */
		protected function _exte_sync_mt4_accountinfo($user_id, $act='accountinfo') {
			$query = $this->_exte_mt4_same ($act) . '&acc=' . $user_id;
			return $this->_exte_mt4_query_request($query);
		}
		
		/*
		 * MT4 用户注册请求
		* @key register
		* @param string 账号 acc  (*)
		* @param string 姓名 nam (*)
		* @param string 交易密码 ctp (*)
		* @param string 邮箱 eml (*)
		* @param string 电话 tel （* + 区号 xxxyyy）
		* @param string ID号码 idn （* 身份证）
		* @param string 代理账号 agt
		* @param string 组 grp (*)
		* @param string 杠杆 lvg （* 默认 100）
		 * return acc, ctp, cip, cpp
		 * */
		protected function _exte_sync_mt4_reigster ($user, $act='reigster')
		{
			
			$encoding = mb_detect_encoding($user['user_name']);
			$user_name_GBK = mb_convert_encoding($user['user_name'], 'GBK', $encoding);
			$query = $this->_exte_mt4_same ($act) . '&acc=' . $user['user_id'] . '&nam=' . $user_name_GBK . '&ctp=' . $user['password'] . '&eml=' . $user['email'] . '&tel=' . $user['phone'] . '&idn=' . $user['IDcard_no'] . '&zip=' . $user['parent_id'] . '&grp='. $user['user_grp_name'] .'&cny=' . $user['str_rala'] .'&lvg=100';
			
			return $this->_exte_mt4_query_request($query);
		}
		
		/*
		 * MT4同步入金请求记录
		* @key deposit
		* $user_id 用户
		* $amt 金额 正数
		* $cmt 注释
		* $query3 = "act=deposit&ver=000006&key=Re2NlHsG&acc=8000006&amt=100&cmt=#8000006-返佣";
		* retrun "act=deposit&ver=000001&err=0&des=OK&acc=8000006&tck=3808401"
		* return array() tck 订单号
		* */
		protected function _exte_sync_mt4_deposit ($user_id, $amt, $cmt, $act='deposit')
		{
			
			$query = $this->_exte_mt4_same ($act) . '&acc=' . $user_id . '&amt=' . $amt . '&cmt=' . $cmt;
			return $this->_exte_mt4_query_request($query);
		}
		
		/*
		 * MT4同步出金请求记录
		 * $user_id 用户
		 * $amt 金额 正数
		 * $cmt 注释
		 * return tck 订单号
		 * */
		protected function _exte_sync_mt4_withdrawal ($user_id, $amt, $cmt, $act='withdrawal')
		{
			
			$query = $this->_exte_mt4_same ($act) . '&acc=' . $user_id . '&amt=' . $amt . '&cmt=' . $cmt;
			return $this->_exte_mt4_query_request($query);
		}
		
		/*
		* MT4同步信用 IN, OUT 请求记录
		* $user_id 用户
		* $amt 金额 正数
		* $cre_type 信用类型，credit-in, credit-out
		* $cmt 注释
		* return tck 订单号
		* */
		protected function _exte_sync_mt4_creditIn ($user_id, $amt, $cmt, $exp, $act='credit-in')
		{
			
			$query = $this->_exte_mt4_same ($act) . '&acc=' . $user_id . '&amt=' . $amt . '&cmt=' . $cmt . '&exp=' . $exp;
			return $this->_exte_mt4_query_request($query);
		}
		
		protected function _exte_sync_mt4_creditOut ($user_id, $amt, $cmt, $exp, $act='credit-out')
		{
			
			$query = $this->_exte_mt4_same ($act) . '&acc=' . $user_id . '&amt=' . $amt . '&cmt=' . $cmt . '&exp=' . $exp;
			return $this->_exte_mt4_query_request($query);
		}
		
		/*
		 * 用户更新信息, 可用于检测当前用户是否已经和MT4同步信息
		 * 更新密码
		 * */
		protected function _exte_sync_mt4_update_user ($user_id, $ctp, $act='update_user')
		{
			
			$query = $this->_exte_mt4_same ($act) . '&acc=' . $user_id . '&ctp=' . $ctp;
			return $this->_exte_mt4_query_request($query);
		}
		
		/*
		*  开启用户只读
		* $user_id 用户ID
		* */
		protected function _exte_sync_mt4_lock_user ($user_id, $act='lock_user')
		{
			
			$query = $this->_exte_mt4_same ($act) . '&acc=' . $user_id;
			return $this->_exte_mt4_query_request($query);
		}
		
		/*关闭用只读录状态
		 * $user_id 用户ID
		* */
		protected function _exte_sync_mt4_unlock_user($user_id, $act='unlock_user')
		{
			
			$query = $this->_exte_mt4_same ($act) . '&acc=' . $user_id;
			return $this->_exte_mt4_query_request($query);
		}
		
		/*用户开启
		*$user_id 用户ID
		* */
		protected function _exte_sync_mt4_enable_user($user_id, $act='enable_user')
		{
			
			$query = $this->_exte_mt4_same ($act) . '&acc=' . $user_id;
			return $this->_exte_mt4_query_request($query);
		}
		
		/*
		*用户关闭
		* $user_id 用户ID
		* */
		protected function _exte_sync_mt4_disable_user($user_id, $act='disable_user')
		{
			
			$query = $this->_exte_mt4_same ($act) . '&acc=' . $user_id;
			return $this->_exte_mt4_query_request($query);
		}
		
		/*用户信息更新*/
		//TODO 查看 L202和此API的关系 此API 专门用于更新多个列的，一般都是需要子类重写该类
		protected function _exte_sync_mt4_update_user_info ($user_id, $col_ary, $act='update_user')
		{
			return '';
		}
		
		/*用户银行卡信息更新到注释
		*$user_id 用户ID
		*$notes  注释
		* */
		protected function _exte_sync_mt4_update_user_bank ($user_id, $col_ary, $act='update_user')
		{
			return '';
		}
		
		/*用户名修改
		*$user_id 用户ID
		*$name 用户名
		* */
		protected function _exte_sync_mt4_update_user_name ($user_id, $col_ary, $act='update_user')
		{
			return '';
		}
		
		/*用户密码重置
		*$user_id 用户ID
		*$password 用户密码
		*string(44) "act=reset_password&ver=000001&err=0&des=OK "
		*return acc, ntp(新交易密码)， err = 0 (成功)
		* */
		protected function _exte_sync_mt4_reset_password ($user_id, $password, $act='reset_password')
		{
			
			$query = $this->_exte_mt4_same ($act) . '&acc='. $user_id. '&ntp=' . $password;
			return $this->_exte_mt4_query_request($query);
			
		}
		
		/*修改密码，适合前台客户操作
		* "act" => "change_password"
		"ver" => "000003"
		"err" => "0"
		"des" => "OK
		* */
		protected function _exte_sync_mt4_change_password($user_id, $oldpsw, $newpsw, $act='change_password')
		{
			
			$query = $this->_exte_mt4_same ($act) . '&acc=' . $user_id . '&ctp=' . $oldpsw . '&ntp=' . $newpsw;
			return $this->_exte_mt4_query_request($query);
		}
		
		/*
		* 用户登录时，同步MT4验证密码
		* 用户密码验证，仅限验证交易密码
		* $user_id  待验证用户ID
		* $ctp 待验证密码
		* return string err=0 表示验证通过
		* */
		protected function _exte_sync_mt4_verify_password($user_id, $ctp, $act='verify')
		{
			$query = $this->_exte_mt4_same ($act) . '&acc=' . $user_id . '&ctp=' . $ctp;
			return $this->_exte_mt4_query_request($query);
		}
		
		/*
		* 修改用户杠杆
		* $user_id
		* $new_lvg
		* */
		protected function _exte_sync_mt4_change_leverage($user_id, $new_lvg, $act='change_leverage')
		{
			
			$query = $this->_exte_mt4_same ($act) . '&acc=' . $user_id . '&lvg=' . $new_lvg;
			return $this->_exte_mt4_query_request($query);
		}
		
		/*
		* 修改用户组别
		* $user_id
		* $new_grp
		* */
		protected function _exte_sync_mt4_change_group($user_id, $new_grp, $act='change_group')
		{
			
			$query = $this->_exte_mt4_same ($act) . '&acc=' . $user_id . '&grp=' . $new_grp;
			return $this->_exte_mt4_query_request($query);
		}
		
		/*
		* 实时 汇总平仓单 返佣
		*
		* 一级IB:85   二级IB:80  三级：75  四级IB:70  有佣金：10
		返佣公式： 用户组标准手基数*返佣比例*手数
		
		举例：
		用户关系：1001-1002-1003-1004-8000001
		
		8000001 有佣金用户：  基数*0.1*手数
		1004 为四级  佣金为   基数*（0.7-0.1）*手数
		1003 为三级  佣金为   基数*（0.75-0.7）*手数
		1002 为二级  佣金为   基数*（0.8-0.75）*手数
		1001 为一级  佣金为   基数*（0.85-0.8）*手数
		* */
		protected function _exte_sync_mt4_commission_summary ()
		{
			
			
		}
		
		//通过API更新本地表数据，达到和mt4_users,agents,user 三个表的同一个用户账户资金一样且是最新的
		protected function _exte_sync_mt4_update_local_account_info($user_id)
		{
			
			$_upd_info = $this->_exte_sync_mt4_accountinfo ($user_id);
			
			$_table = $this->_exte_get_table_obj ($user_id);
			
			$num = $_table::where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))
				->find($_upd_info['acc'])
				->update([
					'user_money'            => $_upd_info['bal'], //余额
					'effective_cdt'         => $_upd_info['cdt'], //信用额
					'cust_lvg'              => $_upd_info['lvg'], //杠杆
					'cust_eqy'              => $_upd_info['eqy'], //净值 = 账户余额 - 所有订单的获利与亏损，保证金
					'cust_vol'              => $_upd_info['vol'], //持仓总量/手数
					'mt4_grp'               => $_upd_info['grp'], //客户组
					'used_bond_money'       => $_upd_info['umg'], //已用保证金 == 已用预付款
					'available_bond_money'  => $_upd_info['fmg'], //可用保证金 == 可用余额
					'rec_upd_date'          => date('Y-m-d H:i:s'),
				]);
			
			return $num;
		}
		
		protected function _exte_sync_mt4_to_loacl_data ($data_list, $_table) {
			
			$upd_num = array ();
			
			if(!empty($data_list)) {
				for($i = 0; $i < count($data_list); $i++) {
					$mt4_user[$i] = Mt4Users::select('LOGIN', 'LEVERAGE', 'BALANCE', 'EQUITY', 'CREDIT', 'MARGIN', 'MARGIN_FREE')->where('LOGIN', $data_list[$i]['user_id'])->first();
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
			
			return $mt4_user;
		}
		
		protected function _exte_mt4_convert_encoding($val) {
			
			$encoding = mb_detect_encoding($val);
			$val = mb_convert_encoding($val, 'GBK', $encoding);
			
			return $val;
		}
	}