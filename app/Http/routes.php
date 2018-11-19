<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
	Route::get('user/login', ['as' => 'login', 'uses' => 'User\LoginController@login']);
	Route::get('/', function() {
		return redirect()->route('login');
	});
	
	//注册验证
	Route::post('user/register/registerVerifyInfo', 'User\RegisterController@registerVerifyInfo');
	Route::post('user/register/registerSendCode', 'User\RegisterController@registerSendCode');
	Route::post('user/register/registerinto', 'User\RegisterController@registerinto');
	
	//前后台不同角色查看已平仓单详情
	Route::get('close/order_detail/{orderId}/{orderType}/{role}', 'User\CloseOrderController@close_order_detail');
	
	//前后台不同角色查看未平仓单详情
	Route::get('open/order_detail/{orderId}/{orderType}/{role}', 'User\OpenOrderController@open_order_detail');
	
	//前后台不同角色查看订单人的详情
	Route::get('show/user_detail/{userId}/{role}', 'User\LoginController@show_user_detail');
	
	Route::group(['prefix' => 'user/register', 'middleware' => 'RegisterMiddleware'], function () {
		Route::get('{register_type?}/{user_id?}/{comm_type?}', 'User\RegisterController@index');
	});
	
	//得到用户关系链
	Route::post('user/relationShip', 'User\UserCenterController@relationShip');
	Route::post('user/relationShipHtml', 'User\UserCenterController@relationShipHtml');
	
	//忘记密码
	Route::get('user/forget_password', 'User\UserForgetPswController@forget_password_browse');
	Route::post('user/check_user_info', 'User\UserForgetPswController@checkUserInfo');
	Route::post('user/forgetpswSendCode', 'User\UserForgetPswController@forgetpswSendCode');
	Route::post('user/forgetPasswordInfoVerification', 'User\UserForgetPswController@forgetPasswordInfoVerification');
	Route::post('user/change_password', 'User\UserForgetPswController@saveChangePassword');
	
	Route::group(['prefix' => 'user', 'middleware' => 'LoginMiddleware'], function () {
		Route::get('captcha', 'User\LoginController@captcha');
		Route::post('signIn', 'User\LoginController@signIn');
		Route::get('index', 'User\LoginController@index')->name('userIndex');
		Route::get('loginOut', 'User\LoginController@loginOut');
		Route::get('main/home', 'User\LoginController@mainHome');
		
		//用户个人资料中心
		Route::get('center', 'User\UserCenterController@user_info_browse');
		Route::get('center/uploadIdCard', 'User\UserCenterController@uploadIdCard_browse');
		Route::get('center/uploadBank', 'User\UserCenterController@uploadBank_browse');
		Route::get('center/uploadChangeBank/{type}', 'User\UserCenterController@uploadChangeBank_browse');
		Route::get('center/uploadHead_browse', 'User\UserCenterController@uploadHead_browse');
		Route::get('center/updPhoneEmail/{type}', 'User\UserCenterController@updPhoneEmail_browse');
		Route::get('center/cancelAccount', 'User\UserCenterController@cancelAccount_browse');
		Route::post('center/cancelVerifyInfo', 'User\UserCenterController@cancelVerifyInfo');
		Route::post('center/cancelVerifyPassSendCode', 'User\UserCenterController@cancelVerifyPassSendCode');
		Route::post('center/uploadIdCard', 'User\UserCenterController@uploadIdCard');
		Route::post('center/uploadBankCard', 'User\UserCenterController@uploadBankCard');
		Route::post('center/uploadChangeBankCard', 'User\UserCenterController@uploadChangeBankCard');
		Route::post('center/updateVerifyInfo', 'User\UserCenterController@updateVerifyInfo');
		Route::post('center/changeBankCardVerifyCode', 'User\UserCenterController@changeBankCardVerifyCode');
		Route::post('center/updVerifyPassSendCode', 'User\UserCenterController@updVerifyPassSendCode');
		Route::post('center/changeBankCardSendCode', 'User\UserCenterController@changeBankCardSendCode');
		Route::post('center/updatePhoneEmailInfo', 'User\UserCenterController@updatePhoneEmailInfo');
		Route::post('center/ajaxCancelAccount', 'User\UserCenterController@ajaxCancelAccount');
		Route::post('center/uploadHeadImg', 'User\UserCenterController@uploadHeadImg');
		
		//修改密码
		Route::get('editpsw', 'User\UserCenterController@user_editpsw_browse');
		Route::post('editpsw_save', 'User\UserCenterController@user_editpsw_save');
		//账户存款
		Route::get('deposit', 'User\UserDepositController@deposit_browse');
		Route::any('deposit_request', 'User\UserDepositController@deposit_request')->name('user_deposit_request');
		//异步
		Route::any('deposit_notfiy', 'PayController\PayCallBackController@deposit_notify_response_success')->name('user_deposit_notfiy');
		Route::any('deposit_notfiy2', 'PayController\PayCallBackController@deposit_notify_response_success2')->name('user_deposit_notfiy2');
		//页面
		Route::any('deposit_return', 'PayController\PayCallBackController@deposit_return_response_success')->name('user_deposit_return');
		Route::any('deposit_return2', 'PayController\PayCallBackController@deposit_return_response_success2')->name('user_deposit_return2');
		
		//账户取款
		Route::get('withdraw', 'User\UserWithdrawController@withdraw_browse');
		Route::post('withdraw_request', 'User\UserWithdrawController@withdraw_request');
		
		//账户流水
		Route::group(['prefix' => 'flow'], function () {
			//我的账户流水
			Route::get('main', 'User\CustomerFlowController@main_browse');
			//入金流水
			Route::post('depositFlowSearch', 'User\CustomerFlowController@depositFlowSearch');
			//出金流水
			Route::post('withdrawalFlowSearch', 'User\CustomerFlowController@withdrawalFlowSearch');
			//出金申请
			Route::post('withdrawApplyFlowSearch', 'User\CustomerFlowController@withdrawApplyFlowSearch');
			//直属入金流水
			Route::post('directDepositFlowSearch', 'User\CustomerFlowController@directDepositFlowSearch');
			Route::post('depositExport', 'User\CustomerFlowController@depositExport');
			Route::get('downloadfile/{file}/{role}', 'User\CustomerFlowController@DownloadFile')->name('download');
			//直属出金流水
			Route::post('directWithdrawalFlowSearch', 'User\CustomerFlowController@directWithdrawalFlowSearch');
		});
		
		//代理商列表
		Route::get('proxy/list', 'User\ProxyListController@proxy_list_browse');
		Route::get('proxy/confirm', 'User\ProxyListController@proxy_confirm_browse');
		Route::get('proxy/direct_cust_detail/{puid}', 'User\ProxyListController@proxy_direct_cust_detail');
		Route::post('proxy/proxyListSearch', 'User\ProxyListController@proxyListSearch');
		Route::post('proxy/proxyConfirmSearch', 'User\ProxyListController@proxyConfirmSearch');
		Route::post('proxy/confirmLevelChange', 'User\ProxyListController@confirmLevelChange');
		Route::post('proxy/direct_cust_detail_list', 'User\ProxyListController@direct_cust_detail_list');
		
		//代理商给直属下级代理商和直属客户佣金转户
		Route::get('proxy/direct_user_commTrans_browse/{uid}', 'User\ProxyListController@direct_user_commTrans_browse');
		Route::post('proxy/directUserCommTrans', 'User\ProxyListController@directUserCommTrans');
		
		//仓位总结
		Route::get('position/summary', 'User\PositionSummaryController@position_summary_browse');
		Route::get('position/comm_summary', 'User\PositionSummaryController@_exte_mt4_sync_comm_summary');
		Route::get('position/summary/deatil/{id}', 'User\PositionSummaryController@position_summary_detail');
		Route::post('position/positionSummarySearch', 'User\PositionSummaryController@positionSummarySearch');
		
		//已平仓单
		Route::get('close/order', 'User\CloseOrderController@close_order_browse');
		Route::post('close/closeOrderSearch', 'User\CloseOrderController@closeOrderSearch');
		
		//未平仓单
		Route::get('open/order', 'User\OpenOrderController@open_order_browse');
		Route::post('open/openOrderSearch', 'User\OpenOrderController@openOrderSearch');
		
		//普通客户仓位总结
		Route::get('position/summary2', 'User\PositionSummary2Controller@position_summary2_browse');
		Route::post('position/positionSummary2Search', 'User\PositionSummary2Controller@positionSummary2Search');
		
		//普通客户已平仓单
		Route::get('close/order2', 'User\CloseOrder2Controller@close_order2_browse');
		Route::post('close/closeOrder2Search', 'User\CloseOrder2Controller@closeOrder2Search');
		
		//普通客户未平仓单
		Route::get('open/order2', 'User\OpenOrder2Controller@open_order2_browse');
		Route::post('open/openOrder2Search', 'User\OpenOrder2Controller@openOrder2Search');
		
		//实时返佣
		Route::get('realtime/rebate', 'User\RealCommissionController@realtime_rebate_browse');
		Route::get('realtime/rebate_detail/{orderNo}/{role}', 'User\RealCommissionController@realtime_rebate_detail');
		Route::post('realtime/realtimeRebateSearch', 'User\RealCommissionController@realtimeRebateSearch');
		
		/*客户管理*/
		Route::get('cust/list', 'User\DirectCustomerController@cust_list_browse');
		Route::get('change/list', 'User\DirectCustomerController@cust_list_chang_group_browse');
		Route::get('cust/change/group/{uid}', 'User\DirectCustomerController@changeDirectCustGroupInfo');
		Route::post('cust/change/group_edit', 'User\DirectCustomerController@changeDirectCustGroupEdit');
		Route::post('cust/directCustListSearch', 'User\DirectCustomerController@directCustListSearch');
		Route::post('cust/directCustChangeListSearch', 'User\DirectCustomerController@directCustChangeListSearch');
		
		//代理商查看直属下级代理商和下级客户
		Route::get('cust/show_direct_cust_info/{role}/{uid}', 'User\DirectCustomerController@show_direct_cust_info');
		Route::post('cust/loginHistorySearch/{uid}', 'User\DirectCustomerController@loginHistorySearch');
		
		/*最新公告*/
		Route::get('news_list_browse', 'User\NewsListController@news_list_browse');
		Route::get('news/news_detail/{newsId}', 'User\NewsListController@news_detail');
		Route::post('newsListSearch', 'User\NewsListController@newsListSearch');
	});
	
	//权益统计测试
	Route::get('test_rights_sum', 'Admin\RightsSummaryController@sum_agents_online_settlement_amount');
	//test
	//Route::get('test', 'User\LoginController@show_user_detail');
	Route::get('test_info', 'admin\AgentController@test_info');
	Route::get('test_sms', 'User\LoginController@test_register');
	//Route::get('comm_summary', 'User\PositionSummaryController@_exte_mt4_sync_comm_summary');
	Route::get('test_serach/{id}', 'User\PositionSummaryController@test_serach_id');
	Route::post('test_export', 'User\PositionSummaryController@position_summary_export');
	
	//爆仓清零
	Route::get('trades_exp_zero', 'Admin\AdminWhsExpZeroController@trades_whs_exp_zero');




include_once('routes-admin.php');