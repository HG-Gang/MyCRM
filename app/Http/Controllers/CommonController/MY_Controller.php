<?php
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/1/23
	 * Time: 15:45
	 */
	
	namespace App\Http\Controllers\CommonController;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	
	class MY_Controller extends Controller
	{
		
		//定义系统常量
		const CZ                = '-CZ';
		const TH                = '-TH';
		const FY                = '-FY';
		const CJTH              = '-CJTH';
		const RJ                = '-RJ';
		const XY                = '-XY';
		const ZH                = '-ZH ';
		const QK                = '-QK';
		const RJTH              = '-RJTH ';
		const BCQL              = '-BCQL';
		const Adj               = '-Adj';
		const MODIFY_MT4_MAINPSW = 1; //修改主密码
		const MODIFY_MT4_TRANPSW = 2; //修改交易码
		const MODIFY_MT4_PHONE   = 3; //修改电话
		const MODIFY_MT4_EMAIL   = 4; //修改邮箱
		const MODIFY_MT4_LEVER   = 5; //修改杠杆
		const ACTIVE_STATUS = 1;
		const NO_ACTIVE_STATUS = 0;
		const OK = 200;
		const CREATED = 201;
		const ACCEPTED = 202;
		const NO_CONTENT = 204;      // 浏览器不会接受返回结果
		const RESET_CONTENT = 205;   // 浏览器不会接受返回结果
		const BAD_REQUEST = 400;
		const UNAUTHORIZED = 401;
		const FORBIDDEN = 403;
		const NOT_FOUND = 404;
		const METHOD_NOT_ALLOWED = 405;
		const NOT_ACCEPTABLE = 406;
		const REQUEST_TIME_OUT = 408;
		const CONFLICT = 409;
		const GONE = 410;
		const INTERNAL_SERVER_ERROR = 500;
		const NOT_IMPLEMENTED = 501;
		const BAD_GATEWAY = 502;
		const SERVICE_UNAVAILABLE = 503;
		const HTTP_VERSION_NOT_SUPPORTED = 505;
		const HTTP_UNAUTHORIZED_NOT_ACTIVE_MT4 = 401;
		const HTTP_ACTIVE_MT4_SUCCESS = 1000;
		const HTTP_ACTIVE_MT4_FAIL = 1001;
		
		public function _exte_per_page() {
			return '';
		}
		
		public function _exte_page_length() {
			return '';
		}
		
		protected function _exte_get_table_obj($_id) {
			return '';
		}
		
		protected function _exte_get_user_info ($_id) {
			return '';
		}
		
		protected function _exte_get_permit ($_username) {
			return '';
		}
		
		protected function _exte_get_system_param($_param_name) {
			return '';
		}
		
		protected function _exte_send_phone_notify($_targetId, $type, $data) {
			return '';
		}
		
		protected function _exte_send_email_notify ($_email_adr, $subject, $content, $email_type, $send_type) {
			return '';
		}
		
		protected function _exte_show_account_relationship_chain ($id, $loginId, $conn_char, $type)
		{
			return '';
		}
		
		protected function _exte_get_mylocal_html ($id, $funcname, $conn_char, $role)
		{
			return '';
		}
		
		protected function _exte_verify_phone($_phone) {
			return '';
		}
		
		protected function _exte_verify_email($_email) {
			return '';
		}
		
		protected function _exte_verify_idno($_idno) {
			return '';
		}
		
		protected function _exte_verify_intiveId($_id) {
			return '';
		}
		
		protected function debugfile($obj,$subject, $file="debugfile",$type="0"){
			/*function debugfile($obj,$file="debugfile",$type="0"){
			$tmp = date('Y-m-d H:i:s',time())."------".__URL__."/".ACTION_NAME."\r\n";
			$tmp .= ($type=="0")?json_encode($obj):$obj;
			@file_put_contents('App/Runtime/Logs/'.$file.'.txt', $tmp ."\r\n\r\n", FILE_APPEND);
		}*/
			$tmp = date('Y-m-d H:i:s',time())."------" . $subject . "\r\n";
			$tmp .= ($type=="0")?json_encode($obj):$obj;
			@file_put_contents(storage_path ('logs') . '/'.$file.'.txt', $tmp ."\r\n\r\n", FILE_APPEND);
		}
		
		protected function pay_debugfile($obj,$subject, $file="pay_debugfile",$type="0"){
			/*function debugfile($obj,$file="debugfile",$type="0"){
			$tmp = date('Y-m-d H:i:s',time())."------".__URL__."/".ACTION_NAME."\r\n";
			$tmp .= ($type=="0")?json_encode($obj):$obj;
			@file_put_contents('App/Runtime/Logs/'.$file.'.txt', $tmp ."\r\n\r\n", FILE_APPEND);
		}*/
			$tmp = date('Y-m-d H:i:s',time())."------" . $subject . "\r\n";
			$tmp .= ($type=="0")?json_encode($obj):$obj;
			@file_put_contents(storage_path ('logs') . '/'.$file.'.txt', $tmp ."\r\n\r\n", FILE_APPEND);
		}
	}