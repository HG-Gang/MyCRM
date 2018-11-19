<?php
	
	#对后台开启csrf过滤
	//Route::when('admin/*', 'csrf', ['post','delete','put']);
	
	//后台管理系统路由
	Route::group(['prefix' => route_prefix()], function() {
		###登录页面
		Route::get('login', 'Admin\LoginController@index');
		###验证码
		Route::get('captcha', 'Admin\LoginController@captcha');
		###判断登陆
		Route::post('logon', 'Admin\LoginController@logon');
		###登出
		Route::get('logout', 'Admin\LoginController@logout');
		Route::group(['middleware' => 'permissions'], function() {
			###后台首页
			Route::get('index', 'Admin\AdminController@index');
			###后台内容首页
			Route::get('welcome', 'Admin\AdminController@create');
			###角色列表
			Route::get('role', 'Admin\RoleController@index');
			###添加角色
			Route::get('role/add', 'Admin\RoleController@create');
			###保存角色
			Route::post('role/addsave', 'Admin\RoleController@store');
			###修改角色
			Route::get('role/edit/{id?}', 'Admin\RoleController@show');
			###保存修改角色
			Route::post('role/editsave', 'Admin\RoleController@editsave');
			###删除角色
			Route::get('role/del', 'Admin\RoleController@del');
			###管理员列表
			Route::get('Administrators', 'Admin\AdministratorsController@index');
			###管理员停用
			Route::get('Administrators/stop', 'Admin\AdministratorsController@stop');
			###管理员启用
			Route::get('Administrators/start', 'Admin\AdministratorsController@start');
			###管理员编辑
			Route::get('Administrators/edit/{id?}', 'Admin\AdministratorsController@show');
			###保存管理员
			Route::post('Administrators/editsave', 'Admin\AdministratorsController@save');
			###添加管理员
			Route::get('Administrators/add', 'Admin\AdministratorsController@add');
			###保存管理员
			Route::post('Administrators/addsave', 'Admin\AdministratorsController@addsave');
			###删除管理员
			Route::get('Administrators/del', 'Admin\AdministratorsController@del');
			###个人资料
			Route::get('userinfo', 'Admin\AdminController@UserInfo');
			###保存个人资料
			Route::post('userinfo/save', 'Admin\AdminController@UserIfoSave');
			##显示修改密码
			Route::get('userpwd', 'Admin\AdminController@UserPwd');
			###保存修改密码
			Route::post('userpwd/save', 'Admin\AdminController@UserPewdSave');
			################代理商管理##############################
			####代理商列表
			Route::get('agent/{user_id?}', 'Admin\AgentController@index');
			Route::get('agents_list', 'Admin\AgentController@agents_list_browse');
			Route::get('agents/agents_edit_info/{uid}', 'Admin\AgentController@agents_edit_info');
			Route::post('agents/agents_edit_save', 'Admin\AgentController@agents_edit_save');
			Route::post('agents/agentsListSearch', 'Admin\AgentController@agentsListSearch');
			
			###查看直属客户
			Route::get('customer/{user_id?}', 'Admin\AgentController@CustomerList')->where('user_id', '[0-9]+');
			###编辑代理商
			Route::get('agent/edit/{user_id?}','Admin\AgentController@AgentEdir')->where('user_id', '[0-9]+');;
			###保存代理商
			Route::post('agent/update','Admin\AgentController@AgentUpdate');
			###添加代理商
			Route::get('agents_add', 'Admin\AgentController@agents_add_browse');
			Route::post('agents_save', 'Admin\AgentController@agents_save');
			
			#################end##################################
			################订单管理##############################
			###仓位总结
			Route::get('order/position_summary_list', 'Admin\PositionSummaryController@position_summary_list');
			Route::post('order/positionSummarySearch', 'Admin\PositionSummaryController@positionSummarySearch');
			###已平仓单
			Route::get('order/close_list', 'Admin\AdminCloseOrderController@close_list');
			Route::post('order/closeListSearch', 'Admin\AdminCloseOrderController@closeListSearch');
			###未平仓单
			Route::get('order/open_list', 'Admin\AdminOpenOrderController@open_list');
			Route::post('order/openlistSearch', 'Admin\AdminOpenOrderController@openlistSearch');
			###实时返佣
			Route::get('order/real_commission_list', 'Admin\AdminRealCommissionController@real_commission_list');
			Route::post('order/realCommissionListSearch', 'Admin\AdminRealCommissionController@realCommissionListSearch');
			###爆仓列表
			Route::get('order/whs_exp_zero_list', 'Admin\AdminWhsExpZeroController@whs_exp_zero_list');
			Route::post('order/whsExpZeroListSearch', 'Admin\AdminWhsExpZeroController@whsExpZeroListSearch');
			#################end##################################
			################客户管理##############################
			Route::get('cust/list', 'Admin\CustomerController@user_list');
			Route::get('cust/add', 'Admin\CustomerController@cust_add_browse');
			Route::get('cust/cust_detail/{acc_uid}', 'Admin\CustomerController@cust_detail');
			Route::get('cust/change_list', 'Admin\CustomerController@change_list');
			Route::post('cust/custListSearch', 'Admin\CustomerController@custListSearch');
			Route::post('cust/custChangeListSearch', 'Admin\CustomerController@custChangeListSearch');
			Route::post('cust/cust_apply_pass', 'Admin\CustomerController@cust_apply_pass');
			Route::post('cust/cust_apply_nopass', 'Admin\CustomerController@cust_apply_nopass');
			Route::post('cust/cust_save_info', 'Admin\CustomerController@cust_save_info');
			Route::post('cust/cust_save_add', 'Admin\CustomerController@cust_save_add');
			#################end##################################
			################财务管理##############################
			###出金申请
			Route::get('amount/withdraw_apply', 'Admin\WithdrawAmountController@withdraw_apply');
			Route::get('amount/orderId_detail/{orderId}', 'Admin\WithdrawAmountController@withdrawOrderIdDetail');
			Route::post('amount/order_status', 'Admin\WithdrawAmountController@withdrawOrderStaus');
			Route::post('amount/withdrawApplySearch', 'Admin\WithdrawAmountController@withdrawApplySearch');
			Route::post('amount/withdrawApplyExport', 'Admin\WithdrawAmountController@withdrawExport');
			Route::get('amount/withdraw_downloadfile/{file}/{role}', 'Admin\WithdrawAmountController@withdraw_downloadfile')->name('admin_withdraw_download');
			###入金流水
			Route::get('amount/deposit_flow', 'Admin\DepositAmountController@deposit_flow');
			Route::post('amount/depositFlowSearch', 'Admin\DepositAmountController@depositFlowSearch');
			Route::post('amount/depositExport', 'Admin\DepositAmountController@depositExport');
			Route::get('amount/depositDownloadfile/{file}/{role}', 'Admin\DepositAmountController@DownloadFile')->name('admin_deposit_download');
			###出金流水
			Route::get('amount/withdraw_flow', 'Admin\WithdrawFlowController@withdraw_flow');
			Route::post('amount/withdrawFlowSearch', 'Admin\WithdrawFlowController@withdrawFlowSearch');
			Route::post('amount/withdrawFlowExport', 'Admin\WithdrawFlowController@withdrawExport');
			Route::get('amount/withdrawDownloadfile/{file}/{role}', 'Admin\WithdrawFlowController@DownloadFile')->name('admin_withdraw_flow_download');
			###权益统计
			Route::get('amount/rights_summary', 'Admin\RightsSummaryController@rights_summary_browse');
			Route::get('amount/rightsSummarySearchDetail/{uid}/{status}/{sumdata}', 'Admin\RightsSummaryController@RightsSummarySearchDetail');
			Route::post('amount/rightsSummarySearch', 'Admin\RightsSummaryController@RightsSummarySearch');
			Route::post('amount/confirm_options', 'Admin\RightsSummaryController@ConfirmWithdrawOrdeposit');
			Route::post('amount/manual_confirm_options', 'Admin\RightsSummaryController@ManualConfirmWithdrawOrdeposit');
			Route::post('amount/rightsSumExport', 'Admin\RightsSummaryController@rightsSumExport');
			Route::get('amount/rights_downloadfile/{file}/{role}', 'Admin\RightsSummaryController@DownloadFile')->name('admin_rights_download');
			
			###批量操作
			Route::get('amount/batch_operation', 'Admin\BatchAmountController@batch_operation_browse');
			Route::get('amount/batch_operation_withdraw', 'Admin\BatchAmountController@batch_operation_withdraw_browse');
			Route::post('amount/batchOperation', 'Admin\BatchAmountController@batchOperation');
			Route::post('amount/batchOperationWithdraw', 'Admin\BatchAmountController@batchOperationWithdraw');
			
			###风险管理
			Route::get('fengXian/profit_list', 'Admin\FengXianManageController@fengXian_profit_browse');
			Route::get('fengXian/position_list', 'Admin\FengXianManageController@fengXian_position_browse');
			Route::post('fengXian/profitSearch', 'Admin\FengXianManageController@fengXian_profit_list');
			Route::post('fengXian/positionSearch', 'Admin\FengXianManageController@fengXian_position_list');
			
			###外汇牌价
			Route::get('amount/whpj_rate', 'Admin\ExchangeRateController@whpj_rate_browse');
			Route::post('amount/whpj_rate_save', 'Admin\ExchangeRateController@whpj_rate_save');
			
			#################end##################################
			################账户审核/认证##########################
			
			###待审核
			Route::get('auth/user_examine', 'Admin\AuthenticationController@user_examine');
			Route::get('auth/user_examine/detail/{uid}', 'Admin\AuthenticationController@user_examine_detail');
			Route::post('auth/userExaminSearch', 'Admin\AuthenticationController@userExaminSearch');
			###客服审核用户信息
			Route::post('auth/user_idcard_bank', 'Admin\AuthenticationController@user_idcard_bank');
			
			###已审核
			Route::get('auth/user_certified', 'Admin\AuthenticationController@user_certified');
			Route::get('auth/user_certified_detail/{uid}', 'Admin\AuthenticationController@userCertifiedDetail');
			Route::post('auth/userCertifiedSearch', 'Admin\AuthenticationController@userCertifiedSearch');
			#################end##################################
			################销户管理##############################
			###销户列表
			Route::get('cancel/user_list', 'Admin\CancellationController@cancel_user_list');
			Route::post('cancel/userlistSearch', 'Admin\CancellationController@userlistSearch');
			Route::post('cancel/cancel_apply_pass', 'Admin\CancellationController@cancel_apply_pass');
			Route::post('cancel/cancel_apply_nopass', 'Admin\CancellationController@cancel_apply_nopass');
			#################end##################################
			################资讯管理##############################
			###新闻列表
			Route::get('news/news_list_browse', 'Admin\NewsInfoController@news_list_browse');
			Route::get('news/news_add_browse', 'Admin\NewsInfoController@new_add_browse');
			Route::get('news/news_edit/{newsid}', 'Admin\NewsInfoController@news_edit');
			Route::post('news/newsListSearch', 'Admin\NewsInfoController@newsListSearch');
			Route::post('news/news_save', 'Admin\NewsInfoController@news_save');
			Route::post('news/news_update', 'Admin\NewsInfoController@news_update');
			#################end##################################
		});
	});

