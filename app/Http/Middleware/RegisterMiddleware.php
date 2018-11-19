<?php
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/2/5
	 * Time: 16:47
	 */
	
	namespace App\Http\Middleware;
	
	use Closure;
	use App\Model\Agents;
	
	class RegisterMiddleware
	{
		public function handle($request, Closure $next) {
			if ($request->user_id == '' && $request->register_type == '' && $request->comm_type == '') {
				return view('user.register.register')->with(['_parent_id' =>'',  '_parent_grpId' => '', '_is_invite' => 'FALSE', '_register_type' => 'user', '_comm_type' => '', '_parent_type' => '']);
			} else {
				if(!in_array($request->register_type, array('agents', 'user'))) { //agents user
					return "<script type=\"text/javascript\">alert('该邀请注册链接无效或有误！');window.location='/';</script>";
				} /*else if($request->user_id != '' && preg_match('/\s/', $request->user_id)) {
					return "<script type=\"text/javascript\">alert('该邀请注册链接无效或有误！');window.location='/';</script>";
				}*/ else if($request->register_type == 'agents' && $request->user_id != '' && $request->comm_type != '') {
					return "<script type=\"text/javascript\">alert('该邀请注册链接无效或有误！');window.location='/';</script>";
				} else if ($request->comm_type != '' && $request->comm_type != 'A') {
					return "<script type=\"text/javascript\">alert('非法邀请注册链接！');window.location='/';</script>";
				} else {
					$_user_info = Agents::select('user_id', 'group_id', 'mt4_grp', 'is_confirm_agents_lvg', 'comm_prop', 'enable')
						->where('user_id', $request->user_id)->whereIn('voided', array ('1', '2'))->whereIn('user_status', array('0', '1', '2', '4'))->first();
					if(!isset($_user_info)) {
						return "<script type=\"text/javascript\">alert('该邀请码不存在！');window.location='/';</script>";
					} else if ((int)$_user_info['group_id'] >= 4 && $request->register_type == 'agents') {
						return "<script type=\"text/javascript\">alert('该邀请码无权邀请代理商注册！');window.location='/';</script>";
					} else if ($_user_info['is_confirm_agents_lvg'] == '0') {
						return "<script type=\"text/javascript\">alert('该邀请码还没确定代理级别,暂不能邀请他人注册!');window.location='/';</script>";
					} else if ((int)$_user_info['comm_prop'] <= 50 && $request->comm_type != '') {
						return "<script type=\"text/javascript\">alert('该邀请码不能邀请注册无佣金客户!');window.location='/';</script>";
					}
					
					if ($request->register_type == 'agents') {
						$comm_type = $_user_info->mt4_grp;
					} else if($request->register_type == 'user') {
						if(isset($request->comm_type) && $request->comm_type == 'A') {
							//无佣金客户
							$strlen    = strpos($_user_info->mt4_grp, $request->comm_type);
							//$comm_type = 'B' . substr($_user_info->mt4_grp, 1);
							$comm_type = substr($_user_info->mt4_grp, 0, $strlen - 1) . '-B';
						} else if($request->comm_type == '') {
							$comm_type = $_user_info->mt4_grp;
						}
					}
					
					return view('user.register.register')->with([
						'_parent_id'             => $_user_info->user_id,
						'_parent_grpId'          => $_user_info->group_id,
						'_register_type'         => $request->register_type,
						'_comm_type'             => $comm_type,
						'_parent_type'           => $_user_info->user_id . $request->comm_type,
						'_is_invite'             => 'TRUE',
					]);
				}
			}
		}
	}