<?php
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/1/23
	 * Time: 15:04
	 */
	
	namespace App\Http\Controllers\CommonController;
	
	use App\Http\Controllers\CommonController\MY_Controller;
	use App\Http\Controllers\CommonController\NofityInfo;
	use Illuminate\Support\Facades\Storage;
	use App\Model\NewsList;
	use Illuminate\Support\Facades\Mail;
	use Illuminate\Http\Request;
	use App\Model\Agents;
	use App\Model\User;
	use App\Model\Mt4Users;
	use App\Model\Mt4Trades;
	use App\Model\DepositRecordLog;
	use App\Model\DrawRecordLog;
	use APP\Model\Admin;
	use App\Model\UserImg;
	use App\Model\SystemLoginLog;
	use App\Model\UserGroup;
	use App\Model\SystemParam;
	use Excel;
	
	abstract class Abstract_Basic_Controller extends MY_Controller
	{
		
		protected $_user                = 'NULL';
		
		protected $_auser               = 'NULL';
		
		protected $_page                = '';
		
		protected $_pageSize            = '';
		
		protected $_offset              = '';
		
		protected $_email_from          = '';
		
		protected $_notify              = '';
		
		//普通客户和代理商直接的UserId区别，代理编号 637001-637999， 客户编号 40370001-40379999
		protected $_userIdIndex        	= '40370001';
		
		protected $_agentsIdIndex       = 637001;
		
		public function __construct(Request $request, NofityInfo $nofityInfo) {
			
			$this->_page                = $request->page;
			$this->_pageSize            = $request->rows;
			$this->_offset              = ($this->_page - 1) * $this->_pageSize;
			$this->_user                = $request->session ()->get ('suser');
			$this->_auser               = $request->session ()->get ('auser');
			$this->_notify              = $nofityInfo;
			
		}
		
		//env('MAIL_USERNAME');
		protected function _exte_email_from ()
		{
			return $this->_email_from = env('MAIL_USERNAME');
		}
		
		protected function _exte_get_table_obj($_id) {
			//todo 正式上线请确定区分 代理商和普通客户ID
			// 1000001
			if ((int)$_id >= $this->_userIdIndex) {
				$_table = new User();
			} else {
				$_table = new Agents();
			}
			
			return $_table;
		}
		
		protected function _exte_get_user_info ($_id)
		{
			$_table = $this->_exte_get_table_obj ($_id);
			
			return $_table->_user_info($_id);
		}
		
		protected function _exte_get_permit ($_username)
		{
			return Admin::select('user_name', 'phone_no', 'user_satus', 'voided', 'rec_crt_user')
				->where('user_name', $_username)->where('voided', '1')->get()->toArray();
		}
		
		protected function _exte_get_system_param($_param_name) {
			return SystemParam::where('para_name', strtoupper($_param_name))->where('voided', '1')->get()->toArray();
		}
		
		protected function _exte_send_phone_notify ($_targetId, $type, $data)
		{
			return $this->_notify->sendCode ($_targetId, $type, $data);
		}
		
		protected function _exte_send_email_notify ($_email_adr, $subject, $content, $email_type, $send_type)
		{
			/*
         * int 类型 为0 为发送失败,其他为ok
         * int 不为0 的时候， 数字 表示发送成功收到邮件人的个数
         * */
			$_rs = false;
			
			if ($email_type == 'registerCode') {
				$num = Mail::send('mail.register_mail', ['vcode'=>$content], function ($message) use ($_email_adr, $subject) {
					$message->subject($subject);
					$message->from($this->_exte_email_from(), '帕达控股');
					$message->to($_email_adr);
				});
			} else if ($email_type == 'registerSuc') {
				$num = Mail::send('mail.register_success_mail', ['user'=>$content, 'send_type' => $send_type], function ($message) use ($_email_adr, $subject) {
					$message->subject($subject);
					$message->from($this->_exte_email_from(), '帕达控股');
					$message->to($_email_adr);
				});
			}
			
			return ($num != 0) ? true : $_rs;
			
		}
		
		/*
		 * id[name]->id[name]
		 * id-id-id
		 * role = agents
		 * $this->_user
		 * */
		protected function _exte_show_account_relationship_chain ($id, $conn_char, $conn_type, $role) {
			
			global $_rala;
			
			$_table = $this->_exte_get_table_obj ($id);
			
			$_info = $_table::select('user_id', 'user_name', 'parent_id')->where('user_id', $id)
				->where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))->get()->toArray();
			
			if (count ($_info) > 0) {
				if ($conn_type == 'idname') {
					$_rala[] = $_info[0]['user_name'] . '[' . $_info[0]['user_id'] . ']';
				} else if ($conn_type == 'id') {
					$_rala[] = $_info[0]['user_id'];
				}
				
				if ($role == 'agents' || $role == 'user') {
					if ($_info[0]['parent_id'] != 0 && $_info[0]['user_id'] != $this->_user['user_id']) {
						self::_exte_show_account_relationship_chain ($_info[0]['parent_id'], $conn_char, $conn_type, $role);
					}
					
					krsort($_rala);
					return implode ($conn_char, $_rala);
				} else if ($role == 'admin') {
					if (count ($_info) > 0) {
						if ($_info[0]['parent_id'] != 0) {
							self::_exte_show_account_relationship_chain ($_info[0]['parent_id'], $conn_char, $conn_type, $role);
						}
					}
					
					krsort($_rala);
					return implode ($conn_char, $_rala);
				}
			} else {
				if (count ($_rala) > 0) {
					krsort($_rala);
					return implode ($conn_char, $_rala);
				} else {
					return $_rala;
				}
			}
		}
		
		protected function _exte_get_mylocal_html ($id, $funcname, $conn_char, $role)
		{
			global $mylocalHtml;
			$_table = $this->_exte_get_table_obj ($id);
			
			$_rs = $_table::select('user_id', 'user_name', 'parent_id')->where('user_id', $id)->where('voided', '1')->whereIn('user_status', array ('0', '1', '2', '4'))->get()->toArray();
			
			if (count ($_rs) > 0) {
				$mylocalHtml[] = '<span><a href="javascript:void(0)" onclick="' . $funcname . '(' . $id . ')">' . $_rs[0]['user_name'] . '[' . $_rs[0]['user_id'] . ']' . '</a></span>';
				if ($role == 'agents' || $role == 'user') {
					if ($_rs[0]['parent_id'] != 0 && $_rs[0]['user_id'] != $this->_user['user_id']) {
						self::_exte_get_mylocal_html ($_rs[0]['parent_id'], $funcname, $conn_char, $role);
					}
					
					krsort ($mylocalHtml);
					return implode ($conn_char, $mylocalHtml);
				} else if ($role == 'admin') {
					if ($_rs[0]['parent_id'] != 0) {
						self::_exte_get_mylocal_html ($_rs[0]['parent_id'], $funcname, $conn_char, $role);
					}
					
					krsort ($mylocalHtml);
					return implode ($conn_char, $mylocalHtml);
				}
			} else {
				if (count ($_rs) > 0) {
					krsort($mylocalHtml);
					return implode ($conn_char, $mylocalHtml);
				} else {
					return $_rs;
				}
			}
		}
		
		/*更新当前用户最后等时间*/
		protected function _exte_update_user_last_logintime ($_id)
		{
			$_table = $this->_exte_get_table_obj ($_id);
			$loginIp = $this->_exte_get_user_loginIp ();
			$_rs = SystemLoginLog::where('login_id', $_id)->where('voided', '1')->orderBy('login_date', 'desc')->first();
			$rs = SystemLoginLog::create([
				'login_id'          => $_id,
				'login_ip'          => $loginIp,
				'login_id_desc'     => $this->_exte_get_user_loginIpCity ($loginIp),
				'login_date'        => date('Y-m-d H:i:s'),
				'voided'            => '1',
			]);
			
			$upd = $_table::whereIn('voided', array ('1', '2'))->whereIn('user_status', array('0', '1', '2', '4'))->find($_id)
				->update([
					'last_logindate'        => ($_rs != null) ? $_rs['login_date'] : date('Y-m-d H:i:s'),
				]);
			
			return $upd;
		}
		
		protected function _exte_custom_update_user_voided ($_id)
		{
			$_table = $this->_exte_get_table_obj ($_id);
			
			return $_table->_update_user_voided($_id);
		}
		
		protected function _exte_get_user_role ($uid)
		{
			return ($uid >= $this->_userIdIndex) ? 'User' : 'Agents';
		}
		
		protected function _exte_get_user_loginIp ()
		{
			//获取当前用户IP
			if (getenv('HTTP_CLIENT_IP')) {
				$ip = getenv('HTTP_CLIENT_IP');
			} elseif (getenv('HTTP_X_FORWARDED_FOR')) {
				$ip = getenv('HTTP_X_FORWARDED_FOR');
			} elseif (getenv('HTTP_X_FORWARDED')) {
				$ip = getenv('HTTP_X_FORWARDED');
			} elseif (getenv('HTTP_FORWARDED_FOR')) {
				$ip = getenv('HTTP_FORWARDED_FOR');
			} elseif (getenv('HTTP_FORWARDED')) {
				$ip = getenv('HTTP_FORWARDED');
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			
			return $ip;
		}
		
		protected function _exte_get_user_loginIpCity ($ip)
		{
			
			$userip = $ip;
			//IP数据库路径，这里用的是QQ IP数据库 20110405 纯真版
			$dat_path = $_SERVER['DOCUMENT_ROOT'] . '/city/ipaddress/qqwry.dat';
			
			//判断IP地址是否有效
			if (!preg_match("^([0-9]{1,3}.){3}[0-9]{1,3}$^", $userip)) {
				return 'IP Address Invalid';
			}
			
			//打开IP数据库
			if (!$fd = @fopen($dat_path, 'rb')) {
				return 'IP data file not exists or access denied';
			}
			
			//explode函数分解IP地址，运算得出整数形结果
			$userip = explode('.', $userip);
			$useripNum = $userip[0] * 16777216 + $userip[1] * 65536 + $userip[2] * 256 + $userip[3];
			
			//获取IP地址索引开始和结束位置
			$DataBegin = fread($fd, 4);
			$DataEnd = fread($fd, 4);
			$useripbegin = implode('', unpack('L', $DataBegin));
			if ($useripbegin < 0) $useripbegin += pow(2, 32);
				$useripend = implode('', unpack('L', $DataEnd));
			if ($useripend < 0) $useripend += pow(2, 32);
				$useripAllNum = ($useripend - $useripbegin) / 7 + 1;
			
			$BeginNum = 0;
			$EndNum = $useripAllNum;
			$useripAddr2 = '';
			$useripAddr1 = '';
			//使用二分查找法从索引记录中搜索匹配的IP地址记录
			while (@$userip1num > @$useripNum || @$userip2num < @$useripNum) {
				$Middle = intval(($EndNum + $BeginNum) / 2);
				
				//偏移指针到索引位置读取4个字节
				fseek($fd, $useripbegin + 7 * $Middle);
				$useripData1 = fread($fd, 4);
				if (strlen($useripData1) < 4) {
					fclose($fd);
					return 'File Error';
				}
				//提取出来的数据转换成长整形，如果数据是负数则加上2的32次幂
				$userip1num = implode('', unpack('L', $useripData1));
				if ($userip1num < 0) $userip1num += pow(2, 32);
				
				//提取的长整型数大于我们IP地址则修改结束位置进行下一次循环
				if ($userip1num > $useripNum) {
					$EndNum = $Middle;
					continue;
				}
				
				//取完上一个索引后取下一个索引
				$DataSeek = fread($fd, 3);
				if (strlen($DataSeek) < 3) {
					fclose($fd);
					return 'File Error';
				}
				$DataSeek = implode('', unpack('L', $DataSeek . chr(0)));
				fseek($fd, $DataSeek);
				$useripData2 = fread($fd, 4);
				if (strlen($useripData2) < 4) {
					fclose($fd);
					return 'File Error';
				}
				$userip2num = implode('', unpack('L', $useripData2));
				if ($userip2num < 0) $userip2num += pow(2, 32);
				
				//找不到IP地址对应城市
				if ($userip2num < $useripNum) {
					if ($Middle == $BeginNum) {
						fclose($fd);
						return 'No Data';
					}
					$BeginNum = $Middle;
				}
			}
			
			$useripFlag = fread($fd, 1);
			if ($useripFlag == chr(1)) {
				$useripSeek = fread($fd, 3);
				if (strlen($useripSeek) < 3) {
					fclose($fd);
					return 'System Error';
				}
				$useripSeek = implode('', unpack('L', $useripSeek . chr(0)));
				fseek($fd, $useripSeek);
				$useripFlag = fread($fd, 1);
			}
			
			if ($useripFlag == chr(2)) {
				$AddrSeek = fread($fd, 3);
				if (strlen($AddrSeek) < 3) {
					fclose($fd);
					return 'System Error';
				}
				$useripFlag = fread($fd, 1);
				if ($useripFlag == chr(2)) {
					$AddrSeek2 = fread($fd, 3);
					if (strlen($AddrSeek2) < 3) {
						fclose($fd);
						return 'System Error';
					}
					$AddrSeek2 = implode('', unpack('L', $AddrSeek2 . chr(0)));
					fseek($fd, $AddrSeek2);
				} else {
					fseek($fd, -1, SEEK_CUR);
				}
				
				while (($char = fread($fd, 1)) != chr(0))
					@$useripAddr2 .= $char;
				$AddrSeek = implode('', unpack('L', $AddrSeek . chr(0)));
				fseek($fd, $AddrSeek);
				while (($char = fread($fd, 1)) != chr(0))
					@$useripAddr1 .= $char;
			} else {
				fseek($fd, -1, SEEK_CUR);
				while (($char = fread($fd, 1)) != chr(0))
					$useripAddr1 .= $char;
				
				$useripFlag = fread($fd, 1);
				if ($useripFlag == chr(2)) {
					$AddrSeek2 = fread($fd, 3);
					if (strlen($AddrSeek2) < 3) {
						fclose($fd);
						return 'System Error';
					}
					$AddrSeek2 = implode('', unpack('L', $AddrSeek2 . chr(0)));
					fseek($fd, $AddrSeek2);
				} else {
					fseek($fd, -1, SEEK_CUR);
				}
				while (($char = fread($fd, 1)) != chr(0)) {
					@$useripAddr2 .= $char;
				}
			}
			fclose($fd);
			//返回IP地址对应的城市结果
			if (preg_match('/http/i', $useripAddr2)) {
				$useripAddr2 = '';
			}
			$useripaddr = "$useripAddr1 $useripAddr2";
			$useripaddr = preg_replace('/CZ88.Net/is', '', $useripaddr);
			$useripaddr = preg_replace('/^s*/is', '', $useripaddr);
			$useripaddr = preg_replace('/s*$/is', '', $useripaddr);
			if (preg_match('/http/i', $useripaddr) || $useripaddr == '') {
				$useripaddr = 'No Data';
			}
			
			$address = iconv('GB2312', 'UTF-8', $useripaddr);
			
			return $address;
		}
		
		protected function _exte_get_user_browse ()
		{
			//读取浏览器信息
			function get_broswer(){
				$sys = $_SERVER['HTTP_USER_AGENT'];  //获取用户代理字符串
				if (stripos($sys, "Firefox/") > 0) {
					preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
					$exp[0] = "Firefox";
					$exp[1] = $b[1];  //获取火狐浏览器的版本号
				} elseif (stripos($sys, "Maxthon") > 0) {
					preg_match("/Maxthon\/([\d\.]+)/", $sys, $aoyou);
					$exp[0] = "傲游";
					$exp[1] = $aoyou[1];
				} elseif (stripos($sys, "MSIE") > 0) {
					preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
					$exp[0] = "IE";
					$exp[1] = $ie[1];  //获取IE的版本号
				} elseif (stripos($sys, "OPR") > 0) {
					preg_match("/OPR\/([\d\.]+)/", $sys, $opera);
					$exp[0] = "Opera";
					$exp[1] = $opera[1];
				} elseif(stripos($sys, "Edge") > 0) {
					//win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
					preg_match("/Edge\/([\d\.]+)/", $sys, $Edge);
					$exp[0] = "Edge";
					$exp[1] = $Edge[1];
				} elseif (stripos($sys, "Chrome") > 0) {
					preg_match("/Chrome\/([\d\.]+)/", $sys, $google);
					$exp[0] = "Chrome";
					$exp[1] = $google[1];  //获取google chrome的版本号
				} elseif(stripos($sys,'rv:')>0 && stripos($sys,'Gecko')>0){
					preg_match("/rv:([\d\.]+)/", $sys, $IE);
					$exp[0] = "IE";
					$exp[1] = $IE[1];
				}else {
					$exp[0] = "未知浏览器";
					$exp[1] = "";
				}
				
				return $exp;
				//return $exp[0].'('.$exp[1].')';
			}
			
		}
		
		protected function _exte_get_agentsTotal ($_id)
		{
			$_count = 0;
			
			if ($_id < $this->_userIdIndex) {
				$_count  = Agents::_get_agentsTotal($_id);
			}
			
			return $_count;
		}
		
		protected function _exte_get_accountTotal ($_id)
		{
			$_count = 0;
			
			if ($_id >= $this->_userIdIndex) {
				$_count  = User::_get_accountTotal($_id);
			}
			
			return $_count;
		}
		
		protected function _exte_get_depositTotal ($_id)
		{
			return mt4Trades::_get_depositTotal($_id);
		}
		
		protected function _exte_get_withdrawTotal ($_id)
		{
			return mt4Trades::_get_withdrawTotal($_id);
		}
		
		protected function _exte_get_closeTotal ($_id)
		{
			return mt4Trades::_get_closeTotal($_id);
		}
		
		protected function _exte_get_openTotal ($_id)
		{
			return mt4Trades::_get_openTotal($_id);
		}
		
		protected function _exte_get_ytdDepTotal ($_id) {
			return DepositRecordLog::_get_ytdDepTotal($_id);
		}
		
		protected function _exte_get_ytdDrawTotal ($_id) {
			return DrawRecordLog::_get_ytdDrawTotal($_id);
		}
		
		protected function _exte_get_hotsNews ()
		{
			return NewsList::_get_hotsNews();
		}
		
		protected function _exte_verify_phone ($_phone)
		{
			$_rs = false;
			
			$_ag_info = Agents::where('phone', 'like', $_phone)->whereIn('voided', array ('1', '2'))->whereIn('user_status', array ('0', '1', '2', '4'))->first();
			$_usr_info = User::where('phone', 'like', $_phone)->whereIn('voided', array ('1', '2'))->whereIn('user_status', array ('0', '1', '2', '4'))->first();
			
			return ($_ag_info != null || $_usr_info != null) ? true : $_rs;
		}
		
		protected function _exte_verify_email ($_email)
		{
			$_rs = false;
			
			$_ag_info = Agents::where('email', 'like', $_email)->whereIn('voided', array ('1', '2'))->whereIn('user_status', array ('0', '1', '2', '4'))->first();
			$_usr_info = User::where('email', 'like', $_email)->whereIn('voided', array ('1', '2'))->whereIn('user_status', array ('0', '1', '2', '4'))->first();
			
			return ($_ag_info != null || $_usr_info != null) ? true : $_rs;
		
		}
		
		protected function _exte_verify_idno ($_idno)
		{
			
			$_rs = false;
			
			$_ag_info = Agents::where('IDcard_no', 'like', $_idno)->whereIn('voided', array ('1', '2'))->whereIn('user_status', array ('0', '1', '2', '4'))->first();
			$_usr_info = User::where('IDcard_no', 'like', $_idno)->whereIn('voided', array ('1', '2'))->whereIn('user_status', array ('0', '1', '2', '4'))->first();
			
			return ($_ag_info != null || $_usr_info != null) ? true : $_rs;
		}
		
		protected function _exte_verify_intiveId ($_id)
		{
			$_rs = false;
			
			$_ag_info = Agents::where('user_id', $_id)->whereIn('voided', array ('1', '2'))->whereIn('user_status', array ('0', '1', '2', '4'))->first();
			
			return ($_ag_info != null) ? false : true;
		}
		/*
		* 方法 isDate
		* 功能 判断日期格式是否正确
		* 参数 $str 日期字符串
		$format 日期格式
		* 返回 无
		*/
		protected function _exte_is_Date($str,$format='Y-m-d'){
			
			$unixTime_1=strtotime($str);
			
			if(!is_numeric($unixTime_1)) return false; //如果不是数字格式，则直接返回
			
			$checkDate=date($format,$unixTime_1);
			$unixTime_2=strtotime($checkDate);
			
			if($unixTime_1 == $unixTime_2){
				return true;
			} else{
				return false;
			}
		}
		
		//身份证图片上传
		protected function _exte_upload_file_idCardPhoto($request)
		{
			
			// IdCardUpload, BankUpload
			$uploadType = $request->uploadType;
			$file_ext = array('JPG', 'JPEG', 'jpg', 'jpeg', 'PNG', 'png');
			//身份证上传
			$Idphoto1 = $request->Idphoto1; //正面
			$Idphoto2 = $request->Idphoto2; //反面
			$maxwidth="1056"; //设置图片的最大宽度
			$maxheight="500"; //设置图片的最大高度
			
			$Idphoto1_name = $Idphoto1->getClientOriginalName(); // 文件原名
			$Idphoto1_ext = $Idphoto1->getClientOriginalExtension(); // 文件扩展名
			$Idphoto1_path = $Idphoto1->getRealPath(); // 临时文件的绝对路径
			$Idphoto1_size = $Idphoto1->getClientSize(); //文件大小
			$Idphoto1_mimeType = getimagesize($_FILES['Idphoto1']['tmp_name'])['mime']; //原图片的真实扩展名
			$Idphoto1_upname = date('YmdHis') . '-' . $this->_user['user_id'] . '-IdCard-pos.' . $Idphoto1_ext;
			
			$Idphoto2_name = $Idphoto2->getClientOriginalName(); // 文件原名
			$Idphoto2_ext = $Idphoto2->getClientOriginalExtension(); // 文件扩展名
			$Idphoto2_path = $Idphoto2->getRealPath(); // 临时文件的绝对路径
			$Idphoto2_size = $Idphoto2->getClientSize(); //文件大小
			$Idphoto2_mimeType = getimagesize($_FILES['Idphoto2']['tmp_name'])['mime']; //原图片的真实扩展名
			$Idphoto2_upname = date('YmdHis') . '-' . $this->_user['user_id'] . '-IdCard-oppos.' . $Idphoto2_ext;
			
			//检查图片大小， 格式
			if (($Idphoto1_size / 1024) > 2048) {
				//正面图片大于2M
				return array (
					'msg'       => 'FAIL',
					'col'       => 'Idphoto1',
					'err'       => 'POSOVERSIZE1',
				);
			} else if (($Idphoto2_size / 1024) > 2048) {
				//反面图片大于2M
				return array (
					'msg'       => 'FAIL',
					'col'       => 'Idphoto2',
					'err'       => 'POSOVERSIZE2',
				);
			} else if(in_array($Idphoto1_ext, $file_ext, true) !== TRUE) {
				//正面格式错误
				return array (
					'msg'       => 'FAIL',
					'col'       => 'Idphoto1',
					'err'       => 'POSERRORFORMAT1',
				);
			} else if(in_array($Idphoto2_ext, $file_ext, true) !== TRUE) {
				//反面格式错误
				return array (
					'msg'       => 'FAIL',
					'col'       => 'Idphoto2',
					'err'       => 'POSERRORFORMAT2',
				);
			}
		
			if ($Idphoto1_mimeType == 'application/octet-stream') {
				$Idphoto1_mimeType = 'image/jpeg';
			} else if ($Idphoto2_mimeType == 'application/octet-stream') {
				$Idphoto2_mimeType = 'image/jpeg';
			}
			
			//身份证上传
			$pos_name_path = public_path('/temp_uploads') . '/' . $Idphoto1_upname;
			$bak_name_path = public_path('/temp_uploads') . '/' . $Idphoto2_upname;
			
			//压缩图片
			$Id_pos_name1 = $this->_extet_thumbImage_handle($_FILES['Idphoto1']['tmp_name'], $maxwidth, $maxheight, $pos_name_path, $Idphoto1_mimeType); //正面
			$Id_bak_name2 = $this->_extet_thumbImage_handle($_FILES['Idphoto2']['tmp_name'], $maxwidth, $maxheight, $bak_name_path, $Idphoto2_mimeType); //反面
			
			$pos_bool = Storage::disk('uploads_file_IdCard')->put($Idphoto1_upname, file_get_contents($pos_name_path));
			$oppos_bool = Storage::disk('uploads_file_IdCard')->put($Idphoto2_upname, file_get_contents($bak_name_path));
			
			if($pos_bool && $oppos_bool) {
				if (UserImg::where('voided', '1')->where('user_id', $this->_user['user_id'])->first() == null) {
					$num = UserImg::create ([
						'user_id'           => $this->_user['user_id'],
						'img_type'          => '2', //1=用户头像, 2=身份证， 3=银行卡
						'img_idcard01_path' => 'uploads/IdCard/' . $Idphoto1_upname,
						'img_idcard02_path' => 'uploads/IdCard/' . $Idphoto2_upname,
						'img_idcard_status' => '1', //身份证审核状态 1 正在审核中，2 审核通过 ，3 审核不通过',
						'voided'            => '1',
						'rec_crt_date'      => date ('Y-m-d H:i:s'),
						'rec_upd_date'      => date ('Y-m-d H:i:s'),
						'rec_crt_user'      => $this->_user['user_name'],
						'rec_upd_user'      => $this->_user['user_name'],
					]);
				} else {
					$num = UserImg::where('voided', '1')->where('user_id', $this->_user['user_id'])->update ([
						'img_idcard01_path' => 'uploads/IdCard/' . $Idphoto1_upname,
						'img_idcard02_path' => 'uploads/IdCard/' . $Idphoto2_upname,
						'voided'            => '1',
						'rec_upd_date'      => date ('Y-m-d H:i:s'),
						'rec_upd_user'      => $this->_user['user_name'],
					]);
				}
		
				return array(
					'msg'       => 'SUC',
					'photo1path'=> 'uploads/IdCard/' . $Idphoto1_upname,
					'photo2path'=> 'uploads/IdCard/' . $Idphoto2_upname,
				);
			} else {
				return array(
					'msg'       => 'FAIL',
					'col'       => 'Idphoto1-Idphoto2',
					'err'       => 'uploadErr',
				);
			}
		}
		
		//银行卡上传
		protected function _exte_upload_file_bankPhoto ($request)
		{
			// IdCardUpload, BankUpload
			//银行卡上传
			$uploadType = $request->uploadType;
			$bankphoto = $request->bankimg; //正面
			$bankphoto_name = $bankphoto->getClientOriginalName(); // 文件原名
			$bankphoto_ext = $bankphoto->getClientOriginalExtension(); // 文件扩展名
			$bankphoto_path = $bankphoto->getRealPath(); // 临时文件的绝对路径
			$bankphoto_size = $bankphoto->getClientSize(); //文件大小
			$bankphoto_mimeType = getimagesize($_FILES['bankimg']['tmp_name'])['mime']; //原图片的真实扩展名
			if ($uploadType == 'changeBank') {
				$bankphoto_upname = date('YmdHis') . '-' . $this->_user['user_id'] . '-chg-bank-pos.' . $bankphoto_ext;
			} else {
				$bankphoto_upname = date('YmdHis') . '-' . $this->_user['user_id'] . '-bank-pos.' . $bankphoto_ext;
			}
			$maxwidth="1056"; //设置图片的最大宽度
			$maxheight="500"; //设置图片的最大高度
			$file_ext = array('JPG', 'JPEG', 'jpg', 'jpeg', 'PNG', 'png');
			
			//检查图片大小， 格式
			if (($bankphoto_size / 1024) > 2048) {
				//正面图片大于2M
				return array (
					'msg'       => 'FAIL',
					'col'       => 'bankimg',
					'err'       => 'POSOVERSIZE1',
				);
			} else if(in_array($bankphoto_ext, $file_ext, true) !== TRUE) {
				//正面格式错误
				return array (
					'msg'       => 'FAIL',
					'col'       => 'bankimg',
					'err'       => 'POSERRORFORMAT',
				);
			}
			
			if ($bankphoto_mimeType == 'application/octet-stream') {
				$bankphoto_mimeType = 'image/jpeg';
			}
			
			//银行卡上传
			$pos_name_path = public_path('/temp_uploads') . '/' . $bankphoto_upname;
			
			//压缩图片
			$bank_pos_name = $this->_extet_thumbImage_handle($_FILES['bankimg']['tmp_name'], $maxwidth, $maxheight, $pos_name_path, $bankphoto_mimeType); //正面
			$bank_pos_bool = Storage::disk('uploads_file_Bank')->put($bankphoto_upname, file_get_contents($pos_name_path));
			
			if($bank_pos_bool) {
				
				
				if (UserImg::where('voided', '1')->where('user_id', $this->_user['user_id'])->first() == null) {
					$num = UserImg::create ([
						'user_id'           => $this->_user['user_id'],
						'img_type'          => '3', // 1=用户头像, 2=身份证， 3=银行卡
						'img_bank_path'     => 'uploads/Bank/' . $bankphoto_upname,
						'img_bank_status'   => '1', //'银行卡审核状态1 正在审核中...，2 审核通过，3 银行卡变更'
						'voided'            => '1',
						'rec_crt_date'      => date ('Y-m-d H:i:s'),
						'rec_upd_date'      => date ('Y-m-d H:i:s'),
						'rec_crt_user'      => $this->_user['user_name'],
						'rec_upd_user'      => $this->_user['user_name'],
					]);
				} else {
					$num = UserImg::where('voided', '1')->where('user_id', $this->_user['user_id'])->update ([
						'img_bank_path'     => 'uploads/Bank/' . $bankphoto_upname,
						'voided'            => '1',
						'rec_upd_date'      => date ('Y-m-d H:i:s'),
						'rec_upd_user'      => $this->_user['user_name'],
					]);
				}
				
				return array(
					'msg'       => 'SUC',
					'photo1path'=> 'uploads/Bank/' . $bankphoto_upname,
				);
			} else {
				return array(
					'msg'       => 'FAIL',
					'col'       => 'bankimg',
					'err'       => 'uploadErr',
				);
			}
		}
		
		protected function _exte_upload_file_head_img ($request)
		{
			$headimg = $request->headimg; //正面
			$headimg_name = $headimg->getClientOriginalName(); // 文件原名
			$headimg_ext = $headimg->getClientOriginalExtension(); // 文件扩展名
			$headimg_path = $headimg->getRealPath(); // 临时文件的绝对路径
			$headimg_size = $headimg->getClientSize(); //文件大小
			$headimg_mimeType = getimagesize($_FILES['headimg']['tmp_name'])['mime']; //原图片的真实扩展名
			$headimg_upname = date('YmdHis') . '-' . $this->_user['user_id'] . '-head-pos.' . $headimg_ext;
			$maxwidth="1056"; //设置图片的最大宽度
			$maxheight="500"; //设置图片的最大高度
			$file_ext = array('JPG', 'JPEG', 'jpg', 'jpeg', 'PNG', 'png');
			
			//检查图片大小， 格式
			if (($headimg_size / 1024) > 2048) {
				//正面图片大于2M
				return array (
					'msg'       => 'FAIL',
					'col'       => 'headimg',
					'err'       => 'POSOVERSIZE1',
				);
			} else if(in_array($headimg_ext, $file_ext, true) !== TRUE) {
				//正面格式错误
				return array (
					'msg'       => 'FAIL',
					'col'       => 'headimg',
					'err'       => 'POSERRORFORMAT',
				);
			}
			
			if ($headimg_mimeType == 'application/octet-stream') {
				$headimg_mimeType = 'image/jpeg';
			}
			
			//银行卡上传
			$pos_name_path = public_path('/temp_uploads') . '/' . $headimg_upname;
			
			//压缩图片
			$headimg_pos_name = $this->_extet_thumbImage_handle($_FILES['headimg']['tmp_name'], $maxwidth, $maxheight, $pos_name_path, $headimg_mimeType); //正面
			$headimg_pos_bool = Storage::disk('uploads_file_Head')->put($headimg_upname, file_get_contents($pos_name_path));
			
			if($headimg_pos_bool) {
				
				
				if (UserImg::where('voided', '1')->where('user_id', $this->_user['user_id'])->first() == null) {
					$num = UserImg::create ([
						'user_id'           => $this->_user['user_id'],
						'img_type'          => '1', // 1=用户头像, 2=身份证， 3=银行卡
						'img_header_path'   => 'uploads/Head/' . $headimg_upname,
						'voided'            => '1',
						'rec_crt_date'      => date ('Y-m-d H:i:s'),
						'rec_upd_date'      => date ('Y-m-d H:i:s'),
						'rec_crt_user'      => $this->_user['user_name'],
						'rec_upd_user'      => $this->_user['user_name'],
					]);
				} else {
					$num = UserImg::where('voided', '1')->where('user_id', $this->_user['user_id'])->update ([
						'img_header_path'   => 'uploads/Head/' . $headimg_upname,
						'voided'            => '1',
						'rec_upd_date'      => date ('Y-m-d H:i:s'),
						'rec_upd_user'      => $this->_user['user_name'],
					]);
				}
				
				return array(
					'msg'       => 'SUC',
					'photo1path'=> 'uploads/Head/' . $headimg_upname,
				);
			} else {
				return array(
					'msg'       => 'FAIL',
					'col'       => 'headimg',
					'err'       => 'uploadErr',
				);
			}
		}
		
		//压缩图片处理函数 返回 bool
		protected function _extet_thumbImage_handle ($im,$maxwidth,$maxheight,$name,$filetype)
		{
			switch ($filetype) {
				case 'image/pjpeg':
				case 'image/jpeg':
					$im = imagecreatefromjpeg($im);    //PHP图片处理系统函数
					break;
				case 'image/gif':
					$im = imagecreatefromgif($im);
					break;
				case 'image/png':
					$im = imagecreatefrompng($im);
					break;
				case 'image/wbmp':
					$im = imagecreatefromwbmp($im);
					break;
			}
			
			$resizewidth_tag = $resizeheight_tag = false;
			$pic_width = imagesx($im);
			$pic_height = imagesy($im);
			
			if(($maxwidth && $pic_width > $maxwidth) || ($maxheight && $pic_height > $maxheight))
			{
				$resizewidth_tag = $resizeheight_tag = false;
				
				if($maxwidth && $pic_width>$maxwidth)
				{
					$widthratio = $maxwidth / $pic_width;
					$resizewidth_tag = true;
				}
				
				if($maxheight && $pic_height>$maxheight)
				{
					$heightratio = $maxheight / $pic_height;
					$resizeheight_tag = true;
				}
				
				if($resizewidth_tag && $resizeheight_tag)
				{
					if($widthratio < $heightratio)
						$ratio = $widthratio;
					else
						$ratio = $heightratio;
				}
				
				if($resizewidth_tag && !$resizeheight_tag)
					$ratio = $widthratio;
				
				if($resizeheight_tag && !$resizewidth_tag)
					$ratio = $heightratio;
				
				$newwidth = $pic_width * $ratio;
				$newheight = $pic_height * $ratio;
				
				if(function_exists("imagecopyresampled"))
				{
					$newim = imagecreatetruecolor($newwidth,$newheight);//PHP图片处理系统函数
					imagecopyresampled($newim,$im,0,0,0,0,$newwidth,$newheight,$pic_width,$pic_height);//PHP图片处理系统函数
				}
				else
				{
					$newim = imagecreate($newwidth,$newheight);
					imagecopyresized($newim,$im,0,0,0,0,$newwidth,$newheight,$pic_width,$pic_height);
				}
				
				switch ($filetype) {
					case 'image/pjpeg' :
					case 'image/jpeg' :
						$result = imagejpeg($newim,$name, 90);
						break;
					case 'image/gif' :
						$result = imagegif($newim,$name);
						break;
					case 'image/png' :
						$result = imagepng($newim,$name, 9);
						break;
					case 'image/wbmp' :
						$result = imagewbmp($newim,$name);
						break;
				}
				imagedestroy($newim);
			}
			else
			{
				switch ($filetype) {
					case 'image/pjpeg' :
					case 'image/jpeg' :
						$result = imagejpeg($im,$name, 90);
						break;
					case 'image/gif' :
						$result = imagegif($im,$name);
						break;
					case 'image/png' :
						$result = imagepng($im,$name, 9);
						break;
					case 'image/wbmp' :
						$result = imagewbmp($im,$name);
						break;
				}
			}
			
			return $result; //返回结果
		}
		
		//todo 待继续优化
		protected function _exte_get_all_subordinate($uid){
			
			static $list = [];
			
			static $listTmp = [];
			
			if(!$uid){
				return false;
			}
			if(!in_array($uid,$list)){
				$list [] = $uid;
				
			}
			
			$agent = Agents::where(['parent_id'=>$uid])->select('user_id')->get()->toArray();
			
			foreach($agent as $v){
				$listTmp[] = $v['user_id'];
			}
			
			if(!empty($listTmp)){
				$idTmp =array_shift($listTmp);
				return self::_exte_get_all_subordinate($idTmp);
			}
			return $list;
		}
		
		protected function _exte_handle_deposit_role_structur_data($data)
		{
			$mon        = explode(',', $data['para_data1']);//星期一
			$tues       = explode(',', $data['para_data2']);//星期二
			$wed        = explode(',', $data['para_data3']);//星期三
			$Thurs      = explode(',', $data['para_data4']);//星期四
			$fri        = explode(',', $data['para_data5']);//星期五
			$sat        = explode(',', $data['para_data6']);//星期六 日
			$other      = explode(',', $data['para_data0']);//某天的特殊时间段
			
			$_rule_ary = array(
				'1'             => array(
					'start'     => strtotime (date ('Ymd' . ' ' . $mon[0])),
					'end'       => strtotime (date ('Ymd' . ' ' . $mon[1])),
					//'expt_start'=> $other[0],
					//'expt_end'  => $other[1],
				),
				'2'             => array(
					'start'     => strtotime (date ('Ymd' . ' ' . $tues[0])),
					'end'       => strtotime (date ('Ymd' . ' ' . $tues[1])),
					//'expt_start'=> $other[0],
					//'expt_end'  => $other[1],
				),
				'3'             => array(
					'start'     => strtotime (date ('Ymd' . ' ' . $wed[0])),
					'end'       => strtotime (date ('Ymd' . ' ' . $wed[1])),
					//'expt_start'=> $other[0],
					//'expt_end'  => $other[1],
				),
				'4'             => array(
					'start'     => strtotime (date ('Ymd' . ' ' . $Thurs[0])),
					'end'       => strtotime (date ('Ymd' . ' ' . $Thurs[1])),
					//'expt_start'=> $other[0],
					//'expt_end'  => $other[1],
				),
				'5'             => array(
					'start'     => strtotime (date ('Ymd' . ' ' . $fri[0])),
					'end'       => strtotime (date ('Ymd' . ' ' . $fri[1])),
				),
				'6'             => array(
					'start'     => strtotime (date ('Ymd' . ' ' . $sat[0])),
					'end'       => strtotime (date ('Ymd' . ' ' . $sat[1])),
				),
				'0'             => array(
					'start'     => strtotime (date ('Ymd' . ' ' . $other[0])),
					'end'       => strtotime (date ('Ymd' . ' ' . $other[1])),
				),
			);
			
			//得到当天的出金规则
			if (in_array(date('w'), array('1', '2', '3', '4', '5'), true)) {
				$_draw_date = $_rule_ary[date('w')];
			} else {
				//星期六 日，入金时间，去 $other
				$_draw_date = $_rule_ary['6'];
			}
			
			return $_draw_date;
		}
		
		protected function _exte_handle_withdraw_role_structur_data($data)
		{
			$mon        = explode(',', $data['para_data1']);//星期一
			$tues       = explode(',', $data['para_data2']);//星期二
			$wed        = explode(',', $data['para_data3']);//星期三
			$Thurs      = explode(',', $data['para_data4']);//星期四
			$fri        = explode(',', $data['para_data5']);//星期五
			$sat        = explode(',', $data['para_data6']);//星期六 日
			$other      = explode(',', $data['para_data0']);//某天的特殊时间段
			
			$_rule_ary = array(
				'1'             => array(
					'start'     => strtotime (date ('Ymd' . ' ' . $mon[0])),
					'end'       => strtotime (date ('Ymd' . ' ' . $mon[1])),
					'expt_start'=> strtotime (date ('Ymd' . ' ' . $other[0])),
					'expt_end'  => strtotime (date ('Ymd' . ' ' . $other[1])),
				),
				'2'             => array(
					'start'     => strtotime (date ('Ymd' . ' ' . $tues[0])),
					'end'       => strtotime (date ('Ymd' . ' ' . $tues[1])),
					'expt_start'=> strtotime (date ('Ymd' . ' ' . $other[0])),
					'expt_end'  => strtotime (date ('Ymd' . ' ' . $other[1])),
				),
				'3'             => array(
					'start'     => strtotime (date ('Ymd' . ' ' . $wed[0])),
					'end'       => strtotime (date ('Ymd' . ' ' . $wed[1])),
					'expt_start'=> strtotime (date ('Ymd' . ' ' . $other[0])),
					'expt_end'  => strtotime (date ('Ymd' . ' ' . $other[1])),
				),
				'4'             => array(
					'start'     => strtotime (date ('Ymd' . ' ' . $Thurs[0])),
					'end'       => strtotime (date ('Ymd' . ' ' . $Thurs[1])),
					'expt_start'=> strtotime (date ('Ymd' . ' ' . $other[0])),
					'expt_end'  => strtotime (date ('Ymd' . ' ' . $other[1])),
				),
				'5'             => array(
					'start'     => strtotime (date ('Ymd' . ' ' . $fri[0])),
					'end'       => strtotime (date ('Ymd' . ' ' . $fri[1])),
					'expt_start'=> strtotime (date ('Ymd' . ' ' . $other[0])),
					'expt_end'  => strtotime (date ('Ymd' . ' ' . $other[1])),
				),
				'6'             => array(
					'start'     => strtotime (date ('Ymd' . ' ' . $sat[0])),
					'end'       => strtotime (date ('Ymd' . ' ' . $sat[1])),
				),
				'0'             => array(
					'start'     => strtotime (date ('Ymd' . ' ' . $other[0])),
					'end'       => strtotime (date ('Ymd' . ' ' . $other[1])),
				),
			);
			
			//得到当天的出金规则
			if (in_array(date('w'), array('1', '2', '3', '4', '5'), true)) {
				$_draw_date = $_rule_ary[date('w')];
			} else {
				$_draw_date = $_rule_ary['6'];
			}
			
			return $_draw_date;
		}
		
		//出金手续费收取规则
		protected function draw_poundage_rule($_money_RMB) {
			
			$_money_pdg = 0;
			
			if ($_money_RMB > 0 && $_money_RMB <= 2000) {
				$_money_pdg = 5;
			} else if ($_money_RMB >= 2001 && $_money_RMB <= 5000) {
				$_money_pdg = 10;
			} else if ($_money_RMB >= 5001 && $_money_RMB <= 10000) {
				$_money_pdg = 20;
			} else if ($_money_RMB >= 10001 && $_money_RMB <= 15000) {
				$_money_pdg = 25;
			} else if ($_money_RMB >= 15001 && $_money_RMB <= 20000) {
				$_money_pdg = 30;
			} else if ($_money_RMB >= 20001 && $_money_RMB <= 25000) {
				$_money_pdg = 35;
			} else if ($_money_RMB >= 25001 && $_money_RMB <= 30000) {
				$_money_pdg = 40;
			} else if ($_money_RMB >= 30001 && $_money_RMB <= 35000) {
				$_money_pdg = 45;
			} else if ($_money_RMB >= 35001) {
				$_money_pdg = 50;
			}
			
			return $_money_pdg;
		}
		
		//判断当前用户角色
		protected function Role(){
			//判断当前用户角色
			$admin = Admin::find(session('id'));
			switch (RoleName($admin->role_id)) {
				case '超级管理员':
				case '管理员':
					$state = 1;
					break;
				case '客服':
					$state = 2;
					break;
				case '财务':
					$state = 3;
					break;
				case '其他':
					$state = 4;
					break;
				default;
					$state = 0;
					break;
			}
			return $state;
		}
		
		/*Excel 相关设定值*/
		protected function _exte_export_excel($name, $role, $data)
		{
			$file_name = $this->_exte_set_export_excel_filename($name, $role);
			
			Excel::create($file_name, function($excel) use ($file_name, $data){
				$excel->sheet($file_name, function($sheet) use ($data){
					$sheet->rows($this->_exte_excel_data_structure_format($data));
				});
			})->store($this->_exte_export_excel_format(), $this->_exte_export_excel_basic_path($role));
			
			if ($role == "agents") {
				$rs = route('download', ['file' => $file_name, 'role' => $role]);
			} else if ($role == "admin" && $name == '入金流水') {
				$rs = route('admin_deposit_download', ['file' => $file_name, 'role' => $role]);
			} else if ($role == "admin" && $name == '权益结算统计') {
				$rs = route('admin_rights_download', ['file' => $file_name, 'role' => $role]);
			}
			
			return $rs;
		}
		
		protected function _exte_set_export_excel_filename($name, $role)
		{
			if ($role == "agents") {
				return $this->_user['user_id'] . '-' . $name . date('YmdHis');
			} else if ($role == "admin") {
				return $this->_auser['username'] . '-' . $name . date('YmdHis');
			}
		}
		
		protected function _exte_export_excel_format()
		{
			return 'xlsx';
		}
		
		protected function _exte_excel_data_structure_format($data)
		{
			$_rs = array();
			
			$_rs[0]['act_order_no']              = trans("systemlanguage.account_deposit_order_no");
			$_rs[0]['act_userId']                = trans("systemlanguage.account_deposit_no");
			$_rs[0]['act_directType']            = trans("systemlanguage.account_deposit_type");
			$_rs[0]['act_directComment']         = trans("systemlanguage.account_deposit_comment");
			$_rs[0]['act_directProfit']          = trans("systemlanguage.account_deposit_moneny");
			$_rs[0]['act_directModifyTime']      = trans("systemlanguage.account_deposit_datetme");
			
			foreach ($data as $key => $val) {
				$_rs[$key + 1]['act_order_no']          = $val['order_no'];
				$_rs[$key + 1]['act_userId']            = $val['userId'];
				$_rs[$key + 1]['act_directType']        = $val['directType'];
				$_rs[$key + 1]['act_directComment']     = $this->_exte_amount_source_desc($val['directComment']);
				$_rs[$key + 1]['act_directProfit']      = $val['directProfit'];
				$_rs[$key + 1]['act_directModifyTime']  = $val['directModifyTime'];
			}
			
			return $_rs;
		}
		
		protected function _exte_amount_source_desc($val){
			if (strpos($val, '-ZH') !== false) {
				return $comment = "佣金转户";
			} else if (strpos($val, '-CZ') !== false) {
				return $comment = "账户充值";
			} else if (strpos($val, '-FY') !== false) {
				return $comment = "账户返佣";
			} else if (strpos($val, '-QK') !== false) {
				return $comment = "账户取款";
			} else if (strpos($val, '-TH') !== false) {
				return $comment = "转户退回";
			} else if (strpos($val, '-RJ') !== false) {
				return $comment = "平台";
			} else if (strpos($val, '-XY') !== false) {
				return $comment = "平台";
			} else if (strpos($val, '-CJTH') !== false) {
				return $comment = "出金退回";
			} else if (strpos($val, '-Adj') !== false) {
				return $comment = "平台";
			} else {
				return $comment = "其他";
			}
		}
		
		protected function _exte_export_excel_basic_path($role)
		{
			if ($role == "agents") {
				return public_path('uploads/Excel-exports/Agents//');
			} else if ($role == "admin") {
				return public_path('uploads/Excel-exports/Admin//');
			}
		}
		
		protected function _exte_get_mt4_grpId($grpname)
		{
			return UserGroup::select('mt4_grpId', 'user_group_name')->where('user_group_name', $grpname)->where('voided', '1')->get()->toArray();
		}
		
		protected function _exte_set_search_condition($subWhere, $data)
		{
			return '';
		}
		
		protected function _exte_get_query_sql_data($sql, $totalType, $col, $orderBy = 'desc')
		{
			$id_list        = array ();
			
			if ($totalType == 'page') {
				$id_list = $sql->skip($this->_offset)->take($this->_pageSize)->orderBy($col, $orderBy)->get()->toArray();
			} else if ($totalType == 'count') {
				$id_list = $sql->count();
			} else if ($totalType == 'sum') {
				$id_list = $sql->get()->toArray();
			}
			
			return $id_list;
		}
	}