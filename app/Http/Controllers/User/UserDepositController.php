<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018-04-23
	 * Time: 上午 11:20
	 */
	
	namespace App\Http\Controllers\User;
	
	use Illuminate\Http\Request;
	use App\Model\SystemConfig;
	use App\Http\Controllers\PayController\PayConfigController;
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	
	class UserDepositController extends Abstract_Mt4service_Controller
	{
		public function deposit_browse ()
		{
			//读取系统配置的汇率
			$_sys_conf = SystemConfig::select('sys_id', 'sys_deposit_rate', 'sys_draw_rate')->where('voided', '1')->get()->toArray();
			
			//获取全局入金规则
			$_global_role = $this->_exte_get_system_param('GLOBALDEPOSITRULE');
			
			//获取当天入金规则
			$_today_role = $this->_exte_get_system_param('DEPOSITRULE');

			$_user_info = $this->_exte_get_user_info($this->_user['user_id']);

			return view('user.user_deposit.user_deposit_browse')->with([
				'_user_info'        => $_user_info,
				'_sys_conf'         => $_sys_conf[0],
				'_global_role'      => $_global_role[0],
				'_today_role'       => $this->_exte_handle_deposit_role_structur_data($_today_role[0]),
			]);
		}
		
		public function deposit_request (Request $request) {
			$userId                 = $request->userId;
			$deposit_amt            = $request->deposit_amt; //存款金额
			$deposit_act_amt        = $request->deposit_act_amt; //实际到账金额
			$deposit_rate           = $request->deposit_rate; //汇率
			$pay_gateway            = $request->pay_gateway; //支付银行
			$pay_channel            = $request->pay_channel; //支付接口名字
			$pay_gateway2           = $request->pay_gateway2; //支付银行
			$pay_channel2           = $request->pay_channel2; //支付接口名字
			$gateway_bank           = $request->gateway_bank;
			
			$act_amt_USD            = number_format(($deposit_amt / $deposit_rate), '2', '.', '');
			//return "暂不支持在线支付.";
			if (!empty($pay_channel2) && $pay_channel2 == 'tongdaoER') {
				$pay_gateway = substr($pay_gateway2, 0, -1);
			}
			
			$param = array(
				'userId'            => $userId,
				'deposit_amt'       => $deposit_amt,
				'deposit_act_amt'   => $act_amt_USD,
				'pay_gateway'       => $pay_gateway2,
				'pay_channel'       => $pay_channel,
				'pay_channel2'      => 'tongdaoER',//$pay_channel2,
			);
			
			$PayConf = new PayConfigController();
			
			$PayConf->form_init($param);
		}
	}