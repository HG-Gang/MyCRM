<?php
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/2/8
	 * Time: 14:59
	 */
	
	namespace App\Http\Controllers\CommonController;
	
	class NofityInfo
	{
		protected $_content             = '';
		
		protected $_gateway_url         = '';
		
		protected $_name                = '';
		
		protected $_pwd                 = '';
		
		protected $_targetId            = '';
		
		protected $_company             = 'pt';
		
		protected function gateway() {
			return $this->gateway_url = 'http://sms.1xinxi.cn/asmx/smsservice.aspx';
		}
		
		protected function name() {
			return $this->_name = 'yyc_liang@qq.com';
		}
		
		protected function pwd() {
			return $this->_pwd = '2E7590A3906F3ED537A7A9AB623E';
		}
		
		protected function main() {
			return $this->gateway () . '?name=' . $this->name() . '&pwd=' . $this->pwd();
		}
		
		protected function content($type, $data) {
			
			switch ($type) {
				case 'registerCode':
					$this->_content = '【帕达控股】您的短信验证码为：'. $data['code'] .'，请勿将验证码提供给他人。如非本人操作，请忽略此短信。';
				break;
				case 'registerSucInfo':
					$this->_content = '【帕达控股】您的账户已开通,账户号码:'. $data['user_id'] .',账户密码:'. $data['password'] .',您可使用此帐号登录客户平台。';
				break;
				case 'registerSucInfo2':
					$this->_content = '【帕达控股】您的账户已开通,账户号码:'. $data['user_id'] .'您可使用此帐号登录客户平台。';
				break;
				case 'modifyPassword':
				case 'changePassword':
					$this->_content = '【帕达控股】您正在进行重置密码操作,短信验证码为：'. $data['code'] .'，请勿将验证码提供给他人。如非本人操作，请联系客服。';
				break;
				case 'resetPassword':
					$this->_content = '【帕达控股】您的账户密码已被重置,重置后是:'. $data['password'] .'如非本人操作请及时联系客服。';
				break;
				case 'cancellAccount':
					$this->_content = '【帕达控股】您正在进行账户注销申请，短信验证码为：'. $data['code'] .'，请勿将验证码提供给他人。如非本人操作，请忽略此短信。';
				break;
				case 'cancelNotAllow':
					$this->_content = '【帕达控股】您的账户:'. $data['user_id'] .'销户申请已被拒绝，详情请咨询客服。';
				break;
				case 'cancelAllow':
					$this->_content = '【帕达控股】您的账户:'. $data['user_id'] .'销户申请已被接受，详情请咨询客服。';
				break;
				case 'deposit':
					$this->_content = '【帕达控股】尊敬的'. $data['user_name'] .'您的帐号'. $data['user_id'] .'于'. date('Y-m-d/H:i') .'入金'. $data['amt'] .'已成功,祝您交易愉快。';
				break;
				case 'widthdraw':
					$this->_content = '【帕达控股】尊敬的'. $data['user_name'] .'您的帐号'. $data['user_id'] .'于'. date('Y-m-d/H:i') .'出金'. $data['amt'] .'已成功,感谢您对本公司的支持。';
				break;
				case 'modifyPhone':
					$this->_content = '【帕达控股】您正在进行更改手机号操作，短信验证码为：' . $data['code'] . '，请勿将验证码提供给他人。如非本人操作，请忽略此短信。';
				break;
				case 'batchOperation':
					$this->_content = '【帕达控股】您的短信验证码为：' . $data['code'] . '，请勿将验证码提供给他人。如非本人操作，请忽略此短信。';
				break;
				case 'WhsExpZeroSms':
					$this->_content = '【帕达控股】尊敬的'. $data['user_name'] .'客户，您的帐号'. $data['user_id'] .'于'. $data['rec_upd_date'] .'账户余额已重置清零,祝您交易愉快。';
					break;
				case 'accept':
					$this->_content = '【帕达控股】您的账户:'. $data['user_id'] .'销户申请已被接受，账户已注销。';
					break;
				case 'refuse':
					$this->_content = '【帕达控股】您的账户:'. $data['user_id'] .'销户申请已被拒绝，详情请咨询客服。';
					break;
				case 'depositTH':
					$this->_content = '';
				break;
				case 'widthdrawTH':
					$this->_content = '【帕达控股】尊敬的'. $data['user_name'] .'您的帐号'. $data['user_id'] .'于'. date('Y-m-d/H:i') .'出金'. $data['amt'] .'被驳回,已返回您的账户,详情请咨询客服。';
				break;
				case 'changeBankCard':
					$this->_content = '【帕达控股】您正在进行银行卡变更操作,短信验证码为：'. $data['code'] .'，请勿将验证码提供给他人。如非本人操作，请联系客服。';
					break;
				case 'RightsSum':
					$this->_content = '【帕达控股】尊敬的'. $data['user_name'] .'您的帐号'. $data['user_id'] .'结算周期'. $data['sum_date'] . '已成功' . $data['sum_amt'] .',如有问题请咨询客服。';
					break;
				default:
					$this->_content = '';
				break;
			}
			
			return $this->_content;
		}
		
		public function sendCode($_targetId, $type, $data) {
			$_res = false;
			
			$_rs = $this->main () . '&content=' . $this->content($type, $data) . '&mobile=' . $_targetId . '&stime=&type=' . $this->_company . '&extno=2';
			
			//0,2017051709344875245519817,0,1,0,提交成功 [状态,发送编号,无效号码数,成功提交数,黑名单数和消息]
			$result = file_get_contents($_rs);
			$_status = explode(',', $result);
			if($_status[0] == '0' && $_status[5] == '提交成功') {
				return true;
			}
			
			return $_res;
		}
	}