<?php
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/3/3
	 * Time: 10:49
	 */
	
	return [
		'edit'                                                              => '编辑',
		'export'                                                            => '导出Excel',
		
		/*代理商列表*/
		'proxy_user_id'                                                     => '账户ID',
		'proxy_user_name'                                                   => '账户姓名',
		'proxy_user_sex'                                                    => '性别',
		'proxy_user_email'                                                  => '邮箱',
		'proxy_user_phone'                                                  => '手机号',
		'proxy_user_groupId'                                                => '级别',
		'proxy_direct_count'                                                => '直属代理',
		'proxy_cust_count'                                                  => '直属客户',
		'proxy_user_money'                                                  => '余额',
		'proxy_cust_eqy'                                                    => '净值',
		'proxy_fy_money'                                                    => '返佣',
		'proxy_rj_money'                                                    => '入金',
		'proxy_qk_money'                                                    => '出金',
		'proxy_rec_crt_date'                                                => '开户日期',
		'proxy_comm_trans'                                                  => '佣金转户',
		'proxy_agents_lvg'                                                  => '代理级别',
		'proxy_agents_settlementmodel'                                      => '结算方式',
		'proxy_agents_confirm'                                              => '级别确认',
		'proxy_agents_parentId'                                             => '上级代理',
		'proxy_agents_commp_rights'                                         => '返佣 / 权益',
		'proxy_agents_commp'                                                => '返佣比例',
		'proxy_agents_rights'                                               => '权益比例',
		'proxy_user_options'                                                => '操作',
		
		/*直属代理商客户列表*/
		'proxy_direct_user_group'                                           => '账户组别',
		'proxy_direct_user_id'                                              => '账户ID',
		'proxy_direct_user_name'                                            => '账户名',
		'proxy_direct_user_money'                                           => '余额',
		'proxy_direct_user_eqy'                                             => '净值',
		'proxy_direct_user_rj_money'                                        => '入金',
		'proxy_direct_user_qk_money'                                        => '出金',
		'proxy_direct_user_net_money'                                       => '净入金',
		'proxy_direct_user_poundage_moneny'                                 => '手续费',
		'proxy_direct_user_profit_loss'                                     => '盈亏',
		'proxy_direct_user_noble_metal'                                     => '贵金属',
		'proxy_direct_user_foreign_exchange'                                => '外汇',
		'proxy_direct_user_energy'                                          => '能源',
		'proxy_direct_user_index'                                           => '指数',
		'proxy_direct_user_total_volume'                                    => '总交易量',
		'proxy_direct_user_swap'                                            => '利息',
		'proxy_direct_user_crtdate'                                         => '开户时间',
		
		/*仓位总结*/
		'position_summary_user_id'                                          => '账户ID',
		'position_summary_user_name'                                        => '账户名',
		'position_summary_agents_group_id'                                  => '代理级别',
		'position_summary_parent_id'                                        => '上级ID',
		'position_summary_deposit_moneny'                                   => '入金',
		'position_summary_withdrawal_moneny'                                => '出金',
		'position_summary_comm_moneny'                                      => '佣金',
		'position_summary_net_deposit_moneny'                               => '净入金',
		'position_summary_profit_loss'                                      => '盈亏',
		'position_summary_poundage_moneny'                                  => '手续费',
		'position_summary_product_type'                                     => '产品分类',
		'position_summary_noble_metal'                                      => '贵金属',
		'position_summary_foreign_exchange'                                 => '外汇',
		'position_summary_energy'                                           => '能源',
		'position_summary_index'                                            => '指数',
		'position_summary_total_volume'                                     => '总交易量',
		'position_summary_swap'                                             => '利息',
		
		/*已平仓单*/
		'close_order_ticket_id'                                             => '订单ID',
		'close_order_user_id'                                               => '账户ID',
		'close_order_symbol_type'                                           => '产品',
		'close_order_cmd_type'                                              => '产品类型',
		'close_order_volume'                                                => '交易量',
		'close_order_sl'                                                    => '止损',
		'close_order_tp'                                                    => '止盈',
		'close_order_commission_money'                                      => '手续费',
		'close_order_profit_money'                                          => '盈亏',
		'close_order_swaps_money'                                           => '利息',
		'close_order_open_price'                                            => '开仓价格',
		'close_order_price'                                                 => '平仓价格',
		'close_order_time'                                                  => '平仓时间',
		
		/*未平仓单*/
		'open_order_ticket_id'                                             => '订单ID',
		'open_order_user_id'                                               => '账户ID',
		'open_order_symbol_type'                                           => '产品',
		'open_order_cmd_type'                                              => '产品类型',
		'open_order_volume'                                                => '交易量',
		'open_order_sl'                                                    => '止损',
		'open_order_tp'                                                    => '止盈',
		'open_order_commission_money'                                      => '手续费',
		'open_order_profit_money'                                          => '盈亏',
		'open_order_swaps_money'                                           => '利息',
		'open_order_feng_xian_val'                                         => '持仓风险率',
		'open_order_open_price'                                            => '开仓价格',
		'open_order_time'                                                  => '开仓时间',
		
		/*实时返佣*/
		'real_rebate_ticket_id'                                            => '订单ID',
		'real_rebate_user_id'                                              => '返佣账户ID',
		'real_rebate_order_money'                                          => '返佣金额',
		'real_rebate_order_source'                                         => '返佣来源',
		'real_rebate_order_time'                                           => '返佣时间',
		
		/*客户管理(直属)*/
		'direct_customer_user_id'                                          => '账户ID',
		'direct_customer_user_name'                                        => '账户名',
		'direct_customer_user_money'                                       => '余额',
		'direct_customer_user_eqy_money'                                   => '净值',
		'direct_customer_user_rj_money'                                    => '入金',
		'direct_customer_user_qk_money'                                    => '出金',
		'direct_customer_user_net_moneny'                                  => '净入金',
		'direct_customer_user_poundage_moneny'                             => '手续费',
		'direct_customer_user_profit_loss'                                 => '盈亏',
		'direct_customer_feng_xian_val'                                    => '风险率',
		'direct_customer_user_noble_metal'                                 => '贵金属',
		'direct_customer_user_foreign_exchange'                            => '外汇',
		'direct_customer_user_energy'                                      => '能源',
		'direct_customer_user_index'                                       => '指数',
		'direct_customer_user_total_volume'                                => '总交易量',
		'direct_customer_user_swap'                                        => '利息',
		'direct_customer_user_rec_crt_date'                                => '开户时间',
		'direct_customer_comm_trans'                                       => '佣金转户',
		
		/*客户管理(直属)--> 客户变更列表*/
		'direct_customer_change_uid'                                       => '变更账户',
		'direct_customer_change_type'                                      => '变更为',
		'direct_customer_change_status'                                    => '申请状态',
		'direct_customer_change_reason'                                    => '失败原因',
		'direct_customer_change_datetime'                                  => '申请时间',
		
		//客户变更申请--admin
		'account_change_id'                                                => '变更账户',
		'account_change_type'                                              => '变更为',
		'account_change_bal'                                               => '账户余额',
		'account_change_vol'                                               => '持仓总量',
		'account_change_apply_id'                                          => '申请人账号',
		'account_change_name'                                              => '申请人名字',
		'account_change_status'                                            => '申请状态',
		'account_change_reason'                                            => '失败原因',
		'account_change_datetime'                                          => '申请日期',
		'account_change_action'                                            => '操作',
		
		/*账户流水--> 入金*/
		'account_deposit_order_no'	                                       => '订单号',
		'account_deposit_no'		                                       => '交易账号',
		'account_deposit_type'                                             => '入金类别',
		'account_deposit_comment'                                          => '入金备注',
		'account_deposit_moneny'                                           => '入金金额 / USD',
		'account_deposit_depamount'                                        => '实际支付 / RMB',
		'account_deposit_source'                                           => '入金来源',
		'account_deposit_flownumber'                                       => '充值流水号',
		'account_deposit_status'                                           => '入金状态',
		'account_deposit_datetme'                                          => '入金时间',
		
		/*账户流水--> 出金*/
		'account_withdraw_order_no'	                                       => '订单号',
		'account_withdraw_no'		                                       => '交易账号',
		'account_withdraw_type'                                            => '出金类别',
		'account_withdraw_comment'                                         => '出金备注',
		'account_withdraw_moneny'                                          => '出金金额 / USD',
		'account_withdraw_depamount'                                       => '实际支付 / RMB',
		'account_withdraw_source'                                          => '出金来源',
		'account_withdraw_flownumber'                                      => '充值流水号',
		'account_withdraw_status'                                          => '出金状态',
		'account_withdraw_datetme'                                         => '出金时间',
		
		//账户出金
		'account_withdrawal_order_no'                                      => '订单号',
		'account_withdrawal_no'                                            => '交易账号',
		'account_withdrawal_type'                                          => '出金类别',
		'account_withdrawal_moneny'                                        => '出金金额 / USD',
		'account_withdrawal_source'                                        => '出金去向',
		'account_withdrawal_status'                                        => '出金状态',
		'account_withdrawal_fail_reason'                                   => '失败原因',
		'account_withdrawal_datetme'                                       => '出金时间',
		
		//账户出金申请记录
		'account_apply_order_no'                                           => '订单号',
		'account_apply_userId'                                             => '账户ID',
		'account_apply_userName'                                           => '账户姓名',
		'account_apply_amount'                                             => '申请金额 / USD',
		'account_apply_actapplyamount' 		                               => '实际金额 / USD',
		'account_apply_actdraw' 		                                   => '实际出金 / RMB',
		'account_apply_drawrate'                                           => '申请汇率',
		'account_apply_drawbankno'                                         => '银行卡号',
		'account_apply_drawbankclass'                                      => '银行名称',
		'account_apply_status'                                             => '出金状态',
		'account_apply_act_pdg_rmb'                                        => '手续费',
		'account_apply_drawpoundage'                                       => '手续费 / USD',
		'account_apply_options'                                            => '订单操作',
		'account_apply_remark'                                             => '备注',
		'account_rec_crt_date'                                             => '申请时间',
		
		//爆仓列表
		'whs_exp_acc_no'                                                   => '爆仓账户',
		'whs_exp_acc_name'                                                 => '爆仓姓名',
		'whs_exp_bal'                                                      => '账户余额',
		'whs_exp_crt'                                                      => '账户信用',
		'whs_exp_status'                                                   => '清零状态',
		'whs_exp_is_zero_date'                                             => '清零日期',
		
		//销户申请
		'cancel_apply_id'         	                                       => '申请账户',
		'cancel_apply_name'       	                                       => '账户姓名',
		'cancel_apply_bal'        	                                       => '账户余额',
		'cancel_apply_vol'        	                                       => '持仓总量',
		'cancel_apply_status'                                              => '申请状态',
		'cancel_apply_remark'     	                                       => '拒绝原因',
		'cancel_apply_datetime'   	                                       => '申请日期',
		'cancel_apply_action'     	                                       => '操作',
		
		//权益结算
		'rights_sum_userId'     	                                       => '账户ID',
		'rights_sum_profit'     	                                       => '盈亏',
		'rights_sum_volume'     	                                       => '交易量',
		'rights_sum_date'     	                                           => '统计日期',
		'rights_sum_userVal2'     	                                       => '权益/返佣比例',
		'rights_sum_userVal'     	                                       => '上/下级权益',
		'rights_sum_userValDiff'     	                                   => '权益点差',
		'rights_sum_returnamt'     	                                       => '已返佣金',
		'rights_sum_shouxufei'                                             => '手续费',
		'rights_sum_money'     	                                           => '应返金额',
		'rights_sum_swaps'                                                 => '利息',
		'rights_sum_yajin'     	                                           => '押金金额',
		'rights_sum_realamt'     	                                       => '实返金额',
		'rights_sum_status'     	                                       => '结算状态',
		'rights_manual_reason'     	                                       => '结算原因',
		'rights_sum_mt4OrderId'     	                                   => 'MT4订单号',
		'rights_sum_remarks'     	                                       => '结算备注',
		'rights_sum_userCycle'     	                                       => '权益周期',
		'rights_sum_reccrtdate'     	                                   => '结算时间',
		//'rights_sum_remarks'                                               => '结算来源',
		'rights_sum_action'     	                                       => '权益操作',
		
		//新闻列表
		'news_id'         	                                               => '新闻ID',
		'news_user'       	                                               => '新闻作者',
		'news_title'        	                                           => '新闻标题',
		'news_content'        	                                           => '新闻内容',
		'news_is_push'        	                                           => '是否推送',
		'news_edit'        	                                               => '操作',
		'news_rec_crt_date'     	                                       => '创建时间',
		'news_rec_upd_date'     	                                       => '更新时间',
		
		//历史记录
		'history_loginId'         	                                       => '登录ID',
		'history_loginIddesc'         	                                   => '登录地点',
		'history_loginIdIp'         	                                   => '登录IP',
		'history_date'         	                                           => '登录时间',
	];