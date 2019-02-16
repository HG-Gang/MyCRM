<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018-04-23
	 * Time: 下午 5:49
	 */
	
	namespace App\Http\Controllers\PayController;
	
	use App\Model\DepositRecordLog;
	
	class PayConfigController
	{
		protected $_prefix                      = ''; //订单前缀
		
		protected $_prefix2                     = ''; //订单前缀
		
		protected $_order_no                    = '';
		
		protected $_serverUrl                   = ''; //网关地址
		
		protected $_serverUrl2                  = ''; //网关地址
		
		protected $_merId                       = ''; //商户号
		
		protected $_merId2                      = ''; //商户号
		
		protected $_key                         = ''; //密匙
		
		protected $_key2                        = ''; //密匙
		
		protected  $_returnUrl                  = '';
		
		protected  $_returnUrl2                 = '';
		
		protected  $_notifyUrl                  = '';
		
		protected  $_notifyUrl2                 = '';
		
		protected  $_bankNo                     = '';
		
		protected $_gateway                     = ''; //支付方式，默认网银
		
		protected $_deposit_url_otc         	= '';
		
		protected $_notifyUrl_otc         		= '';
		
		protected $_gateway_code                = [
				'ECITIC'                            => 'CTIB', //中信银行
				'CMBC'                              => 'CMSB', //民生银行 OK
				'SHB'                               => 'BOSH', //上海银行
				'BCCB'                              => 'BCCB', //北京银行
				'BOCO'                              => 'COMM', //交通银行
				'HXB'                               => 'HXBJ', //华夏银行 OK
				'CGB'                               => 'CGB', //广发银行
				'CIB'                               => 'IBCN', //兴业银行
				'SPDB'                              => 'SPDB', //浦发银行
				'CZBANK'                            => 'CZSB', //浙商银行 OK
				'NBCB'                              => 'NBCB', //宁波银行 OK
				'QLBCHINA'                          => 'QLBCHINA', //齐鲁银行x
				'HZBANK'                            => 'HZCB', //杭州银行x
				'NJCB'                              => 'NJCB', //南京银行x
				'TCCB'                              => 'TJCB', //天津银行x
				'HSBANK'                            => 'HSBANK', //徽商银行x
				'ABC'                               => 'LZBANK', //兰州银行x
			//	'POST'                              => 'QDCCB', //青岛银行x
				'GUILINBANK'                        => 'GUILINBANK', //广州农村商业银行x
				'GRCBANK'                           => 'GRCBANK', //延边农村商业银行x
				'PINGANBANK'                        => 'SZPA', //平安银行
				'CMBCHINA'                          => 'CMB', //招商银行
				'BGZCHINA'                          => 'BGZCHINA', //贵州银行x
				'BOC'                               => 'BOC', //中国银行
				'ABC'                               => 'ABC', //农业银行
				'CCB'                               => 'CCB', //建设银行 OK
				'ICBC'                              => 'ICBC', //工商银行
				'POST'                              => 'PSBC', //邮政银行
				'CEB'                               => 'CEB', //光大银行
				'BOHC'                              => 'BOHC', //渤海银行
				'SHRB'                              => 'SHRB', //上海农商银行
				'HSCB'                              => 'HSCB', //徽商银行
				'JSBK'                              => 'JSBK', //江苏银行
				'EBCL'                              => 'EBCL', //恒丰银行
				'BJRC'                              => 'BJRC', //北京农商银行
				'GZCU'                              => 'GZCU', //广州农商银行
				'SJBC'                              => 'SJBC', //盛京银行
				'CQRCB'                             => 'CQRCB', //重庆农商银行
				'BEA'                               => 'BEA', //东亚银行
				'BOCD'                              => 'BOCD', //成都银行
				'GZCB'                              => 'GZCB', //广州商业银行
				'BOWZ'                              => 'BOWZ', //温州银行
		];
		
		public function gateway() {
			return $this->_gateway = 'gateway';
		}
		
		public function orderNoPrefix () {
			return $this->_prefix = 'PDFX-';
		}
		
		public function orderNoPrefix2 () {
			return $this->_prefix2 = 'PDFX2-';
		}
		
		public function orderNoPrefix_otc () {
			return 'PADAOTC-';
		}
		
		public function orderNo() {
			return $this->_order_no = date('YmdHis');
		}
		
		public function returnUrl () {
			return $this->_returnUrl = route('user_deposit_return');
		}
		
		public function returnUrl2 () {
			return $this->_returnUrl2 = route('user_deposit_return2');
		}
		
		public function notifyUrl () {
			return $this->_notifyUrl = route('user_deposit_notfiy');
		}
		
		public function notifyUrl2 () {
			return $this->_notifyUrl2 = route('user_deposit_notfiy2');
		}
		
		public function notifyUrl_otc() {
			return $this->_notifyUrl_otc = route('user_deposit_notfiy_otc');
		}
		
		public function merId () {
			return $this->_merId = '10952';
		}
		
		public function merId2 () {
			return $this->_merId2 = '1806';
		}
		
		public function key () {
			return $this->_key = '5d975b1e0c7a8e6b467b841591cdd2dd40f9b968';
		}
		
		public function key2 () {
			return $this->_key2 = '4A#w6Rz';
		}
		
		public function serverUrl () {
			return $this->_serverUrl = 'http://pay.yundunbaopay.com/apisubmit';
		}
		
		public function serverUrl2 () {
			return $this->_serverUrl2 = '';
		}
		
		public function deposit_url_otc() {
			return $this->_deposit_url_otc = '';
		}
		
		public function bankCode($bankCode) {
			return $this->_gateway_code[$bankCode];
		}
		
		public function form_init ($param)
		{
			if ($param['pay_channel'] == 'tongdaoYI') {
				$data = array(
					'version'               =>'1.0',
					'customerid'            => $this->merId(), //商户平台的订单号，确保唯一性
					'sdorderno'             => $this->orderNoPrefix() . $this->orderNo(). '-' . $param['userId'],//商户订单号
					'total_fee'             => number_format($param['deposit_amt'], '2', '.', ''), //支付金额，单位分,无小数点
					'paytype'               => 'usdt',
					'returnurl'             => $this->returnUrl(),//支付结果同步通知地址
					'notifyurl'             => $this->notifyUrl(),//支付结果异步通知地址
					'remark'                => $param['userId'] . '-CZ',//商户展示名称
					'bankcode'              => '',
					'get_code'              => '0',
				);
				/*$data                  = array();
				$data['merid']         = $this->merId();//商户号
				$data['sn']            = $this->orderNo(). '-' . $param['userId'];//订单号
				$data['money']         = $param['deposit_amt']; //金额
				$data['subject']       = $param['userId'] . '-CZ';//商品描述
				$data['urlCallback']   = $this->notifyUrl();//异步通知地址
				$data['extra']         = $param['userId'] . '-CZ';//备注*/
				//2.支付参数签名
				$data['sign']          = md5('version='.$data['version'].'&customerid='.$data['customerid'].'&total_fee='.$data['total_fee'].'&sdorderno='.$data['sdorderno'].'&notifyurl='.$data['notifyurl'].'&returnurl='.$data['returnurl'].'&'.$this->key());
				//3.将支付参数转为字符串
			//	$paramsStr              = $this->ToUrlParams($data);
			} else if ($param['pay_channel2'] == 'tongdaoER') {
				$data = array(
					'mch_id'            => $this->merId2(),
					'pay_type'          => 'unipay.web',
					'order_id'          => $this->orderNoPrefix2() . $this->orderNo(). '-' . $param['userId'],//商户平台的订单号，确保唯一性
					'order_time'        => time(),
					'title'             => $param['userId'] . '-CZ',//商户展示名称
					'amount'            => $param['deposit_amt'] * 100, //支付金额，单位分,无小数点
					'bank_code'         => $this->bankCode($param['pay_gateway']),//$this->bankCode($param['pay_gateway']),//银行代号，参见银行代号列表,
					'page_url'          => $this->returnUrl2(),//支付结果同步通知地址
					'notify_url'        => $this->notifyUrl2(),//支付结果异步通知地址
					'request_ip'        => getClientId(),
					'reserve1'          => 'PB-083-662',
				);
				
				//$data = base64_encode(json_encode($order));
				/*if ($param['pay_channel'] == 'tongdaoYI') {
					$signTmp = 'appId=' . $order['appId'] . '&tradeNo=' . $order['tradeNo'] . '&amount=' . $order['amount'] . '&token=' . $this->key();
					$order['sign'] = md5($signTmp);
					$_t = array(
						'mchId'     => $this->merId(),
						'timestamp' => date('YmdHis') .rand (100,999),
						'data'      => $data
					);
				} else if ($param['pay_channel2'] == 'tongdaoER') {
					$_t = array(
						'mchId'     => $this->merId2(),
						'timestamp' => date('YmdHis') .rand (100,999),
						'data'      => $data
					);*/
					
					ksort($data);
					$_str = "amount=" . $data['amount'] . "&mch_id=" .$data['mch_id'] . '&order_id=' . $data['order_id'] . '&order_time=' . $data['order_time'] . '&pay_type=' .$data['pay_type'] . '&title=' . $data['title'] . '&key=' . $this->key2();
					
					$data['sign'] = md5($_str);
				//}
				
				/*if ($param['pay_channel'] == 'tongdaoYI') {
					//$_str .= "key=" . $this->key();
				} else if ($param['pay_channel2'] == 'tongdaoER') {
					$_str .= "key=" . $this->key2();
					$sign = strtoupper(md5($_str));
					$_t['signature']    = $sign;
				}*/
			}
			
			
			//dd($_t);exit();
			//本地初始记录当前订单信息
			if ($param['pay_channel'] == 'tongdaoYI') {
				$num = DepositRecordLog::create([
					'dep_mchId'                     => $this->merId(), //商户号
					'dep_channel'                   => ($param['pay_channel'] == 'tongdaoYI') ? 'YunDunBao' : 'Other', //支付类型
					'dep_body'                      => $param['userId'] . '-CZ',  //备注
					'dep_outTrande'                 => $data['sdorderno'], //订单号，唯一
					'dep_amount'                    => $param['deposit_amt'], //实际支付
					'dep_act_amount'                => $param['deposit_act_amt'], //实际存款
					'dep_status'                    => '01', //默认支付失败
					'voided'                        => '01', //默认MT4没有处理次订单
					'rec_crt_user'                  => $param['userId'],
					'rec_upd_user'                  => $param['userId'],
					'rec_crt_date'                  => date('Y-m-d H:i:s'),
					'rec_upd_date'                  => date('Y-m-d H:i:s'),
				]);
			} else if ($param['pay_channel2'] == 'tongdaoER') {
				$num = DepositRecordLog::create([
					'dep_mchId'                     => $this->merId2(), //商户号
					'dep_channel'                   => ($param['pay_channel'] == 'tongdaoER') ? 'UNPAY2' : 'QICAIHONG', //支付类型
					'dep_body'                      => $param['userId'] . '-CZ',  //备注
					'dep_outTrande'                 => $data['order_id'], //订单号，唯一
					'dep_amount'                    => $param['deposit_amt'], //实际支付
					'dep_act_amount'                => $param['deposit_act_amt'], //实际存款
					'dep_status'                    => '01', //默认支付失败
					'voided'                        => '01', //默认MT4没有处理次订单
					'rec_crt_user'                  => $param['userId'],
					'rec_upd_user'                  => $param['userId'],
					'rec_crt_date'                  => date('Y-m-d H:i:s'),
					'rec_upd_date'                  => date('Y-m-d H:i:s'),
				]);
			}
			
			if (!$num) {
				echo "系统繁忙,请稍后再操作.";
			} else {
				//4.发送支付请求
				
				if ($param['pay_channel'] == 'tongdaoYI') {
					$this->deposit_request_post($data, $this->serverUrl());
					//header("Content-type:text/html;charset=utf-8");
					//header("Location:".$this->serverUrl().'?'.$paramsStr);exit;
				} else if ($param['pay_channel2'] == 'tongdaoER') {
					//$s = json_decode(base64_decode($data['data']));
					//header("Location:" . $s->url)
					$this->deposit_request_post($data, $this->serverUrl2());
				}
			}
			
		}
		
		protected function deposit_request_post($data, $url)
		{
			
			/*$header[] = 'Content-Type:application/json;charset=UTF-8';
			$request = curl_init();
			curl_setopt($request, CURLOPT_URL, $this->serverUrl());
			curl_setopt($request,CURLOPT_HTTPHEADER, $header);
			curl_setopt($request, CURLOPT_HEADER, false);
			curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($request, CURLOPT_POST, true);
			curl_setopt($request, CURLOPT_POSTFIELDS, json_encode($data));
			
			$response = curl_exec($request);
			curl_close($request);
			$data = json_decode($response, true);
			if ($data['status'] == 'FAIL') {
				echo $data['message'];
				exit();
			}
			$s = json_decode(base64_decode($data['data']));
			
			header("Location:" . $s->url);
			exit();*/
			//dd($data);exit();
			$str = '<form action="' . $url .'" method="post" name="formPost">';
			foreach ($data as $k => $v) {
				$str .= '<input type="hidden" name="' . $k . '" value="' . $v . '">';
			}
			$str .= '</form>';
			//$str .= $str . "<script>document.forms['formPost'].submit();</script>";
			//dd($str);exit();
			echo $str . "<script>document.forms['formPost'].submit();</script>";
			//dd($str);
//			if ($rs->code == 0) {
//				header("Location:" . $rs->data);
//				exit();
//			} else {
//				echo $rs->msg;
//			}
		}
		
		protected function deposit_request_post2($data)
		{
			
			//$header[] = 'Content-Type:application/json;charset=UTF-8';
			$request = curl_init();
			curl_setopt($request, CURLOPT_URL, $this->serverUrl2());
			//curl_setopt($request,CURLOPT_HTTPHEADER, $header);
			curl_setopt($request, CURLOPT_HEADER, false);
			curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($request, CURLOPT_POST, true);
			curl_setopt($request, CURLOPT_POSTFIELDS, $data);
			
			$response = curl_exec($request);
			curl_close($request);
			$rs = json_decode($response, true);
			
			if ($rs['code'] != 0) {
				echo $rs['msg'];
				exit();
			}
			//$s = json_decode(base64_decode($rs['data']));
			header("Location:" . $rs['data']);
			exit();
		}
		
		/**
		 * 将参数数组签名
		 * @param  array $array [待签名的数组]
		 * @param  array $key   [商户密匙]
		 * @return string       [签名字符串]
		 */
		protected function SignArray($array,$key){
			ksort($array);//排序
			$blankStr = $this->ToUrlParams($array);
			$sign = md5($blankStr.$key);
			return $sign;
		}
		
		/**
		 * 将参数拼接成字符串
		 * @param  array $array [待签名的数组]
		 * @return string       [拼接完成的字符串]
		 */
		protected function ToUrlParams($array){
			$buff = "";
			foreach ($array as $k => $v){
				if(!is_array($v)){
					$buff .= $k . "=" . $v . "&";
				}
			}
			$buff = trim($buff, "&");
			return $buff;
		}
		
		/*
		 * OTC 充值入口
		 * */
		public function form_init_otc($param)
		{
			$orderId = $this->orderNoPrefix_otc() . $this->orderNo(). '-' . $param['userId'];//商户平台的订单号，确保唯一性
			$data = collect([
					'playerId'			=> $param['playerId'],
					'orderId'			=> $orderId,
					'callback'			=> $this->notifyUrl_otc(),//支付结果异步通知地址
			]);
			
			//本地初始记录当前订单信息
			$num = DepositRecordLog::create([
					'dep_mchId'                     => 'OTCOTC', //商户号
					'dep_channel'                   => 'OTCPAY', //支付类型
					'dep_body'                      => $param['userId'] . '-CZ',  //备注
					'dep_outTrande'                 => $orderId, //订单号，唯一
					'dep_amount'                    => $param['deposit_amt'], //实际支付
					'dep_act_amount'                => $param['deposit_act_amt'], //实际存款
					'dep_amt_rate'					=> $param['deposit_rate'], //当前存款汇率
					'dep_status'                    => '01', //默认支付失败
					'voided'                        => '01', //默认MT4没有处理次订单
					'rec_crt_user'                  => $param['userId'],
					'rec_upd_user'                  => $param['userId'],
					'rec_crt_date'                  => date('Y-m-d H:i:s'),
					'rec_upd_date'                  => date('Y-m-d H:i:s'),
			]);
			
			if (!$num) {
				echo "系统繁忙,请稍后再操作.";
			} else {
				//4.发送支付请求
				$ret = $this->otc_pay_request($data->toJson());
				
				if($ret['flag'] == 'fail') {
					echo '呀！不小心出错了, 请重试或者联系客服.';
				} else if($ret['flag'] == 'success') {
					header("Location:".$ret['url']);
					exit;
				}
			}
		}
		
		protected function otc_pay_request($data)
		{
			$client = new \GuzzleHttp\Client();
			$request = $client->post($this->deposit_url_otc(), ['body' => $data]);
			$response = $request->getBody();
			return json_decode($response, true);
		}
	}
