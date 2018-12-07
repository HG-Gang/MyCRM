<?php
	
	/**
	 * label    : 菜单名称
	 * url      : 菜单路由，如果有子菜单的时候路由为#
	 * icon     ：菜单图标
	 * menu_tag : 菜单表标识，与config/admin_uri.php 中的权限管理中的 menu_tag对应
	 */
	return [
		['label' => '后台首页','url'=>route_prefix() . '/index', 'icon' => '&#xe616;', 'menu_tag' => 'Admin', 'submenu' => []],
		
		[
			'label' => '代理商管理', 'url' => '#', 'icon' => '&#xe60d;', 'menu_tag' => 'Agent', 'submenu' => [
			/*['label' => '代理列表', 'url' => '/admin/agent', 'menu_tag' => 'Agent_list'],*/
			['label' => '代理列表', 'url' => route_prefix() . '/agents_list', 'menu_tag' => 'Agent_list'],
			['label' => '添加代理', 'url' => route_prefix() . '/agents_add', 'menu_tag' => 'Agent_add'],
		]
		],
		[
			'label' => '订单管理', 'url' => '#', 'icon' => '&#xe627;', 'menu_tag' => 'Order', 'submenu' => [
			['label' => '仓位总结', 'url' => route_prefix() . '/order/position_summary_list', 'menu_tag' => 'order_position_summary_list'],
			['label' => '已平仓单', 'url' => route_prefix() . '/order/close_list', 'menu_tag' => 'order_close_list'],
			['label' => '未平仓单', 'url' => route_prefix() . '/order/open_list', 'menu_tag' => 'order_open_list'],
			['label' => '实时返佣', 'url' => route_prefix() . '/order/real_commission_list', 'menu_tag' => 'oredr_real_commission_list'],
			['label' => '爆仓列表', 'url' => route_prefix() . '/order/whs_exp_zero_list', 'menu_tag' => 'order_whs_exp_zero_list'],
		]
		],
		[
			'label' => '客户管理', 'url' => '#', 'icon' => '&#xe62c;', 'menu_tag' => 'User', 'submenu' => [
			['label' => '客户列表', 'url' => route_prefix() . '/cust/list', 'menu_tag' => 'user_list'],
			['label' => '添加客户', 'url' => route_prefix() . '/cust/add', 'menu_tag' => 'user_add'],
			/*['label' => '客户变更', 'url' => route_prefix() . '/cust/change_list', 'menu_tag' => 'change_list'],*/
		]
		],
		[
			'label' => '财务管理', 'url' => '#', 'icon' => '&#xe63a;', 'menu_tag' => 'Amount', 'submenu' => [
			['label' => '出金申请', 'url' => route_prefix() . '/amount/withdraw_apply', 'menu_tag' => 'withdraw_apply'],
			['label' => '入金流水', 'url' => route_prefix() . '/amount/deposit_flow', 'menu_tag' => 'deposit_flow'],
			['label' => '出金流水', 'url' => route_prefix() . '/amount/withdraw_flow', 'menu_tag' => 'withdraw_flow'],
			['label' => '权益统计', 'url' => route_prefix() . '/amount/rights_summary', 'menu_tag' => 'rights_summary'],
			['label' => '外汇牌价', 'url' => route_prefix() . '/amount/whpj_rate', 'menu_tag' => 'whpj_rate'],
		]
		],
		[
			'label' => '批量管理', 'url' => '#', 'icon' => '&#xe68b;', 'menu_tag' => 'Batch', 'submenu' => [
			['label' => '批量入金', 'url' => route_prefix() . '/amount/batch_operation', 'menu_tag' => 'batch_operation'],
			['label' => '批量出金', 'url' => route_prefix() . '/amount/batch_operation_withdraw', 'menu_tag' => 'batch_operation_withdraw']
		]
		],
			[
				'label' => '风险管理', 'url' => '#', 'icon' => '&#xe61a;', 'menu_tag' => 'FengXian', 'submenu' => [
					['label' => '盈利风险', 'url' => route_prefix() . '/fengXian/profit_list', 'menu_tag' => 'fengXian_profit'],
					['label' => '仓位风险', 'url' => route_prefix() . '/fengXian/position_list', 'menu_tag' => 'fengXian_position'],
					['label' => 'IP风险', 'url' => route_prefix() . '/fengXian/Ipaddress_list', 'menu_tag' => 'fengXian_Ipaddress']
				]
			],
		[
			'label' => '资料审核', 'url' => '#', 'icon' => '&#xe6f5;', 'menu_tag' => 'Auth', 'submenu' => [
			/*['label' => '待上传', 'url' => route_prefix() . '/auth/user_pending', 'menu_tag' => 'user_pending'],*/
			['label' => '待审核', 'url' => route_prefix() . '/auth/user_examine', 'menu_tag' => 'user_examine'],
			['label' => '已审核', 'url' => route_prefix() . '/auth/user_certified', 'menu_tag' => 'user_certified'],
			/*['label' => '退回', 'url' => route_prefix() . '/auth/user_notpass', 'menu_tag' => 'user_notpass'],*/
		]
		],
		[
			'label' => '销户管理','url'=>'#', 'icon' => '&#xe62b;', 'menu_tag' => 'Cancel', 'submenu' => [
			['label' => '销户列表', 'url' => route_prefix() . '/cancel/user_list', 'menu_tag' => 'cancel_user_list'],
		]
		],
		[
			'label' => '资讯管理','url'=>'#', 'icon' => '&#xe616;', 'menu_tag' => 'News', 'submenu' => [
			['label' => '新闻列表', 'url' => route_prefix() . '/news/news_list_browse', 'menu_tag' => 'news_list'],
			['label' => '添加新闻', 'url' => route_prefix() . '/news/news_add_browse', 'menu_tag' => 'news_add'],
		]
		],
		[
			'label' => '管理员管理', 'url' => '#', 'icon' => '&#xe62d;', 'menu_tag' => 'Admin', 'submenu' => [
			['label' => '角色管理', 'url' => route_prefix() . '/role', 'menu_tag' => 'Admin_Role_list'],
			['label' => '管理员列表', 'url' => route_prefix() . '/Administrators', 'menu_tag' => 'Admin_list'],
		]
		],
	];
