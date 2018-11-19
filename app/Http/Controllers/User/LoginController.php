<?php
	
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/2/2
	 * Time: 16:05
	 */
	
	namespace App\Http\Controllers\User;
	use App\Model\Agents;
	use App\Model\User;
	use Captcha, Validator, Input, Redirect;
	use Illuminate\Http\Request;
	use App\Model\Mt4Users;
	use App\Model\Mt4Trades;
	use App\Model\UserImg;
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	
	class LoginController extends Abstract_Mt4service_Controller
	{
		
		protected $_live800_hashCode    = 'd06860d45b9bdc08085f970625b63c0d'; //live800客户端 会员对接 那里一致
		
		protected $_memo                = 'PADAFX'; //md5('PADAFX') 等于上面的
		
		public function login() {
			return view('user.login.login');
		}
		
		public function index(Request $request) {
			
			//TODO 每一次刷新URL时,通过API请求，更新当前账户信息
			if (env('SYNCMT4_UPDATEINFO')) {
				$_rs = $this->_exte_mt4_update_local_user_info ($this->_user['user_id']);
			}
			
			$_user_info = $this->_exte_get_user_info ($this->_user['user_id']);
			$_user_info['img'] = UserImg::where('user_id', $this->_user['user_id'])->where('voided', '1')->get()->toArray();
			
			//每次刷新，更新 $this->_user
			$request->session ()->put ('suser', $_user_info);
			$this->_user = $request->session ()->get ('suser');
			$_hasCode = $this->_exte_get_live800_hasCode($this->_user);
			
			return view('user.index.index')->with ([
				'_user_info'                => $_user_info,
				'_hasCode'                  => $_hasCode,
				'_role'                     => $this->_exte_get_user_role($_user_info['user_id']),
			]);
		}
		
		public function captcha() {
			ob_clean();
			return Captcha::create('custom_captcha');
		}
		
		public function signIn(Request $request) {
			
			$_rs = array ();
			
			$loginUid       = $request->loginUid;
			$loginPsw       = $request->loginPassword;
			$cptcode        = $request->cptcode;
			$_verifyType    = $request->session ()->get ('verifyType');
			
			if (!Captcha::check($cptcode)) {
				$_rs = $_rs + array ('errcptcode' => '验证码错误!', 'loginStatus' => self::BAD_REQUEST);
				return response()->json($_rs);
			}
			
			$_user_info = $this->_exte_get_user_info ($loginUid);
			
			if ($_user_info == null) {
				$_rs = $_rs + array ('notactive' => '无效账户!', 'loginStatus' => self::NO_ACTIVE_STATUS);
			}
			
			if (env('SYNCMT4_UPDATEINFO')) {
				$_chk_mt4_user = $this->_check_mt4_user ($loginUid);
				if ($_chk_mt4_user == null && $_user_info != null) {
					//在线同步注册MT4
					$mt4_grpId = $this->_exte_get_mt4_grpId($_user_info['mt4_grp']);
					$_user_info['mt4_grpId'] = $mt4_grpId[0]['user_group_name'];
					$mt4 = $this->_exte_sync_mt4_reigster2 ($_user_info);
					if (is_array($mt4) && $mt4['0'] == 'OK') {
						$send_type = ($_verifyType == 'phone') ? 'registerSucInfo' : 'registerSucInfo2';
						$phone = substr($_user_info['phone'], (stripos($_user_info['phone'], '-') + 1));
						$_phone = $this->_exte_send_phone_notify($phone, 'registerSucInfo', array ('user_id' => $_user_info->user_id, 'password' => base64_decode ($_user_info->password)));
						//$_email = $this->_exte_send_email_notify($_user_info->email, '注册成功', $_user_info, 'registerSuc', $_verifyType);
						$_num = $this->_sync_mt4_register_update_local_user_info($_user_info['user_id']);
						$_rs = $_rs + array ('notsyncmt4' => '在线同步注册成功!', 'loginStatus' => self::HTTP_ACTIVE_MT4_SUCCESS);
					} else {
						$_rs = $_rs + array ('notsyncmt4' => '在线同步失败!，请联系客服.', 'loginStatus' => self::HTTP_ACTIVE_MT4_FAIL);
					}
				} else {
					$mt4 = $this->_exte_mt4_verify_password ($loginUid, $loginPsw);
					
					/*if($mt4['ret'] != '0' && $mt4['msg'] != 'OK') {
						$_rs = $_rs + array ('mt4msg' => '网络故障,暂时无法登陆', 'loginStatus' => self::INTERNAL_SERVER_ERROR);
					} else {*/
						if (is_array($mt4) && $mt4['ret'] != '0') {
							$_rs = $_rs + array ('errpsw' => '密码错误!', 'loginStatus' => self::NOT_FOUND);
						} else {
							if (base64_decode($_user_info['password']) != $loginPsw) {
								$_num = $this->_update_user_password ($loginUid, $loginPsw);
							}
						}
					//}
				}
			}
			
			if (empty($_rs)) {
				//记录当前用户最后登录时间和IP地址
				$_lastTime = $this->_exte_update_user_last_logintime($_user_info['user_id']);
			}
		
			$request->session ()->put ('suser', $_user_info);
			return response()->json((!empty($_rs)) ? $_rs : array ('msg' => 'OK', 'loginStatus' => self::OK));
		}
		
		public function mainHome(Request $request) {
			
			//得到当前代理商直属客户的出入金及直属代理商和直属客户总数
			return view ('user.index.main')->with ([
				'_user_info'                => $this->_user,
				'_role'                     => $this->_exte_get_user_role($this->_user['user_id']),
				'_agentsTotal'              => $this->_exte_get_agentsTotal($this->_user['user_id']), //直属代理总数
				'_accountTotal'             => $this->_exte_get_accountTotal($this->_user['user_id']), //直属客户总数
				//'_depositTotal'             => $this->_exte_get_depositTotal($_user_info['user_id']),
				//'_withdrawTotal'            => $this->_exte_get_withdrawTotal($_user_info['user_id']),
				'_closeTotal'               => $this->_exte_get_closeTotal($this->_user['user_id']), //已平总数
				'_openTotal'                => $this->_exte_get_openTotal($this->_user['user_id']), //未平总数
				'_ytdDepTotal'              => $this->_exte_get_ytdDepTotal($this->_user['user_id']), //昨日入金
				'_ytdDrawTotal'             => $this->_exte_get_ytdDrawTotal($this->_user['user_id']), //昨日出金
				'_hotsNews'                 => $this->_exte_get_hotsNews(),
			]);
		}
		
		//查看订单人的详细信息
		//$userId, $role
		public function show_user_detail ($userId, $role) {
			
			//TODO 更新本地表当前账户信息
			if (env('SYNCMT4_UPDATEINFO')) {
				$_rs = $this->_exte_mt4_update_local_user_info ($userId);
			}
			
			//userId基本信息， 账户资金，账户状态，账户类别
			$_user_info = $this->_exte_get_user_info($userId);
			
			//持仓单情况 已平、未平仓单
			$_user_info['close'] = Mt4Trades::where('LOGIN', $userId)->where('CLOSE_TIME', '>', '1970-01-01 00:00:00')->whereIn('CMD', array(0, 1, 2, 3, 4, 5))->where('CONV_RATE1', '<>', 0)->count();
			$_user_info['open'] = Mt4Trades::where('LOGIN', $userId)->where('CLOSE_TIME', '1970-01-01 00:00:00')->whereIn('CMD', array(0, 1, 2, 3, 4, 5))->where('CONV_RATE1', '<>', 0)->count();
			
			//得到当前userId的用户关系链
			$_user_info['user_rala'] = $this->_exte_show_account_relationship_chain($userId, ' -> ', 'idname', $role);
			//TODO 上线时改回来
			//$_user_info['role'] = 'admin';//$role;
			$_user_info['role'] = $role;
			//判定当前账户ID是否是当前登录账号ID的直属客户
			//TODO test
			//$_user_info['direct_user'] = 'TRUE';
			$_user_info['direct_user'] = ($_user_info['parent_id'] == $this->_user['user_id']) ? 'TRUE' : 'FALSE';
			
			return view ('user.user_detail.show_user_detail')->with (['_user_info' => $_user_info]);
		}
		
		public function loginOut(Request $request) {
			$request->session()->flush();
			return Redirect()->route('login');
		}
		
		protected function _check_mt4_user ($_id)
		{
			return Mt4Users::_mt4_user_info($_id);
		}
		
		protected function _update_user_password($_id, $psw)
		{
			$_table = $this->_exte_get_table_obj ($_id);
			
			return $_table::whereIn('voided', array ('0', '1', '2'))->where('user_status', array ('0', '1', '2', '4'))
				->where('user_id', $_id)->update([
					'password'          => base64_encode($psw),
					'rec_upd_user'      => $_id,
					'rec_upd_date'      => date('Y-m-d H:i:s'),
				]);
		}
		
		protected function _exte_get_live800_hasCode($data) {
			
			// userId & name & memo & timestamp & key  将userid，name,memo,timestamp和token以拼接字符串的方式拼接在一起得到字符串str1；
			$userinfo = $data['user_id'] . $data['user_name'] . '(' . $data['user_id'] . ')' . $this->_memo . time() . $this->_live800_hashCode;
			
			//  将第1步得到的字符串str1进行URL编码得到str2，编码字符集采用utf-8；将第2步得到的字符串str2进行md5加密得到字符串hashcode
			$hashCode = md5(urlencode($userinfo));
			
			//userId=8000010&name=攻城狮&memo=test&hashCode=76e1e2c855c6928080473a79d4caaa18&timestamp=1512637904000
			$user_str = 'userId=' . $data['user_id'] . '&name=' . $data['user_name'] . '(' . $data['user_id'] . ')' . '&memo=' . $this->_memo . '&hashCode=' . $hashCode . '&timestamp=' . time();
			
			// 将所有参数合在一起进行URL编码，编码字符集采用utf-8，最后得到info值；
			$info = urlencode($user_str);
			
			return $info;
		}
		
		protected function _sync_mt4_register_update_local_user_info($_id) {
			return $this->_exte_custom_update_user_voided ($_id);
		}
		
		public function test_sms() {
			//dd($this->_exte_mt4_get_user_info(637001));
			//$all_agt        = AgentsGroup::select('group_id', 'group_name', 'agents_comm_prop')->whereIn('group_id', array(1, 2, 3, 4))->where('voided', '1')->get()->toArray();
			//$_email = $this->_exte_send_email_notify('361598216@qq.com', '注册成功', $this->_exte_get_user_info(100001), 'registerSuc', 'email');
			//$_email2 = $this->_exte_send_phone_notify('18566205824', 'registerCode', $this->_exte_get_user_info(100001));
			//$s = strripos('JJAFX-20180607102704-1001', '-');
			//$mt4 = $this->_exte_mt4_get_user_info(8000010);
			//dd($_email2);
			//dd(substr('JJAFX-20180607102704-1001', $s + 1));
			//$res_deposit=$this->_exte_mt4_update_user (array('login'=>81051 , 'enable_read_only'=>'1'));
			//$res_deposit=$this->_exte_mt4_update_user (array('login'=>81051 , 'email'=>'123456@qq.com'));
			//$res_deposit=$this->_exte_mt4_withdrawal_amount ('81051', 10000, '81051' . self::QK);
			//$res_deposit=$this->_exte_mt4_deposit_amount ('81051', 1000000, '81051' . self::CZ);
			//$res_deposit=$this->_exte_mt4_reset_user_pwd ('81051', 'abcd1234');
			//$res_deposit=$this->_exte_mt4_update_credit_in('81051', 10000000, '81051' . self::XY);
			//$res_deposit=$this->_exte_mt4_update_credit_out('81051', 100, '81051' . self::BCQLXYDK);
			//$res_deposit=$this->_exte_mt4_update_user(array('login'=>81098 , 'group'=>'demoforex'));
			//var_dump($res_deposit);
			//dd($res_deposit);
			//var_dump($res_deposit);//显示结果
			//dd($this->_exte_send_phone_notify('15915470631', 'registerCode', array('code'=> 123456)));
			//dd(getprotobyname('TCP'));
			/*// 建立客户端的socet连接
			$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		//连接服务器端socket
					$connection = socket_connect($socket, '103.66.57.201', 36698);
		//要发送到服务端的信息。
		//$arr = array('id'=>123,'mthost' => '47.52.225.72:443', 'mgr' => 88, 'mgrpass' => 'abc110', 'login' => 900158, 'password' => 'abc110');
		$arr = array('id'=>10001,'mthost' => '47.52.225.72:443', 'mgr' => 88, 'mgrpass' => 'abc110', 'login' => 900158, 'password' => 'abc110','money'=>100);
		
		$send_data = json_encode($arr, JSON_FORCE_OBJECT);
		// 将客户的信息写到通道中，传给服务器端
		if (!socket_write($socket, "$send_data\n")) {
			echo "Write failed\n";
		}
		//服务器端收到信息后，客户端接收服务端传给客户端的回应信息。
		$buffer = socket_read($socket, 1024, PHP_NORMAL_READ);
		echo "sent to server:$send_data\n response from server was:" . $buffer . "\n";*/
			
			/*$ws = 'http://alisz.titanera.com:28888';
			//$ws = 'http://st.titanera.com:28888';
			try {
				$soap = new SoapClient($ws);
			}
			catch(SoapFault $e)
			{
				echo $e;
			}*/
			
			/*$ret = $soap->__getFunctions();
			echo "Func list: </br>";
			foreach($ret as $value)
			{
				echo $value."</br>";
			}
			
			echo "<br />";
			
			$ret=$soap->__getTypes();
			
			echo "Types list: </br>";
			foreach($ret as $value)
			{
				echo $value."</br>";
			}
			
			echo "<br />";*/
			
			//$result=$soap->getbalance (array('login'=>81040));
			//var_dump($result);//显示结果
			//dd($this->object_to_array($result));
			//echo "<br />";
			//$res_deposit=$soap->deposit (array('login'=>81041 , 'money'=>10,'comment'=>'81040-CZ'));
			//var_dump($res_deposit);//显示结果
			//$res_deposit=$soap->creditin(array('login'=>81040 , 'money'=>10000,'comment'=>'81040-XY'));
			//dd($result);//显示结果
			//TCP数据样本
			//WNEWACCOUNT MASTER=password|IP=[图片]103.193.174.54|GROUP=2|LOGIN=81052|NAME=test|PASSWORD=mk12345678|INVESTOR=|EMAIL=|COUNTRY=China|STATE=|CITY=|ADDRESS=|COMMENT=|PHONE=|PHONE_PASSWORD=|STATUS=|ZIPCODE=|ID=|LEVERAGE=100|AGENT=|SEND_REPORTS=1|DEPOSIT=50000[0A]QUIT[0A]
			/*$Mt4Date = $this->_exte_sync_mt4_reigster2('aaa');
			foreach (explode('\r\n', $Mt4Date) as $val) { //分割MT4返回的数据，并格式成数组
				$kv = explode('=', $val);
				$newary[$kv[0]] = $kv[1];
				/*if(in_array('fatal_err', $kv, true)) {
					$newary[$kv[0]] = $kv[1];
					break;
				} else {
					if(empty($kv)) {
						$newary['mt4_connect'] = '_CONNECT_FAILED_';
						break;
					} else {
						$newary[$kv[0]] = $kv[1];
					}
				}*/
			//$a = $this->_exte_sync_mt4_reigster2('aaa');
			//$a = $this->_exte_mt4_get_user_group_info(81060);
			//$a = $this->_exte_mt4_verify_password(81075, 'abcd1234');
			//$a = $this->_exte_mt4_get_user_info(81060);
			//$a = $this->_exte_mt4_get_balance(81060);
			//dd($a);
			//var_dump($this->object_to_array($a));
			//dd($this->object_to_array($a));
		}
		
		public function test_register ()
		{
			//$ret = $this->_exte_mt4_deposit_withdrawal_amount(637001, -10, 'withdrawal-Adj');
			//$reg = $this->_exte_mt4_get_balance(637001);
			/*$data = array(
				'user_id' => '40370001',
				'user_name' => '测试注册测试',
				'mt4_grpId' => 'PD-B1-A',
				'password' => 'abcd1234',
				'parent_id' => '637001',
			);*/
			//$reg = $this->_exte_mt4_verify_password(637001, 'abcd1234');
			//$reg1 = $this->_exte_get_user_info(200001);
			//$reg = $this->_exte_sync_mt4_reigster2($reg1);
			//$_email = $this->_exte_send_email_notify('yyc_liang@qq.com', '注册成功', $reg1, 'registerSuc', 'verifyphone');
			//dd($_email);
			//dd(json_decode($ret['Result'], true));
			//$deo1 = $this->_exte_mt4_withdrawal_amount(6000256, 50000, 'Withdraw-Adj');
			//dd($deo1);
			//禁止客户登录及交易
			//$params = array('login' => 100481, 'enable' => 0, 'enable_read_only' => 1);
			//$mt4_data = $this->_exte_mt4_update_user($params);
			//dd($mt4_data);
			//$info = $this->_exte_get_user_info(100001);
			//$phone = substr($info['phone'], (stripos($info['phone'], '-') + 1));
			//$data = array('user_name' => $info['user_name'], 'user_id' => $info['user_id'], 'amt' => 1000);
			//$_phone = $this->_exte_send_phone_notify($phone, 'widthdrawTH', $data);
			//$verify_psw = $this->_exte_mt4_verify_password($this->_user['user_id'], $password);
			//dd($this->_exte_get_mt4_grpId('AGJ-B-0'));exit();
			/*$name = array(
				0 => '夏宇泽', 1 => '钟书豪', 2 => '钟天耀', 3 => '汪明婕', 4 => '汪皓轩',
				5 => '段文曼', 6 => '秦紫晴', 7 => '武佩鸿', 8 => '黎昱涵', 9 => '黎若馨',
				//10 => '王皓月', 11 => '王文田', 12 => '王玥婷', 13 => '王博贤', 14 => '王翠楠',
				//15 => '张月婷', 16 => '张雨杨', 17 => '张昊然', 18 => '张鑫雨', 19 => '张馨怡',
				//20 => '刘晓婷', 21 => '刘益冉', 22 => '刘名扬', 23 => '刘家洋', 24 => '刘易轩',
				//25 => '黄奕轩', 26 => '黄雅鑫', 27 => '黄镌謦', 28 => '黄文瀚', 29 => '黄嘉贤',
			);
			$amt = array(
				0 => 90000, 1 => 800000, 2 => 150000,
				3 => 210000,
			);
			for ($i = 0; $i <= 9; $i ++) {
				$_rs[$i] = Agents::create([
					'user_name'             => $name[$i],
					'password'              => base64_encode('abcd123'),
					'sex'                   => ($i % 2 == 0) ? '男' : '女',
					'phone'                 => '86' . '-' . '159'. rand(12345678, 99999999),
					'IDcard_no'			    => '44'. rand(1234567890, 9999999999) . rand(654321, 987654),
					'email'				    => rand(123456789, 999999999) . '@qq.com',
					'group_id'              => 4,
					'parent_id'             => '637001', // 查找URL的编号的user_id, 0 属于平台的用户
					'user_money'		    => '0',
					'cust_eqy'              => '0',
					'effective_cdt'         => '0',
					'comm_prop'			    => 70,
					'mt4_grp'			    => 'PD-B1-A',
					'trans_mode'            => 1, // 交易模式，0 佣金模式，1 保证金模式
					'settlement_model'      => '1', //用户结算模式，1 线上结算，2 线下结算
					//'bond_money'          => '0/1', // 保证金金额
					'IDcard_status'		    => '0', // 默认 0 没通过审核，1 通过审核，2 正在审核中
					'user_status'           => '0', //0 未认证，1 已认证，-1 禁用
					'is_confirm_agents_lvg' => '1',
					'enable_readonly'       => '0', //默认， 能登录能交易(0=未勾上)，能登录 不能交易(1 = 只读 勾上)
					'is_out_money'          => '0', //default (0) 允许出金	1 不允许
					'enable'                => '1', //默认启用(能登录能交易, 1 = 勾上)， 不能登录(0 = 未勾上)
					'bank_status'           => '0',
					'IDcard_status'			=> '0',
					'cust_lvg'              => 100,
					'rights'                => 70, //权益比例
					'cycle'                 => 1,//结算周期
					'voided'                => '1', //注册后允许登录
					'rec_crt_date'          => date('Y-m-d H:i:s'),
					'rec_upd_date'          => date('Y-m-d H:i:s'),
					'rec_crt_user'          => 'admin',
					'rec_upd_user'          => 'admin',
				]);
				
				//$_str_rala[$i] = $this->_exte_show_account_relationship_chain($_rs[$i]->user_id, '-', 'id', 'admin'); //用户关系链
				$num = Agents::find($_rs[$i]->user_id)->update(['mt4_code' => $_rs[$i]->user_id, 'is_out_money' => '1', 'country' => '637001-' . $_rs[$i]->user_id, 'rec_upd_date' => date('Y-m-d H:i:s')]);
				$data[$i] = Agents::where('user_id', $_rs[$i]->user_id)->first();
				$mt4_grpId[$i] = $this->_exte_get_mt4_grpId($_rs[$i]->mt4_grp);
				$data[$i]['mt4_grpId'] = $mt4_grpId[$i][0]['user_group_name'];
				$mt4[$i] = $this->_exte_sync_mt4_reigster2 ($data[$i]);
				$deo[$i] = $this->_exte_mt4_deposit_amount($_rs[$i]->user_id, 500000, 'Deposit-Adj');
				$str[$i] = $_rs[$i]->user_id . ' : ' . $_rs[$i]->user_name . ' psw: '. 'abcd123';
			}
			dd(array('deo1' => $deo, 'mt4' => $mt4, 'usr' => $str));*/
			//dd(array('mt4' => $mt4, 'deposit' => $deo));*/
			/*for ($i = 100261; $i <= 100360; $i ++) {
				$_rs[$i] = $this->_exte_get_user_info($i);
				$mt4_grpId[$i] = $this->_exte_get_mt4_grpId($_rs[$i]['mt4_grp']);
				$_rs[$i]['mt4_grpId'] = $mt4_grpId[$i][0]['mt4_grpId'];
				$mt4[$i] = $this->_exte_sync_mt4_reigster2 ($_rs[$i]);
				$deo[$i] = $this->_exte_mt4_deposit_amount($i, 200000, 'Deposit-Adj');
				//$num[$i] = Agents::find($i)->update(['is_out_money' => '1', 'rec_upd_date' => date('Y-m-d H:i:s')]);
			}
			dd($deo);*/
			
			/*$data['amt']        = 934.72;
			$data['user_id']    = '8000250';
			$data['user_name']  = '徐德江';
			//$data['phone']      = '15393661239';
			//$deo1 = $this->_exte_mt4_deposit_amount($data['user_id'], $data['amt'], '8000250-15112-CZ');
			//$_sendinfo1          = $this->_exte_send_phone_notify($data['phone'], 'deposit', $data);
			
			$data1['amt']        = 2242.15;
			$data1['user_id']    = '8000287';
			$data1['user_name']  = '李子佳';
			$deo2 = $this->_exte_mt4_deposit_amount('8000287', 2242.15, '8000287-14415-CZ');
			$_sendinfo2          = $this->_exte_send_phone_notify('13828316115', 'deposit', $data1);
			
			dd(array('deo1' => $deo1, 'send1' => $_sendinfo1, 'deo2' => $deo2, 'send2' => $_sendinfo2, ));*/
			//$_email = $this->_exte_send_email_notify('991013978@qq.com', '注册成功', $this->_exte_get_user_info(8000392), 'registerSuc', 'verifyemail');
			//$data['user_id'] ='8000392';
			//$data['password'] ='a413199';
			//$_email2 = $this->_exte_send_phone_notify('15932153331', 'registerSucInfo', $data);
			//dd($_email2);
		}
	}