<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018-04-23
	 * Time: 下午 5:13
	 */
	
	namespace App\Http\Controllers\PayController;
	
	use App\Model\Mt4Trades;
	use Illuminate\Http\Request;
	use App\Model\DepositRecordLog;
	use Redirect;
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	
	class PayCallBackController extends Abstract_Mt4service_Controller
	{
		//服务器异步
		/*{
			"amount":"10",
			"charset":"UTF-8",
			"notifyId":"180611145501099993940",
			"notifyTime":"2018-06-11 16:55:36",
			"notifyType":"TRADE_SUCCESS",
			"orderId":"180611147100659476696",
			"orderStatus":"SUCCESS",
			"outOrderId":"JJATY-20180611164615-8000013",
			"payTime":"2018-06-11 16:55:34",
			"signature":"19D83418E35ADDEDBC72D2DEC040CEC5"
		}*/
		public function deposit_notify_response_success (Request $request)
		{
			/*$_retrun_data['notifyId']		= $request->notifyId; //通知唯一识别码
			$_retrun_data['notifyTime']		= $request->notifyTime; //通 知 时 间 ： yyyy-MM-ddHH:mm:ss
			$_retrun_data['notifyType']		= $request->notifyType; //通知类型:TRADE_SUCCESS
			$_retrun_data['signature']    	= $request->signature; //签名
			$_retrun_data['orderId']		= $request->orderId; //平台订单号==商户号
			$_retrun_data['outOrderId']	    = $request->outOrderId; //商户订单号
			$_retrun_data['buyerId']	    = $request->buyerId; //买家用户 ID
			$_retrun_data['orderStatus']	= $request->orderStatus; //订单状态SUCCESS—支付成功PAYERROR— 支 付 失 败(其他原因，如银行返回失败)
			$_retrun_data['amount']		    = $request->amount / 100; //订单金额
			$_retrun_data['payTime']		= $request->payTime; //支 付 时 间 ： yyyy-MM-ddHH:mm:ss
			$_retrun_data['attach']	        = $request->attach; //附加数据，在查询 API 和支付通知中原样返回，可作为自定义参数使用。
			$_retrun_data['charset']		= $request->charset; //字符集 UTF-8*/
			
			$_retrun_data['outOrderId']		= $request->sdorderno;//商户订单号
			$_retrun_data['orderId']		= $request->customerid; //商户号
			$_retrun_data['serialNo']		= $request->sdpayno; //平台流水号
			$_retrun_data['amount']		    = $request->total_fee;
			$_retrun_data['orderStatus']	= $request->status; //订单状态
			$_retrun_data['sign']		    = $request->sign;
			$_retrun_data['channel']		= $request->paytype; //支付类型
			$_retrun_data['data']           = $request->all();
			
			$this->pay_debugfile($_retrun_data, '支付异步回调', 'PayPostRetrun');
			
			if ($_retrun_data['orderStatus'] == '1') {
				if ($this->check_orderno_in_mt4_status ($_retrun_data)) {
					echo 'SUCCESS';
				} else {
					$_retrun_data['orderStatus'] = 200;
					$_rs = $this->_exte_pay_success_sync_mt4_deposit($_retrun_data, $request);
				}
			} else {
				echo '错误的访问!';
			}
		}
		
		public function deposit_notify_response_success2 (Request $request)
		{
			/*$_retrun_data['notifyId']		= $request->notifyId; //通知唯一识别码
			$_retrun_data['notifyTime']		= $request->notifyTime; //通 知 时 间 ： yyyy-MM-ddHH:mm:ss
			$_retrun_data['notifyType']		= $request->notifyType; //通知类型:TRADE_SUCCESS
			$_retrun_data['signature']    	= $request->signature; //签名
			$_retrun_data['orderId']		= $request->orderId; //平台订单号==商户号
			$_retrun_data['outOrderId']	    = $request->outOrderId; //商户订单号
			$_retrun_data['buyerId']	    = $request->buyerId; //买家用户 ID
			$_retrun_data['orderStatus']	= $request->orderStatus; //订单状态SUCCESS—支付成功PAYERROR— 支 付 失 败(其他原因，如银行返回失败)
			$_retrun_data['amount']		    = $request->amount / 100; //订单金额
			$_retrun_data['payTime']		= $request->payTime; //支 付 时 间 ： yyyy-MM-ddHH:mm:ss
			$_retrun_data['attach']	        = $request->attach; //附加数据，在查询 API 和支付通知中原样返回，可作为自定义参数使用。
			$_retrun_data['charset']		= $request->charset; //字符集 UTF-8*/
			$_retrun_data['outOrderId']		= $request->order_id;//商户订单号
			$_retrun_data['orderId']		= $request->mch_id; //商户号
			$_retrun_data['serialNo']		= $request->up_order_id; //平台流水号
			$_retrun_data['amount']		    = $request->amount / 100;
			$_retrun_data['orderStatus']	= ($request->trade_state == 8) ? 200 : $request->trade_state;
			$_retrun_data['trade_type']		= $request->trade_type;
			$_retrun_data['trade_time']		= $request->trade_time;
			$_retrun_data['reserve1']		= $request->reserve1;
			$_retrun_data['msg']		    = $request->msg;
			//$_retrun_data['sign']		    = $request->sign;
			//$_retrun_data['channel']		= $request->channel;
			$_retrun_data['data']           = $request->all();
			
			$this->pay_debugfile($_retrun_data, '支付异步回调2', 'pay_debugfile2');
			
			if ($_retrun_data['orderStatus'] == 200) {
				if ($this->check_orderno_in_mt4_status ($_retrun_data)) {
					echo 'SUCCESS';
				} else {
					$_rs = $this->_exte_pay_success_sync_mt4_deposit($_retrun_data, $request);
				}
			} else {
				echo '错误的访问!';
			}
		}
		
		//页面回调
		public function deposit_return_response_success (Request $request)
		{
			$_retrun_data['outOrderId']		= $request->tradeNo; //支付单号，长度22位
			$_retrun_data['result']		    = $request->state; //支付状态 ONWAY: 待支付, SUCCESS: 支付成功, FAIL: 支付失败
			$_retrun_data['amount']		    = $request->amount / 100; //支付金额，单位分
			$_retrun_data['channel']		= $request->channel; //商户产品支付渠道
			$_retrun_data['sign']		    = $request->sign; //签名
			$_retrun_data['data']           = $request->all();
			
			$this->debugfile($_retrun_data, '支付页面回调', 'PayGetRetrun');
			if ($_retrun_data['result'] == 'SUCCESS') {
				echo 'SUCCESS';
				return redirect()->route('userIndex');
			} else {
				echo '无效的地址!';
			}
		}
		
		public function deposit_return_response_success2 (Request $request)
		{
			$_retrun_data['outOrderId']		= $request->order_id; //商户订单号
			$_retrun_data['result']		    = $request->code; //结果
			
			$this->debugfile($_retrun_data, '支付页面回调', 'debugfile2');
			if ($_retrun_data['result'] == 0) {
				echo 'SUCCESS';
				return redirect()->route('userIndex');
			} else {
				echo '无效的地址!';
			}
		}
		
		protected function check_orderno_in_mt4_status ($resp_data)
		{
			$_local = strripos($resp_data['outOrderId'], '-');
			$uid = substr($resp_data['outOrderId'], $_local + 1);
			$_table = $this->_exte_get_table_obj ($uid);
			
			$_rs = $_table::select('user_id', 'user_name', 'phone', 'email')->where('user_id', $uid)->where('voided' ,'1')->whereIn('user_status', array('0', '1', '2', '4'))->first();
			$trades_info = DepositRecordLog::select('dep_id', 'dep_amount', 'dep_act_amount', 'voided') //支付金额，充值金额
			->where('rec_crt_user', $uid)->where('dep_outTrande', $resp_data['outOrderId']) //订单号
			->where('dep_mchId', $resp_data['orderId']) //商户号
			->where('dep_amount', $resp_data['amount']) //支付金额
			->first();
			
			$cmt = $uid . '-' . $trades_info['dep_id'] . self::CZ;
			$chk_mt4 = Mt4Trades::where('LOGIN', $uid)->where('PROFIT', $trades_info['dep_act_amount'])->where('COMMENT', $cmt)->where('CMD', 6)->first();
			
			if ($chk_mt4 != null && $trades_info != null) {
				//已经入金成功
				$num = DepositRecordLog::where('dep_outTrande', $resp_data['orderId'])->where('rec_crt_user', $uid)->update([
					'voided'                        => '02', //MT4开始入金此账户
					'dep_mt4_id'					=> $chk_mt4['TICKET'], // TODO MT4入金成功后，将此单号记录在此表
					'rec_upd_date'                  => date('Y-m-d H:i:s'),
				]);
				
				$phone              = substr($_rs['phone'], (stripos($_rs['phone'], '-') + 1));
				$data['amt']        = $trades_info['dep_act_amount'];
				$data['user_id']    = $_rs['user_id'];
				$data['user_name']  = $_rs['user_name'];
				$_sendinfo          = $this->_exte_send_phone_notify($phone, 'deposit', $data);
				
				return true;
			} else {
				return false;
			}
		}
		
		protected function _exte_pay_success_sync_mt4_deposit ($_retrun_data, $request)
		{
			$outNo = $this->check_pay_order_trades_status($_retrun_data);
			
			if($outNo) {
				//支付成功，开始写入MT4
				$_local     = strripos($_retrun_data['outOrderId'], '-');
				$uid        = substr($_retrun_data['outOrderId'], $_local + 1);
				$_table     = $this->_exte_get_table_obj ($uid);
				
				$_rs = $_table::select('user_id', 'user_name', 'phone', 'email')->where('user_id', $uid)->where('voided' ,'1')->whereIn('user_status', array('0', '1', '2', '4'))->first();
				if($_rs == null) {
					echo '非法请求';
				} else {
					$trades_info = DepositRecordLog::select('dep_id', 'dep_amount', 'dep_act_amount', 'voided') //支付金额，充值金额
					->where('rec_crt_user', $uid)
						->where('dep_outTrande', $_retrun_data['outOrderId']) //订单号
						->where('dep_outChannelNo', $_retrun_data['serialNo']) //商户号
						->where('dep_amount', $_retrun_data['amount']) //支付金额
						->first();
					
					$cmt = $uid . '-' . $trades_info['dep_id'] . self::CZ;
					//查找MT4_TRADES订单表，查看是否已经存在该记录
					$chk_mt4 = Mt4Trades::where('LOGIN', $uid)->where('PROFIT', $trades_info['dep_act_amount'])->where('COMMENT', $cmt)->where('CMD', 6)->first();
					
					if ($chk_mt4 == null) {
						if($trades_info != null && $trades_info['voided'] == '01') {
							if ($_retrun_data['orderStatus'] == 200) {
								//一切正常 调用完成逻辑  订单号 金额， 开始同步MT4
								$sync_mt4 = $this->_exte_mt4_deposit_amount($uid, $trades_info['dep_act_amount'], $cmt);
								echo ($_retrun_data['orderId'] == "10952") ? 'success' : 'SUCCESS';
								if(is_array($sync_mt4) && $sync_mt4['ret'] == '0') {
									$phone              = substr($_rs['phone'], (stripos($_rs['phone'], '-') + 1));
									$data['amt']        = $trades_info['dep_act_amount'];
									$data['user_id']    = $_rs['user_id'];
									$data['user_name']  = $_rs['user_name'];
									$_sendinfo          = $this->_exte_send_phone_notify($phone, 'deposit', $data);
									
									$num = DepositRecordLog::where('dep_outTrande', $_retrun_data['outOrderId'])->where('rec_crt_user', $uid)->update([
										'voided'                        => '02', //MT4开始入金此账户
										'dep_outChannelNo'              => $_retrun_data['serialNo'], // 上游渠道订单号， 唯一
										'dep_mt4_id'					=> $sync_mt4['order'], //TODO MT4入金成功后，将此单号记录在此表
										'rec_upd_date'                  => date('Y-m-d H:i:s'),
									]);
								}
								return Redirect()->route('userIndex');
							}
						} else {
							echo '无效的订单或已经处理此订单!';
						}
					} else {
						$phone              = substr($_rs['phone'], (stripos($_rs['phone'], '-') + 1));
						$data['amt']        = $trades_info['dep_act_amount'];
						$data['user_id']    = $_rs['user_id'];
						$data['user_name']  = $_rs['user_name'];
						$_sendinfo          = $this->_exte_send_phone_notify($phone, 'deposit', $data);
						$num = DepositRecordLog::where('dep_outTrande', $_retrun_data['outOrderId'])->where('rec_crt_user', $uid)->update([
							'voided'                        => '02', //MT4开始入金此账户
							'dep_outChannelNo'              => $_retrun_data['serialNo'], // 上游渠道订单号， 唯一
							'dep_mt4_id'					=> $chk_mt4['TICKET'], // TODO MT4入金成功后，将此单号记录在此表
							'rec_upd_date'                  => date('Y-m-d H:i:s'),
						]);
					}
				}
			} else {
				return Redirect()->route('userIndex');
			}
		}
		
		protected function check_pay_order_trades_status($param) {
			
			//支付成功回调检查订单
			if($param['orderStatus'] == 200) {
				$pay_status = '02'; //支付状态 01 失败，02成功
			} else {
				$pay_status = '01'; //支付状态 01 失败，02成功
			}
			
			$_rs = DepositRecordLog::where('dep_outTrande', $param['outOrderId'])->where('dep_amount', $param['amount'])->first();
			
			if ($_rs != null && $_rs['voided'] == '01') {
				/*异步回调更新本地初始记录当前订单信息 voided = 01 未处理过，02 一级处理过次订单*/
				$num = DepositRecordLog::where('dep_outTrande', $param['outOrderId'])->update([
					'dep_status'                    => $pay_status, //订单状态 01 失败，02支付成功
					'dep_outChannelNo'              => $param['serialNo'], // 上游渠道订单号， 唯一
					'dep_transTime'                 => date('Y-m-d H:i:s'),
					'rec_upd_date'                  => date('Y-m-d H:i:s'),
				]);
				$is_exists_trades = true;
			} else {
				$is_exists_trades = false;
			}
			
			return $is_exists_trades;
		}
	}
