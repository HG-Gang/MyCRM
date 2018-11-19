<?php
	
	return [
		[
			'menu_name' => '订单管理',
			'menu_tag' => 'Order',
			'sub_menu' => [
				[
					'menu_name' => '仓位总结',
					'menu_tag' => 'order_position_summary_list',
					'uri' => [
						'PositionSummaryController@position_summary_list' => '仓位总结列表',
						'PositionSummaryController@positionSummarySearch' => '查找',
					]
				],
				[
					'menu_name' => '已平仓单',
					'menu_tag' => 'order_close_list',
					'uri' => [
						'AdminCloseOrderController@close_list' => '已平仓单列表',
						'AdminCloseOrderController@closeListSearch' => '查找',
					]
				],
				[
					'menu_name' => '未平仓单',
					'menu_tag' => 'order_open_list',
					'uri' => [
						'AdminOpenOrderController@open_list' => '未平仓单列表',
						'AdminOpenOrderController@openlistSearch' => '查找',
					]
				],
				[
					'menu_name' => '实时返佣',
					'menu_tag' => 'oredr_real_commission_list',
					'uri' => [
						'AdminRealCommissionController@real_commission_list' => '实时返佣列表',
						'AdminRealCommissionController@realCommissionListSearch' => '查找',
					]
				],
				[
					'menu_name' => '爆仓列表',
					'menu_tag' => 'order_whs_exp_zero_list',
					'uri' => [
						'AdminWhsExpZeroController@whs_exp_zero_list' => '爆仓列表列表',
						'AdminWhsExpZeroController@whsExpZeroListSearch' => '查找',
					]
				],
			]
		],
		[
			'menu_name' => '客户管理',
			'menu_tag' => 'User',
			'sub_menu' => [
				[
					'menu_name' => '客户列表',
					'menu_tag' => 'user_list',
					'uri' => [
						'CustomerController@user_list' => '客户列表',
						'CustomerController@custListSearch' => '客户列表信息',
						'CustomerController@cust_detail' => '查看客户信息',
						'CustomerController@cust_save_info' => '更新客户信息',
					]
				],
				[
					'menu_name' => '添加客户',
					'menu_tag' => 'user_add',
					'uri' => [
						'CustomerController@cust_add_browse' => '客户列表',
						'CustomerController@cust_save_add' => '保存',
					]
				],
				/*[
					'menu_name' => '客户变更',
					'menu_tag' => 'change_list',
					'uri' => [
						'CustomerController@change_list' => '客户变更列表',
						'CustomerController@custChangeListSearch' => '变更列表信息',
						'CustomerController@cust_apply_pass' => '确认变更',
						'CustomerController@cust_apply_nopass' => '拒绝变更',
					]
				],*/
			]
		],
		[
			'menu_name' => '财务管理',
			'menu_tag' => 'Amount',
			'sub_menu' => [
				[
					'menu_name' => '出金申请',
					'menu_tag' => 'withdraw_apply',
					'uri' => [
						'WithdrawAmountController@withdraw_apply' => '出金申请列表',
						'WithdrawAmountController@withdrawApplySearch' => '查找',
						'WithdrawAmountController@withdrawOrderIdDetail' => '订单详情',
						'WithdrawAmountController@withdrawOrderStaus' => '订单操作',
					]
				],
				[
					'menu_name' => '入金流水',
					'menu_tag' => 'deposit_flow',
					'uri' => [
						'DepositAmountController@deposit_flow' => '入金流水列表',
						'DepositAmountController@depositFlowSearch' => '查找',
						'DepositAmountController@depositExport' => '导出Excel',
						'DepositAmountController@DownloadFile' => '下载Excel',
					]
				],
				[
					'menu_name' => '出金流水',
					'menu_tag' => 'withdraw_flow',
					'uri' => [
						'WithdrawFlowController@withdraw_flow' => '出金流水列表',
						'WithdrawFlowController@withdrawFlowSearch' => '查找',
						'WithdrawFlowController@withdrawExport' => '导出Excel',
						'WithdrawFlowController@DownloadFile' => '下载Excel',
					]
				],
				[
					'menu_name' => '权益统计',
					'menu_tag' => 'rights_summary',
					'uri' => [
						'RightsSummaryController@rights_summary_browse' => '权益统计列表',
						'RightsSummaryController@RightsSummarySearch' => '查找',
						'RightsSummaryController@RightsSummarySearchDetail' => '查看明细',
						'RightsSummaryController@DownloadFile' => '下载',
						'RightsSummaryController@rightsSumExport' => '导出Excel',
						'RightsSummaryController@ConfirmWithdrawOrdeposit' => '确认结算操作',
						'RightsSummaryController@ManualConfirmWithdrawOrdeposit' => '手动结算',
					]
				],
				[
					'menu_name' => '外汇牌价',
					'menu_tag' => 'whpj_rate',
					'uri' => [
						'ExchangeRateController@whpj_rate_browse' => '外汇牌价',
						'ExchangeRateController@whpj_rate_save' => '更新外汇牌价',
					]
				],
			]
		],
		
		[
			'menu_name' => '批量入金',
			'menu_tag' => 'Batch',
			'sub_menu' => [
				[
					'menu_name' => '批量入金',
					'menu_tag' => 'batch_operation',
					'uri' => [
						'BatchAmountController@batch_operation_browse' => '视图',
						'BatchAmountController@batchOperation' => '批量入金',
					]
				],
				[
					'menu_name' => '批量出金',
					'menu_tag' => 'batch_operation_withdraw',
					'uri' => [
						'BatchAmountController@batch_operation_withdraw_browse' => '视图',
						'BatchAmountController@batchOperationWithdraw' => '批量出金',
					]
				]
			]
		],
		
		[
				'menu_name' => '风险管理',
				'menu_tag' => 'FengXian',
				'sub_menu' => [
						[
								'menu_name' => '盈利风险',
								'menu_tag' => 'fengXian_profit',
								'uri' => [
										'FengXianManageController@fengXian_profit_browse' => '视图',
										'FengXianManageController@fengXian_profit_list' => '查找',
								]
						],
						[
								'menu_name' => '仓位风险',
								'menu_tag' => 'fengXian_position',
								'uri' => [
										'FengXianManageController@fengXian_position_browse' => '视图',
										'FengXianManageController@fengXian_position_list' => '查找',
								]
						],
				]
		],
		
		[
			'menu_name' => '资料审核',
			'menu_tag' => 'Auth',
			'sub_menu' => [
				[
					'menu_name' => '待审核',
					'menu_tag' => 'user_examine',
					'uri' => [
						'AuthenticationController@user_examine' => '待审核列表',
						'AuthenticationController@userExaminSearch' => '查找',
						'AuthenticationController@user_examine_detail' => '审核账户信息',
						'AuthenticationController@user_idcard_bank' => '认证操作',
					]
				],
				[
					'menu_name' => '已审核',
					'menu_tag' => 'user_certified',
					'uri' => [
						'AuthenticationController@user_certified' => '已审核列表',
						'AuthenticationController@userCertifiedSearch' => '查找',
						'AuthenticationController@userCertifiedDetail' => '查看账户信息',
					]
				],
			]
		],
		[
			'menu_name' => '销户管理',
			'menu_tag' => 'Cancel',
			'sub_menu' => [
				[
					'menu_name' => '销户列表',
					'menu_tag' => 'cancel_user_list',
					'uri' => [
						'CancellationController@cancel_user_list' => '销户列表列表',
						'CancellationController@userlistSearch' => '查找',
						'CancellationController@cancel_apply_pass' => '审核通过',
						'CancellationController@cancel_apply_nopass' => '审核不通过',
					]
				],
			]
		],
		[
			'menu_name' => '资讯管理',
			'menu_tag' => 'News',
			'sub_menu' => [
				[
					'menu_name' => '新闻列表',
					'menu_tag' => 'news_list',
					'uri' => [
						'NewsInfoController@news_list_browse' => '新闻列表',
						'NewsInfoController@newsListSearch' => '查找',
						'NewsInfoController@news_edit' => '编辑',
						'NewsInfoController@news_update' => '更新',
					
					]
				],
				[
					'menu_name' => '添加新闻',
					'menu_tag' => 'news_add',
					'uri' => [
						'NewsInfoController@news_add_browse' => '添加',
						'NewsInfoController@news_save' => '保存',
					
					]
				],
			]
		],
		[
			'menu_name' => '代理商管理',
			'menu_tag' => 'Agent',
			'sub_menu' => [
				/*[
					'menu_name' => '代理列表',
					'menu_tag' => 'Agent_list',
					'uri' => [
						'AgentController@index' => '查看代理列表',
						'AgentController@CustomerList'=>'查看直属客户',
						'AgentController@AgentEdir'=>'编辑代理商信息',
						'AgentController@AgentUpdate'=>'保存编辑代理商信息'
					]
				],*/
				[
					'menu_name' => '代理列表',
					'menu_tag' => 'Agent_list',
					'uri' => [
						'AgentController@agents_list_browse' => '代理列表',
						'AgentController@agentsListSearch' => '查看代理上列表',
						'AgentController@agents_edit_info' => '编辑代理商信息',
						'AgentController@agents_edit_save' => '保存编辑代理商信息',
					]
				],
				[
					'menu_name' => '添加代理',
					'menu_tag' => 'Agent_add',
					'uri' => [
						'AgentController@agents_add_browse' => '添加代理',
						'AgentController@agents_save' => '注册代理',
					]
				],
			]
		],
		[
			'menu_name' => '管理员管理',
			'menu_tag' => 'Admin',
			'sub_menu' => [
				[
					'menu_name' => '角色管理',
					'menu_tag' => 'Admin_Role_list',
					'uri' => [
						'RoleController@index' => '查看角色列表',
						'RoleController@create' => '添加角色',
						'RoleController@store' => '添加角色提交',
						'RoleController@show' => '编辑角色',
						'RoleController@editsave' => '编辑角色提交',
						'RoleController@del' => '删除角色',
					]
				],
				[
					'menu_name' => '管理员列表',
					'menu_tag' => 'Admin_list',
					'uri' => [
						'AdministratorsController@index' => '管理员列表',
						'AdministratorsController@add' => '添加管理员',
						'AdministratorsController@addsave' => '添加管理员提交',
						'AdministratorsController@stop' => '停用管理员',
						'AdministratorsController@start' => '启动管理员',
						'AdministratorsController@show' => '编辑管理员',
						'AdministratorsController@save' => '编辑管理员提交',
						'AdministratorsController@del' => '删除管理员',
					]
				],
			]
		],
	
	];
