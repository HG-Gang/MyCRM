<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018-05-22
	 * Time: 下午 3:07
	 */
	
	namespace App\Http\Controllers\Admin;
	
	use Illuminate\Http\Request;
	use App\Model\TransApplyLog;
	use App\Model\OperationLog;
	use App\Model\Mt4Users;
	use App\Model\Mt4Trades;
	use App\Model\User;
	use App\Model\Agents;
	use App\Model\UserGroup;
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	
	class CustomerController extends Abstract_Mt4service_Controller
	{
		protected $_str_rala            = '';
		
		public function user_list ()
		{
			return view('admin.cust_list.cust_list_browse')->with(['role' => $this->Role()]);
		}
		
		public function cust_add_browse()
		{
			$usergrpId  = UserGroup::where('voided', '1')->get()->toArray();
			
			return view('admin.cust_list.cust_add_browse')->with(['usergrpId'     => $usergrpId,]);
		}
		
		public function change_list ()
		{
			return view('admin.cust_list.change_list_browse');
		}
		
		public function custListSearch(Request $request)
		{
			$data = array(
				'userId'            => $request->userId,
				'username'          => $request->username,
				'userstatus'        => $request->userstatus,
				'startdate'         => $request->startdate,
				'enddate'           => $request->enddate,
				'loginId'           => $this->_user,
			);
			
			$result = array('rows' => '', 'total' => '');
			$_rs = $this->get_all_cust_id_list ('page', $data);
			
			if (!empty($_rs)) {
				//统计汇总当前页各种资金
				$_sumdata = $this->get_all_cust_page_sumdata($_rs, $data, 'pageTotal');
				$_search_sumdata = $this->get_all_cust_sumdata($data);
				
				//对查询结果和汇总结果再次重新整理数据结构
				foreach ($_rs as $key => $_info) {
					$_rs[$key]['total_comm']              = $_sumdata[$_info['user_id']]['total_comm'];
					$_rs[$key]['total_yuerj']             = $_sumdata[$_info['user_id']]['total_yuerj'];
					$_rs[$key]['total_yuecj']             = $_sumdata[$_info['user_id']]['total_yuecj'];
					$_rs[$key]['total_volume']            = $_sumdata[$_info['user_id']]['total_volume'];
					$_rs[$key]['total_swaps']             = $_sumdata[$_info['user_id']]['total_swaps'];
					$_rs[$key]['total_profit']            = $_sumdata[$_info['user_id']]['total_profit'];
					$_rs[$key]['total_noble_metal']       = $_sumdata[$_info['user_id']]['total_noble_metal'];
					$_rs[$key]['total_for_exca']          = $_sumdata[$_info['user_id']]['total_for_exca'];
					$_rs[$key]['total_crud_oil']          = $_sumdata[$_info['user_id']]['total_crud_oil'];
					$_rs[$key]['total_index']             = $_sumdata[$_info['user_id']]['total_index'];
					$_rs[$key]['total_net_worth']         = $_sumdata[$_info['user_id']]['total_net_worth'];
					$_rs[$key]['mt4MarginLevel']          = number_format($_rs[$key]['mt4MarginLevel'], '2', '.', '');
				}
				
				$result['rows'] = $_rs;
				$result['total'] = $this->get_all_cust_id_list('count', $data);
				$result['footer'] = [[
					'mt4_login'         => '总计',
					'user_name'         => '',
					'mt4MarginLevel'    => '',
					'mt4_balance'       => $_search_sumdata['search_total_bal'],
					'mt4_equity'        => $_search_sumdata['search_total_eqy'],
					'total_yuerj'       => $_search_sumdata['search_total_yuerj'],
					'total_yuecj'       => $_search_sumdata['search_total_yuecj'],
					'total_net_worth'   => $_search_sumdata['search_total_net_worth'],
					'total_comm'        => $_search_sumdata['search_total_comm'],
					'total_profit'      => $_search_sumdata['search_total_profit'],
					'total_noble_metal' => $_search_sumdata['search_total_noble_metal'],
					'total_for_exca'    => $_search_sumdata['search_total_for_exca'],
					'total_crud_oil'    => $_search_sumdata['search_total_crud_oil'],
					'total_index'       => $_search_sumdata['search_total_index'],
					'total_volume'      => $_search_sumdata['search_total_volume'],
					'total_swaps'       => $_search_sumdata['search_total_swaps'],
					'mt4_regdate'       => '',
				]];
			}
			
			return json_encode ($result);
		}
		
		public function custChangeListSearch(Request $request)
		{
			$result = array('rows' => '', 'total' => '');
			
			$_rs = $this->get_all_change_list('page', $request);
			
			if (!empty($_rs)) {
				$_bal_vol = $this->get_act_chang_info($_rs); //得到这些人的余额和持仓总量
				if (!empty($_bal_vol)) {
					for ($i = 0; $i < count($_rs); $i++) {
						if (!empty($_bal_vol[$_rs[$i]['transUid']]['bal'])) {
							$_rs[$i]['bal'] = number_format($_bal_vol[$_rs[$i]['transUid']]['bal'][0]['BALANCE'], 2);
						} else {
							$_rs[$i]['bal'] = number_format(0, 2);
						}
						
						$_rs[$i]['vol'] = $_bal_vol[$_rs[$i]['transUid']]['vol'];
					}
				}
				
				$result['rows'] = $_rs;
				$result['total'] = $this->get_all_change_list('count', $request);
			}
			
			return json_encode($result);
		}
		
		public function cust_detail ($acc_uid)
		{
			//$_upd_info = $this->_exte_mt4_update_local_user_info($acc_uid);
			$_acc_info = $this->_exte_get_user_info($acc_uid);
			
			$ag_lvl = UserGroup::select('user_group_id', 'user_group_name')->where('voided', 1)->get()->toArray();
			
			// 1 超管视图，2 客服视图，3 财务视图
			return view('admin.cust_list.cust_detail_' . $this->Role())->with([
				'_acc_info' => $_acc_info,
				'ag_lvl'    => $ag_lvl,
			]);
		}
		
		public function cust_save_add (Request $request)
		{
			$data           = $request->data;
			$username       = $data['username'];
			$sex            = $data['sex'];
			$userInviterId  = $data['userInviterId'];
			$password       = $data['password'];
			$usergrpId      = $data['usergrpId'];
			$usergrpName    = $request->usergrpName;
			
			//查看介绍人ID合法性
			if ($userInviterId == "0") {
				$chkInviterId['group_id'] = 0;
				$chkInviterId['trans_mode'] = 0;
			} else {
				$chkInviterId   = Agents::where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))->where('user_id', $userInviterId)->first();
			}
			
			if ($chkInviterId == null) {
				//无效的介绍人ID
				return response()->json([
					'msg'        => 'FAIL',
					'err'        => 'NonExist',
					'col'        => 'userInviterId',
				]);
			}
			
			//检查用户组别合法性
			$chkusergrp     = UserGroup::where('voided', '1')->where('user_group_id', $usergrpId)->where('user_group_name', $usergrpName)->first();
			if ($chkusergrp == null) {
				//无效的用户组别
				return response()->json([
					'msg'        => 'FAIL',
					'err'        => 'Invalidgrp',
					'col'        => 'usergrpId',
				]);
			}
			
			$num = User::create([
				'user_name'             => $username,
				'password'              => base64_encode($password),
				'sex'                   => $sex,
				'phone'                 => '86' . '-' . '139'. rand(12345678, 99999999),
				'IDcard_no'			    => '44'. rand(1234567890, 9999999999) . rand(654321, 987654),
				'email'				    => rand(123456789, 999999999) . '@qq.com',
				'group_id'              => 5,
				'parent_id'             => $userInviterId, // 查找URL的编号的user_id, 0 属于平台的用户
				'user_money'		    => '0',
				'cust_eqy'              => '0',
				'effective_cdt'         => '0',
				'comm_prop'			    => 70,
				'mt4_grp'			    => $usergrpName,
				'trans_mode'            => $chkInviterId['trans_mode'], // 交易模式，0 佣金模式，1 保证金模式
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
				'rights'                => 0, //权益比例
				'cycle'                 => 0,//结算周期
				'voided'                => '1', //注册后允许登录
				'rec_crt_date'          => date('Y-m-d H:i:s'),
				'rec_upd_date'          => date('Y-m-d H:i:s'),
				'rec_crt_user'          => 'admin',
				'rec_upd_user'          => 'admin',
			]);
			
			if ($num) {
				//本地创建用户成功后更新当前用户country列
				$no = $num->find($num->user_id)->update(['mt4_code' => $num->user_id, 'country' => $this->_exte_show_account_relationship_chain($num->user_id, '-', 'id', 'admin')]);
				
				//本地新增用户成功，同步MT4注册
				$data = $this->_exte_get_user_info($num->user_id);
				$mt4_grpId = $this->_exte_get_mt4_grpId($data['mt4_grp']);
				$data['mt4_grpId'] = $mt4_grpId[0]['user_group_name'];
				$mt4 = $this->_exte_sync_mt4_reigster2($data);
				
				if (is_array($mt4) && $mt4['0'] == 'OK') {
					//注册成功
					return response()->json([
						'msg'        => 'SUC',
						'err'        => 'NOERR',
						'col'        => 'NOTCOL',
					]);
				} else {
					return response()->json([
						'msg'        => 'FAIL',
						'err'        => $mt4,
						'col'        => 'NOTCOL',
					]);
				}
			}
		}
		
		public function cust_save_info (Request $request)
		{
			$data           = $request->data;
			$userId         = $data['userId'];
			$username       = $data['username'];
			$password       = $data['password'];
			$userIdcardNo   = $data['userIdcardNo'];
			$userphoneNo    = $data['userphoneNo'];
			$useremail      = $data['useremail'];
			$usergrpId      = $data['usergrpId'];
			//$usertype       = $data['usertype'];
			//$userrights     = $data['userrights'];
			//$usercycle      = $data['usercycle'];
			$cust_lvg       = $data['cust_lvg'];
			$userparentId   = $data['userparentId'];
			//$useragtId      = $data['useragtId'];
			//$userrebate     = $data['userrebate'];
			$userremark     = $data['userremark'];
			//$reccrtdate     = $data['reccrtdate'];
			$usercountry    = $data['usercountry'];
			$usergrpName    = $request->usergrpName;
			$useragtName    = $request->useragtName;
			$enable         = $request->enable;
			$enablereadonly = $request->enablereadonly;
			$isoutmoney     = $request->isoutmoney;
			$_modules       = '86';
			$col_ary        = array();
			$_error         = array();
			
			$curr_info      = $this->_exte_get_user_info($userId);
			
			if ($curr_info['phone'] != $_modules . '-' . $userphoneNo && $this->Role() == 1) {
				//手机有变化，检查手机唯一性
				$_tel = $this->_exte_verify_phone($userphoneNo);
				if ($_tel) {
					//手机号已存在
					return response()->json([
						'msg'        => 'FAIL',
						'err'        => 'Existphone',
						'col'        => 'userphoneNo',
					]);
				}
				//$col_ary['phone'] = $_modules . '-' . $userphoneNo;
			}
			
			if ($curr_info['IDcard_no'] != $userIdcardNo && ($this->Role() == 1 || $this->Role() == 2)) {
				//身份证有变化
				$_ido = $this->_exte_verify_idno ($userIdcardNo);
				if ($_ido) {
					//身份证已存在
					return response()->json([
						'msg'        => 'FAIL',
						'err'        => 'Existidcard',
						'col'        => 'userIdcardNo',
					]);
				}
				//$col_ary['id'] = $userIdcardNo;
			}
			
			if ($curr_info['email'] != $useremail && $this->Role() == 1) {
				//邮件有变化
				$_eml = $this->_exte_verify_email ($useremail);
				if ($_eml) {
					//邮箱已存在
					return response()->json([
						'msg'        => 'FAIL',
						'err'        => 'Existemail',
						'col'        => 'useremail',
					]);
				}
				//$col_ary['email'] = $useremail;
			}
			
			////检查 用户组，上级代理 数据的合法性
			$chk_grp = UserGroup::where('user_group_id', $usergrpId)->where('user_group_name', $usergrpName)->where('voided', '1')->first();
			if($userparentId != '0') {
				$chk_pid = Agents::where('user_id', $userparentId)->where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))->first();
			} else {
				$chk_pid['group_id'] = $userparentId; //$userparentId = 0
			}
			
			if ($chk_grp == null) {
				$_error['grp'] = 'err_grp';
			} elseif ($chk_pid == null || $userparentId == $userId) {
				$_error['pid']	= 'err_pid';
			}
			
			if(!empty($_error)) {
				return response()->json([
					'msg'           => 'FAIL',
					'err'           => $_error,
					'col'           => 'data_vali_err',
				]);
			}
			
			//判定用户组是否更改
			if ($usergrpName != $curr_info['mt4_grp'] && ($this->Role() == 1 || $this->Role() == 2)) {
				//组别已被改变, 检查当前userId是否还有持仓单
				$is_orderNO = Mt4Trades::where('LOGIN', $userId)->where('CLOSE_TIME', '1970-01-01 00:00:00')->whereIn('CMD', array(0, 1, 2, 3, 4, 5))->count();
				if ($is_orderNO > 0) {
					return response()->json([
						'msg'       => 'FAIL',
						'err'       => 'ACCOUNTEXISTORDER',
						'col'       => 'usergrpId', //存在订单，无法更改用户组
					]);
				} else {
					$col_ary['group'] = $usergrpName;
				}
			}
			
			//判定账户 是否 启用，只读
			if ($curr_info['enable_readonly'] != $enablereadonly) {
					//账户只读状态已经改变
					$col_ary['enable_read_only'] = $enablereadonly;
			}
			//判定账户启用状态 1 启用, 0 禁用
			if ($curr_info['enable'] != $enable) {
				//账户启用状态已经改变, 同步MT4更新状态
				$col_ary['enable'] = $enable;
			}
			
			//同步MT4更新相关列
			$col_ary['login'] = $userId;
			//$col_ary['leverage'] = $cust_lvg;
			if ($this->Role() == 1) {
				$col_ary['name'] = $this->_exte_mt4_username_convert_encode($username);
			}
			$mt4_upd = $this->_exte_mt4_update_user($col_ary);
			
			if (is_array($mt4_upd) && $mt4_upd['ret'] != '0') {
				return response()->json([
					'msg'       => 'FAIL',
					'err'       => 'MT4OHTERUPDFAIL', //同步MT4更新其他列失败
					'col'       => 'userphoneNo',
				]);
			} else if (!is_array($mt4_upd)) {
				return response()->json([
					'msg'       => 'FAIL',
					'err'       => 'NETERRUPDFAIL', //网络故障,无法更新账户状态
					'col'       => 'MT4OHTER',
				]);
			}
			
			if ($this->Role() == 1) {
				//检查密码是否被更改
				if ($password != '********' && base64_decode($curr_info['password']) != $password && $this->Role() == 1) {
					//已经被修改, 同步MT4修改密码
					$mt4 = $this->_exte_mt4_reset_user_pwd($userId, $password);
					if (is_array($mt4) && $mt4['ret'] == '0') {
						//更改成功，短信通知
						//$_rs = $this->_exte_send_phone_notify($userphoneNo, 'resetPassword', array('password' => $password));
					} else {
						return response()->json([
							'msg'       => 'FAIL',
							'err'       => 'PSWUPDFAIL',
							'col'       => 'password', //密码更新失败
						]);
					}
				}
			}
			
			if ($mt4_upd['ret'] == '0') {
				//如果上面都执行正确，开始更新本地表数据
				$upd_num = User::where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))->find($userId)->update([
					'user_name'             => ($this->Role() == 1) ? $username : $curr_info['user_name'],
					'password'              => ($password != '********' && $this->Role() == 1) ? base64_encode($password) : $curr_info['password'],
					'email'                 => ($this->Role() == 1) ? $useremail : $curr_info['email'],
					'phone'                 => ($this->Role() == 1) ? $_modules . '-' . $userphoneNo : $curr_info['phone'],
					'IDcard_no'			    => $userIdcardNo,
					//'comm_prop'             => $userrebate,
					'mt4_grp'               => $usergrpName, //用户组
					'parent_id'             => $userparentId, //上级代理
					//'cust_lvg'              => $cust_lvg, //杠杆
					//'group_id'              => $useragtId,
					'is_confirm_agents_lvg' => '1',
					//'country'               => $col_ary['country'],
					'enable'                => $enable,
					'enable_readonly'       => $enablereadonly,
					'is_out_money'          => $isoutmoney, //是否允许出金
					//'trans_mode'            => $usertype, //账户类型
					//'cycle'                 => ($usertype == 1) ? $usercycle : 0, //结算周期
					//'rights'                => ($usertype == 1) ? $userrights : 0, //权益值
					'remark'                => $userremark,
					'rec_upd_user'          => $this->_auser['username'],
					'rec_upd_date'          => date('Y-m-d H:i:s'),
				]);
				
				//更新客户层级关系
				$mt4_upd_country = $this->_exte_mt4_update_user2 ($userId);
				$upd_country = User::where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))->find($userId)->update([
					'country'           => $this->_str_rala,
				]);
				
				if ($upd_num) {
					//TODO 更新了某个人的记录信息
					$loginIp = $this->_exte_get_user_loginIp();
					$crt_log = OperationLog::create([
						'name'                  => $this->_auser['username'],
						'user_id'               => $userId,
						'order_number'          => 0,
						'content'               => '[' . $this->_auser['username'] . '] ' . ' 成功修改了 ' . $curr_info['user_name'] . ' [' . $curr_info['user_id'] . '] ' . '账户信息',
						'handle_ip'             => $loginIp . '( ' . $this->_exte_get_user_loginIpCity($loginIp) . ' )',
						'created_on'            => time(),
						'type'                  => '0',
						'role_class'			=> $this->_auser['username'],
					]);
					
					return response()->json([
						'msg'           => 'SUC',
						'err'           => 'NOERR',
						'col'           => 'NOCOL',
					]);
				} else {
					return response()->json([
						'msg'           => 'FAIL',
						'err'           => 'INFOUPDATEFAIL',
						'col'           => 'NOCOL',
					]);
				}
			}
		}
		
		public function cust_apply_pass(Request $request) {
			
			$uid = $request->uid;
			$chk_user_info = TransApplyLog::where('trans_uid', $uid)->where('trans_apply_status', '0')->where('voided', '1')->first();
			if($chk_user_info == null) {
				return response()->json([
					'msg'                   => 'FAIL',
					'col'                   => 'INVALIDUSER',
				]);
			} else {
				//更改用户组别
				$params = array('login' => $uid, 'group' => $chk_user_info['trans_type_name']);
				$mt4_upd = $this->_exte_mt4_update_user($params);
				
				if(!is_array($mt4_upd)) {
					return response()->json([
						'msg'                   => 'FAIL',
						'col'                   => 'FATALCANOTCONNECT',
					]);
				} else if(is_array($mt4_upd) && $mt4_upd['ret'] == '0') {
					//MT4更改成功，开始修改本地表数据
					$num = User::where('user_id', $uid)->whereIn('user_status', array('0', '1', '2', '4'))->where('voided', '1')
						->update([
							'mt4_grp'           => $chk_user_info['trans_type_name'],
							'rec_upd_user'      => $this->_auser['username'],
							'rec_upd_date'      => date('Y-m-d H:i:s'),
						]);
					$upd = TransApplyLog::where('trans_uid', $uid)->where('trans_apply_status', '0')->where('voided', '1')
						->update([
							'trans_apply_status'=> '1', //0等待变更，1 确认变更，-1变更失败
							'rec_upd_user'      => $this->_auser['username'],
							'rec_upd_date'      => date('Y-m-d H:i:s'),
						]);
					if($num && $upd) {
						if((string)$chk_user_info['trans_type_gid'] == '1') {
							$comm_type = '有佣金';
						} else {
							$comm_type = '无佣金';
						}
						//TODO 更新了某个人的记录信息
						$ip = $this->_exte_get_user_loginIp();
						$crt_log = OperationLog::create([
							'name'                  => $this->_auser['username'],
							'user_id'               => $uid,
							'order_number'          => 0,
							'content'               => '[' . $this->_auser['username'] . '] ' . ' 成功变更[' . $chk_user_info['trans_uid'] . '] ' . ', '. '变更后的账户组类型是: ' . $comm_type . ' [' . $chk_user_info['trans_type_name'] . ']',
							'handle_ip'             => $ip . '( ' . $this->_exte_get_user_loginIpCity($ip) . ' )',
							'created_on'            => time(),
							'type'                  => '0',
							'role_class'			=> $this->Role(),
						]);
					}
					
					if($num && $crt_log && $upd) {
						return response()->json([
							'msg'                   => 'SUCCESS',
							'col'                   => 'UPDATESUC',
						]);
					} else {
						return response()->json([
							'msg'                   => 'FAIL',
							'col'                   => 'UPDATEFAIL',
						]);
					}
				} else {
					return response()->json([
						'msg'                   => 'FAIL',
						'col'                   => 'FATALCANOTCONNECT',
					]);
				}
			}
		}
		
		public function cust_apply_nopass(Request $request) {
			
			$uid = $request->uid;
			$trans_apply_reason = $request->trans_apply_reason;
			$chk_user_info = TransApplyLog::where('trans_uid', $uid)->where('trans_apply_status', '0')->where('voided', '1')->first();
			if($chk_user_info == null) {
				return response()->json([
					'msg'                   => 'FAIL',
					'col'                   => 'INVALIDUSER',
				]);
			} else {
				//客户取消某某个账户组类型的申请变更
				$num = TransApplyLog::where('trans_uid', $uid)->where('trans_apply_status', '0')->where('voided', '1')
					->update([
						'trans_apply_reason'=> $trans_apply_reason,
						'trans_apply_status'=> '-1', //0等待变更，1 确认变更，-1变更失败
						'rec_upd_user'      => $this->_auser['username'],
						'rec_upd_date'      => date('Y-m-d H:i:s'),
					]);
				if($num) {
					if((string)$chk_user_info['trans_type_gid'] == '1') {
						$comm_type = '有佣金';
					} else {
						$comm_type = '无佣金';
					}
					//TODO 更新了某个人的记录信息
					$ip = $this->_exte_get_user_loginIp();
					$crt_log = OperationLog::create([
						'name'                  => $this->_auser['username'],
						'user_id'               => $uid,
						'order_number'          => 0,
						'content'               => '[' . $this->_auser['username'] . '] ' . '取消了[' . $chk_user_info['trans_uid'] . '] 账户的' . $comm_type . ' [' . $chk_user_info['trans_type_name'] . ']' . '变更申请',
						'handle_ip'             => $ip . '( ' . $this->_exte_get_user_loginIpCity($ip) . ' )',
						'created_on'            => time(),
						'type'                  => '0',
						'role_class'			=> $this->Role(),
					]);
				}
				
				if($num && $crt_log) {
					return response()->json([
						'msg'                   => 'SUCCESS',
						'col'                   => 'UPDATESUC',
					]);
				} else {
					return response()->json([
						'msg'                   => 'FAIL',
						'col'                   => 'UPDATEFAIL',
					]);
				}
			}
		}
		
		protected function get_all_cust_id_list ($totalType, $data)
		{
			$query_sql = User::selectRaw("
				user.user_id, user.user_name, user.parent_id,
				user.trans_mode, user.mt4_code, user.user_money, user.cust_eqy, user.mt4_grp,
				user.user_status, user.voided, user.IDcard_status, user.bank_status,
				mt4_users.LOGIN as mt4_login, mt4_users.NAME as mt4_name, mt4_users.BALANCE as mt4_balance,
				mt4_users.EQUITY as mt4_equity, mt4_users.REGDATE as mt4_regdate, mt4_users.MARGIN_LEVEL as mt4MarginLevel
			")->join('mt4_users', 'mt4_users.LOGIN', ' = ', 'user.user_id')
				->whereIn('user.voided', array ('1', '2'))->whereIn('user.user_status', array('0', '1', '2', '4'))
				->where(function ($subWhere) use ($data) {
					$this->_exte_set_search_condition($subWhere, $data);
				});
			
			return $this->_exte_get_query_sql_data($query_sql, $totalType, 'user.rec_crt_date');
		}
		
		protected function get_all_cust_page_sumdata($id_list, $data, $totalType)
		{
			$_rs            = array ();
			
			if ($totalType == 'pageTotal') {
				foreach ($id_list as $key => $vdata) {
					$_one_sumdata[$vdata['user_id']] = Mt4Trades::selectRaw ("
						/*手续费*/
						abs( sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.commission else 0 end ) ) as total_comm,
						/*客户余额入金*/
						sum( case when mt4_trades.profit > 0 and mt4_trades.CMD in (6) and mt4_trades.COMMENT NOT LIKE '%Adj%' then mt4_trades.profit else 0 end ) as total_yuerj,
						/*客户余额出金*/
						sum( case when mt4_trades.profit < 0 and mt4_trades.CMD in (6) and mt4_trades.COMMENT NOT LIKE '%Adj%' then mt4_trades.profit else 0 end ) as total_yuecj,
						/*手数*/
						sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.volume else 0 end ) as total_volume,
						/*利息*/
						abs( sum( case when mt4_trades.swaps < 0 and mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.swaps else 0 end ) ) as total_swaps,
						/*盈亏*/
						sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.profit else 0 end ) as total_profit,
						/*贵金属*/
					     sum( case when mt4_trades.symbol in ( select sym_symbol from symbol_prices where sym_grp_id = 1 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.volume else 0 end ) as total_noble_metal,
					    /*外汇*/
					     sum( case when mt4_trades.symbol in ( select sym_symbol from symbol_prices where sym_grp_id = 2 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.volume else 0 end ) as total_for_exca,
					    /*原油*/
					     sum( case when mt4_trades.symbol in ( select sym_symbol from symbol_prices where sym_grp_id = 3 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.volume else 0 end ) as total_crud_oil,
					    /*指数*/
					     sum( case when mt4_trades.symbol in ( select sym_symbol from symbol_prices where sym_grp_id = 4 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.volume else 0 end ) as total_index
					")->where('LOGIN', $vdata['user_id'])->get()->toArray();
				}
				
				for ($i = 0; $i < count($_one_sumdata); $i ++) {
					/*手续费*/
					$_rs[$id_list[$i]['user_id']]['total_comm']                 = number_format ($_one_sumdata[$id_list[$i]['user_id']][0]['total_comm'], 2, '.', '');
					/*客户余额入金*/
					$_rs[$id_list[$i]['user_id']]['total_yuerj']                = number_format ($_one_sumdata[$id_list[$i]['user_id']][0]['total_yuerj'], 2, '.', '');
					/*客户余额出金*/
					$_rs[$id_list[$i]['user_id']]['total_yuecj']                = number_format ($_one_sumdata[$id_list[$i]['user_id']][0]['total_yuecj'], 2, '.', '');
					/*手数*/
					$_rs[$id_list[$i]['user_id']]['total_volume']               = $_one_sumdata[$id_list[$i]['user_id']][0]['total_volume'] / 100;
					/*利息*/
					$_rs[$id_list[$i]['user_id']]['total_swaps']                = number_format ($_one_sumdata[$id_list[$i]['user_id']][0]['total_swaps'], 2, '.', '');
					/*盈亏*/
					$_rs[$id_list[$i]['user_id']]['total_profit']               = number_format ($_one_sumdata[$id_list[$i]['user_id']][0]['total_profit'], 2, '.', '');
					/*贵金属*/
					$_rs[$id_list[$i]['user_id']]['total_noble_metal']          = $_one_sumdata[$id_list[$i]['user_id']][0]['total_noble_metal'] / 100;
					/*外汇*/
					$_rs[$id_list[$i]['user_id']]['total_for_exca']             = $_one_sumdata[$id_list[$i]['user_id']][0]['total_for_exca'] / 100;
					/*原油*/
					$_rs[$id_list[$i]['user_id']]['total_crud_oil']             = $_one_sumdata[$id_list[$i]['user_id']][0]['total_crud_oil'] / 100;
					/*指数*/
					$_rs[$id_list[$i]['user_id']]['total_index']                = $_one_sumdata[$id_list[$i]['user_id']][0]['total_index'] / 100;
					/*净入金 = 入金 - 出金*/
					$_rs[$id_list[$i]['user_id']]['total_net_worth']            = number_format(($_one_sumdata[$id_list[$i]['user_id']][0]['total_yuerj']- abs($_one_sumdata[$id_list[$i]['user_id']][0]['total_yuecj'])), 2, '.', '');
				}
			}
			
			return $_rs;
		}
		
		protected function get_all_cust_sumdata($data)
		{
			$_all_rs        = array ();
			
			$_allsumdata['search_total'] = Mt4Trades::selectRaw ("
					/*总手续费*/
					abs( sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.commission else 0 end ) ) as total_comm,
					/*客户总余额入金*/
					sum( case when mt4_trades.profit > 0 and mt4_trades.CMD in (6) and mt4_trades.COMMENT NOT LIKE '%Adj%' then mt4_trades.profit else 0 end ) as total_yuerj,
					/*客户总余额出金*/
					sum( case when mt4_trades.profit < 0 and mt4_trades.CMD in (6) and mt4_trades.COMMENT NOT LIKE '%Adj%' then mt4_trades.profit else 0 end ) as total_yuecj,
					/*总手数 == 总交易量*/
					sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.volume else 0 end ) as total_volume,
					/*总利息*/
					abs( sum( case when mt4_trades.swaps < 0 and mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.swaps else 0 end ) ) as total_swaps,
					/*总盈亏*/
					sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.profit else 0 end ) as total_profit,
					/*总贵金属*/
					 sum( case when mt4_trades.symbol in ( select sym_symbol from symbol_prices where sym_grp_id = 1 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.volume else 0 end ) as total_noble_metal,
					/*总外汇*/
					 sum( case when mt4_trades.symbol in ( select sym_symbol from symbol_prices where sym_grp_id = 2 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.volume else 0 end ) as total_for_exca,
					/*总原油*/
					 sum( case when mt4_trades.symbol in ( select sym_symbol from symbol_prices where sym_grp_id = 3 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.volume else 0 end ) as total_crud_oil,
					/*总指数*/
					 sum( case when mt4_trades.symbol in ( select sym_symbol from symbol_prices where sym_grp_id = 4 and voided = 1 ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.volume else 0 end ) as total_index
				")->whereIn('mt4_trades.LOGIN', function ($whereIn) use ($data) {
				$whereIn->select('user.user_id')->from('user')
					->join('mt4_users', 'mt4_users.LOGIN', ' = ', 'user.user_id')
					->whereIn('user.voided', array ('1', '2'))->whereIn('user.user_status', array('0', '1', '2', '4'))
					->where(function ($subWhere) use ($data) {
						$this->_exte_set_search_condition($subWhere, $data);
					});
			})->get()->toArray();
			
			//总余额，净值
			$_allsumdata['search_bal_eqy'] = Mt4Users::selectRaw('
				/*余额*/
				sum(mt4_users.BALANCE) as all_total_bal,
				/*净值*/
				sum(mt4_users.EQUITY) as all_total_eqy
			')->whereIn('mt4_users.LOGIN', function ($whereIn) use ($data) {
				$whereIn->select('user.user_id')->from('user')
					->join('mt4_users', 'mt4_users.LOGIN', ' = ', 'user.user_id')
					->whereIn('user.voided', array ('1', '2'))->whereIn('user.user_status', array('0', '1', '2', '4'))
					->where(function ($subWhere) use ($data) {
						$this->_exte_set_search_condition($subWhere, $data);
					});
			})->get()->toArray();
			
			/*手续费*/
			$_all_rs['search_total_comm']               = number_format($_allsumdata['search_total'][0]['total_comm'], 2, '.', '');
			/*客户余额入金*/
			$_all_rs['search_total_yuerj']              = number_format($_allsumdata['search_total'][0]['total_yuerj'], 2, '.', '');
			/*客户余额出金*/
			$_all_rs['search_total_yuecj']              = number_format($_allsumdata['search_total'][0]['total_yuecj'], 2, '.', '');
			/*手数*/
			$_all_rs['search_total_volume']             = $_allsumdata['search_total'][0]['total_volume'] / 100;
			/*利息*/
			$_all_rs['search_total_swaps']              = number_format($_allsumdata['search_total'][0]['total_swaps'], 2, '.', '');
			/*盈亏*/
			$_all_rs['search_total_profit']             = number_format($_allsumdata['search_total'][0]['total_profit'], 2, '.', '');
			/*贵金属*/
			$_all_rs['search_total_noble_metal']        = $_allsumdata['search_total'][0]['total_noble_metal'] / 100;
			/*外汇*/
			$_all_rs['search_total_for_exca']           = $_allsumdata['search_total'][0]['total_for_exca'] / 100;
			/*原油*/
			$_all_rs['search_total_crud_oil']           = $_allsumdata['search_total'][0]['total_crud_oil'] / 100;
			/*指数*/
			$_all_rs['search_total_index']              = $_allsumdata['search_total'][0]['total_index'] / 100;
			/*净入金 = 入金 - 出金*/
			$_all_rs['search_total_net_worth']          = number_format(($_allsumdata['search_total'][0]['total_yuerj'] - abs($_allsumdata['search_total'][0]['total_yuecj'])), 2, '.', '');
			/*总余额*/
			$_all_rs['search_total_bal']                = number_format($_allsumdata['search_bal_eqy'][0]['all_total_bal'], 2, '.', '');
			/*总净值*/
			$_all_rs['search_total_eqy']                = number_format($_allsumdata['search_bal_eqy'][0]['all_total_bal'], 2, '.', '');
		
			return $_all_rs;
		}
		
		protected function get_all_change_list($totalType, $request)
		{
			$user_id        = $request->userId;
			$apply_status   = $request->trans_apply_status;
			$startdate      = $request->startdate;
			$enddate        = $request->enddate;
			
			$query_sql = TransApplyLog::select(
				'trans_apply_log.trans_id as transId',
				'trans_apply_log.trans_uid as transUid',
				'trans_apply_log.trans_type_gid as transTypeGid',
				'trans_apply_log.trans_type_name as transTypeName',
				'trans_apply_log.trans_apply_uid as transApplyUid',
				'trans_apply_log.trans_apply_uname as transApplyUname',
				'trans_apply_log.trans_apply_status as transApplyStatus',
				'trans_apply_log.trans_apply_reason as transApplyReason',
				'trans_apply_log.rec_crt_date'
			)->where('trans_apply_log.voided', '1')
				->where(function($query) use($user_id, $apply_status, $startdate, $enddate) {
					if (!empty($startdate) && !empty($enddate) && $this->_exte_is_Date ($startdate) && $this->_exte_is_Date ($enddate)) {
						$query->whereBetween('trans_apply_log.rec_crt_date', [$startdate .' 00:00:00', $enddate . ' 23:59:59']);
					} else {
						if(!empty($startdate) && $this->_exte_is_Date ($startdate)) {
							$query->where('trans_apply_log.rec_crt_date',  '>=', $startdate .' 23:59:59');
						}
						if(!empty($enddate) && $this->_exte_is_Date ($enddate)) {
							$query->where('trans_apply_log.rec_crt_date', '<', $enddate .' 00:00:00');
						}
					}
					
					if(!empty($user_id)) {
						$query->where('trans_apply_log.trans_uid', 'like', '%' . $user_id . '%');
					}
					if($apply_status != "") {
						$query->where('trans_apply_log.trans_apply_status', $apply_status);
					}
					
					
				});
			
			if ($totalType == 'page') {
				$id_list = $query_sql->skip($this->_offset)->take($this->_pageSize)->orderBy('trans_apply_log.rec_crt_date', 'desc')->get()->toArray();
			} else if ($totalType == 'count') {
				$id_list = $query_sql->count();
			} else if ($totalType == 'sum') {
				$id_list = $query_sql->get()->toArray();
			}
			
			return $id_list;
		}
		
		protected function get_act_chang_info($list) {
			
			$_rs = array();
			
			if(!empty($list)) {
				foreach ($list as $k => $v) {
					$_rs[$v['transUid']]['bal'] = Mt4Users::select('LOGIN', 'NAME', 'BALANCE', 'CREDIT')->where('LOGIN', $v['transUid'])->get()->toArray();
					$_rs[$v['transUid']]['vol'] = Mt4Trades::where('LOGIN', $v['transUid'])->where('CLOSE_TIME', '1970-01-01 00:00:00')->whereIn('CMD', array(0, 1, 2, 3, 4, 5))->where('CONV_RATE1', '<>', 0)->count();
				}
			}
			
			return $_rs;
		}
		
		protected function _exte_mt4_update_user2($userId, $fill = false)
		{
			$this->_str_rala = $this->_exte_show_account_relationship_chain($userId, '-', 'id', 'admin');
			$col_ary['country'] = $this->_str_rala;
			$col_ary['login'] = $userId;
			$mt4_upd = $this->_exte_mt4_update_user($col_ary);
		}
		
		protected function _exte_set_search_condition($subWhere, $data)
		{
			if (!empty($data['startdate']) && !empty($data['enddate']) && $this->_exte_is_Date ($data['startdate']) && $this->_exte_is_Date ($data['enddate'])) {
				$subWhere->whereBetween('mt4_users.REGDATE', [$data['startdate'] .' 00:00:00', $data['enddate'] . ' 23:59:59']);
			} else {
				if(!empty($data['startdate']) && $this->_exte_is_Date ($data['startdate'])) {
					$subWhere->where('mt4_users.REGDATE',  '>= ', $data['startdate'] .' 23:59:59');
				}
				if(!empty($data['enddate']) && $this->_exte_is_Date ($data['enddate'])) {
					$subWhere->where('mt4_users.REGDATE', '<', $data['enddate'] .' 00:00:00');
				}
			}
			
			if (!empty($data['userId'])) {
				$subWhere->where(function ($subOrWhere) use ($data) {
					$subOrWhere->where('mt4_users.LOGIN', 'like', '%' . $data['userId'] . '%')->orWhere('mt4_users.ID', 'like', '%' . $data['userId'] . '%');
				});
			}
			if (!empty($data['username'])) {
				$subWhere->where('mt4_users.NAME', 'like', '%' . $data['username'] . '%');
			}
			if (!empty($data['userstatus'])) {
				$subWhere->where('user.user_status', $data['userstatus']);
			}
			
			return $subWhere;
		}
	}