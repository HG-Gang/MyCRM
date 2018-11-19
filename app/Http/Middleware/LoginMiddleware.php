<?php
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/2/4
	 * Time: 16:54
	 */
	
	namespace App\Http\Middleware;
	
	use Closure;
	
	class LoginMiddleware
	{
		public function handle($request, Closure $next) {
			
			$excpt_action = array(
				'user/captcha',
				'user/signIn',
				'user/loginOut',
				'user/realtime/rebate_detail/{orderNo}/{role}',
				'user/cust/show_direct_cust_info/{role}/{uid}',
				'user/cust/loginHistorySearch/{uid}',
				'user/proxy/direct_cust_detail/{puid}',
				'user/proxy/direct_cust_detail_list',
				'user/position/comm_summary',
				'user/deposit_notfiy',
				'user/deposit_return',
				'user/deposit_notfiy2',
				'user/deposit_return2',
			);
			
			if (in_array($request->route()->uri(), $excpt_action, true)) {
				return $next($request);
			} else {
				if ($request->session()->has('suser')) {
					return $next($request);
				} else {
					return "<script type='text/javascript'>alert('会话已过期，请重新登录');top.location.href ='/user/loginOut';</script>";
				}
			}
		}
	}