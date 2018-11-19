<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018-04-24
	 * Time: 上午 10:14
	 */
	
	namespace App\Http\Controllers\User;
	
	use App\Model\Mt4Trades;
	use Illuminate\Http\Request;
	use App\Model\SystemConfig;
	use App\Model\DrawRecordLog;
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	
	class UserWithdrawController extends Abstract_Mt4service_Controller
	{
		protected function _exte_enable_mt4 ()
		{
			return false;
		}
		
		public function withdraw_browse ()
		{
			//读取系统配置的汇率
			$_sys_conf = SystemConfig::select('sys_id', 'sys_deposit_rate', 'sys_draw_rate', 'sys_poundage_money')->where('voided', '1')->get()->toArray();
			
			//获取全局出金规则
			$_global_role = $this->_exte_get_system_param('GLOBALWITHDRAWRULE');
			
			//获取当天出金规则
			$_today_role = $this->_exte_get_system_param('WITHDRAWRULE');
			
			//TODO 将最新资金信息更新到本地表中
			if (env('SYNCMT4_UPDATEINFO')) {
				$_rs = $this->_exte_mt4_update_local_user_info ($this->_user['user_id']);
			}
			
			//重新获取当前用户最新信息
			$_user_info = $this->_exte_get_user_info($this->_user['user_id']);
			
			//获取当用户订单情况
			$_isOrderNo = Mt4Trades::where('LOGIN', $this->_user['user_id'])->where('CLOSE_TIME', '1970-01-01 00:00:00')->where('CONV_RATE1', '<>', 0)->whereIn('CMD', array(0, 1, 2, 3, 4, 5))->count();
			
			//TODO 能登录就可以取钱, 并且银行卡审核通过后才可以申请出金
			return view('user.user_withdraw.user_withdraw_browse')->with([
				'_user_info'        => $_user_info,
				'_sys_conf'         => $_sys_conf[0],
				'_isOrderNo'        => $_isOrderNo,
				'_global_role'      => $_global_role[0],
				'_today_role'       => $this->_exte_handle_withdraw_role_structur_data($_today_role[0]),
			]);
		}
		
		public function withdraw_request (Request $request)
		{
			$userId          = $request->userId;
			$withdraw_amt    = $request->withdraw_amt; //取款金额
			$withdraw_psw    = $request->withdraw_psw;
			$withdraw_rate   = $request->withdraw_rate; //取款汇率
			$poundagemoney   = $request->poundagemoney; //当前取款手续费
			
			if (env('SYNCMT4_UPDATEINFO')) {
				$_rs             = $this->_exte_mt4_update_local_user_info ($this->_user['user_id']);
			}
			$_user_info      = $this->_exte_get_user_info($this->_user['user_id']);
			
			//计算风险率，低于100 不能出金
			//除数不能为0
			if ($_user_info['used_bond_money'] != 0) {
				$risk_rate = ($_user_info['cust_eqy'] / $_user_info['used_bond_money']) * 100; //风险率 =（净值 / 已用保证金）* 100
				if($risk_rate < 100) {
					return response()->json([
						'msg'           => 'FAIL',
						'err'           => 'margin_level_low100',
						'col'           => 'NOTCOL',
					]);
				}
			}
			
			if ($withdraw_amt > $_user_info['user_money']) {
				return response()->json([
					'msg'           => 'FAIL',
					'err'           => 'more_available_amt',
					'col'           => 'withdraw_amt',
				]);
			}
			
			if ($withdraw_amt >= 7000) {
				return response()->json([
					'msg'           => 'FAIL',
					'err'           => 'more_than_sys_val',
					'col'           => 'withdraw_amt',
				]);
			}
			
			//检查密码
			$mt4 = $this->_exte_mt4_verify_password($this->_user['user_id'], $withdraw_psw);
			if(!is_array($mt4)) {
				return response()->json([
					'msg'           => 'FAIL',
					'err'           => 'FATALCANOTCONNECT',
					'col'           => 'NOTCOL',
				]);
			} else if(is_array($mt4) && $mt4['ret'] != '0') {
				return response()->json([
					'msg'           => 'FAIL',
					'err'           => 'PSWERR',
					'col'           => 'withdraw_psw',
				]);
			} else {
				//TODO 出金手续费低于100 收取额定手续费
				$_real_rmb = 0;
				if ($withdraw_amt < 100) {
					//收取 固定 5 美元手续费
					//(取款金额 - 5) * 当时的汇率 = 实际取款RMB
					$_real_rmb = ($withdraw_amt - $poundagemoney) * $withdraw_rate;
				}
				
				
				//开始计算客户需要扣除的手续费金额
				//取款金额 * 当时的汇率 = 实际取款RMB
				//$_real_rmb = $withdraw_amt * $withdraw_rate;
				//真正实际扣取RMB手续费是 真正实际到账RMB - RMB手续费收费标准
				//$_real_pdg = $this->draw_poundage_rule($_real_rmb);
				//真正实际到账RMB 是  实际取款RMB  - 手续费RMB
				//$_real_rmb = $_real_rmb - $_real_pdg;
				
				//API请求扣除取款金额
				$_mt4_amt = $this->_exte_mt4_withdrawal_amount($_user_info['user_id'], $withdraw_amt, $_user_info['user_id'] . self::QK);
				
				if (is_array($_mt4_amt) && $_mt4_amt['ret'] == '0') {
					//MT4扣款成功， 短信通知，本地生存记录
					$phone = substr($_user_info['phone'], (stripos($_user_info['phone'], '-') + 1));
					$data['amt'] = $withdraw_amt;
					$data['user_name'] = $this->_user['user_name'];
					$data['user_id'] = $this->_user['user_id'];
					$_phone_sms = $this->_exte_send_phone_notify($phone, 'widthdraw', $data);
					
					$_rs = DrawRecordLog::create([
						'mt4_trades_no'                 => $_mt4_amt['order'], //MT4返回的编号
						'user_id'                       => $_user_info['user_id'], // 申请用户ID
						'user_name'                     => $_user_info['user_name'], // 申请用户名
						'apply_amount'                  => $withdraw_amt,// 申请出金金额
						'act_apply_amount'              => ($withdraw_amt < 100) ? ($withdraw_amt - $poundagemoney) : $withdraw_amt, //实际申请出金金额，这里是记录扣取系统预设每笔扣取手续费百分比 * 申请金额 后的金额
						'act_draw'                      => ($withdraw_amt < 100) ? $_real_rmb : ($withdraw_amt * $withdraw_rate), //  $act_draw 实际出金金额RMB
						'act_pdg_rmb'                   => 0, //收取RMB 手续费
						'draw_rate'                     => $withdraw_rate, // 申请取款汇率
						'draw_poundage'                 => ($withdraw_amt < 100) ? $poundagemoney : 0, // 申请取款手续费
						'draw_bank_no'                  => $_user_info['bank_no'], // 申请出金银行号
						'draw_bank_class'               => $_user_info['bank_class'], // 申请出金银行
						'draw_bank_info'                => $_user_info['bank_info'], // 申请出金银行开户行详细地址
						'apply_status'                  => '0', // 出金申请状态0 = 待处理1 = 正在处理2 = 已出款3 = 出款失败， 如果是 3 则  apply_remark备注必填！
						'apply_remark'                  => '', // 出金申请状态失败的时候 【3】 该列必须填写备注
						'voided'                        => '1',
						'mt4_return_status'             => $_mt4_amt['ret'],
						'rec_crt_user'                  => $_user_info['user_name'],
						'rec_upd_user'                  => $_user_info['user_name'],
						'rec_crt_date'                  => date('Y-m-d H:i:s'),
						'rec_upd_date'                  => date('Y-m-d H:i:s'),
					]);
					
					if($_rs) {
						return response()->json([
							'msg'           => 'SUC',
							'err'           => 'NOERR',
							'col'           => 'NOTCOL',
						]);
					} else {
						return response()->json([
							'msg'           => 'FAIL',
							'err'           => 'APPLYFAIL',
							'col'           => 'NOTCOL',
						]);
					}
				} else {
					return response()->json([
						'msg'           => 'FAIL',
						'err'           => 'SYSERR',
						'col'           => 'NOTCOL',
					]);
				}
			}
		}
	}