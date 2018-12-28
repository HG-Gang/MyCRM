<?php
	/**
	 * Created by PhpStorm.
	 * User: Hank
	 * Date: 2018/3/27
	 * Time: 15:18
	 */
	
	namespace App\Http\Controllers\User;
	
	use Illuminate\Http\Request;
	use App\Model\DrawRecordLog;
	use App\Model\Mt4Trades;
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	
	class CustomerFlowController extends Abstract_Mt4service_Controller
	{
		public function main_browse()
		{
			return view ('user.customer_flow.main_browse')->with (['_user_info' => $this->_user, '_role_type' => ($this->_user['user_id'] >= $this->_userIdIndex) ? 'User' : 'Agents']);
		}
		
		//入金流水
		public function depositFlowSearch (Request $request)
		{
			$depositresult = array('rows' => '', 'total' => '');
			
			$_rs = $this->get_account_self_deposit_list ('page', $request);
			
			if (!empty($_rs)) {
				$depositresult['rows'] = $_rs;
				$depositresult['total'] = $this->get_account_self_deposit_list ('count', $request);
				$_datasum = $this->get_account_self_deposit_list_sum_data ($request);
				$depositresult['footer'] = [[
					'order_no'          => '总计',
					'userId'            => '',
					'depositType'       => '',
					'depositComment'    => '',
					'depositActProfit'  => $_datasum[0]['depositActProfit'],
					'depositStatus'     => '',
					'depositDate'       => '',
				]];
			}
			
			return json_encode ($depositresult);
		}
		
		//出金流水
		public function withdrawalFlowSearch (Request $request)
		{
			$result = array('rows' => '', 'total' => '');
			
			$_rs = $this->get_account_self_withdrawl_list ('page', $request);
			
			if (!empty($_rs)) {
				$result['rows'] = $_rs;
				$result['total'] = $this->get_account_self_withdrawl_list ('count', $request);
				$_datasum = $this->get_account_self_withdrawl_list_sum_data ($request);
				$result['footer'] = [[
					'order_no'              => '总计',
					'userId'                => '',
					'withdrawalType'        => '',
					'withdrawalActProfit'   => $_datasum[0]['withdrawalActProfit'],
					'withdrawalDate'        => '',
				]];
			}
			
			return json_encode ($result);
		}
		
		//出金申请
		public function withdrawApplyFlowSearch (Request $request)
		{
			$result = array('rows' => '', 'total' => '');
			
			$_rs = $this->get_account_self_withdrawl_apply_list ('page', $request);
			
			if (!empty($_rs)) {
				$result['rows'] = $_rs;
				$result['total'] = $this->get_account_self_withdrawl_apply_list ('count', $request);
				$_datasum = $this->get_account_self_withdrawl_apply_list_sum_data ($request);
				$result['footer'] = [[
					'order_no'              => '总计',
					'userId'                => '',
					'userName'              => '',
					'applyamount'           => $_datasum[0]['applyamount'],
					'actapplyamount'        => $_datasum[0]['actapplyamount'],
					'drawrate'              => '',
					'drawbankno'            => '',
					'drawbankclass'         => '',
					'applystatus'           => '',
					'rec_crt_date'          => '',
				]];
			}
			
			return json_encode ($result);
		}
		
		//直属入金流水
		public function directDepositFlowSearch (Request $request)
		{
			$result = array('rows' => '', 'total' => '');
			
			$_rs = $this->get_direct_account_deposit_list ('page', $request);
			
			if (!empty($_rs)) {
				$result['rows'] = $_rs;
				$result['total'] = $this->get_direct_account_deposit_list ('count', $request);
				$_datasum = $this->get_direct_account_deposit_list_sum_data ($request);
				$result['footer'] = [[
					'order_no'          => '总计[不含信用]',
					'userId'            => '',
					'directType'        => '',
					'directProfit'      => $_datasum[0]['directProfit'],
					'directComment'     => '',
					'directModifyTime'  => '',
				]];
			}
			
			return json_encode ($result);
		}
		
		//直属出金流水
		public function directWithdrawalFlowSearch (Request $request)
		{
			$result = array('rows' => '', 'total' => '');
			
			$_rs = $this->get_direct_account_withdrawal_list ('page', $request);
			
			if (!empty($_rs)) {
				$result['rows'] = $_rs;
				$result['total'] = $this->get_direct_account_withdrawal_list ('count', $request);
				$_datasum = $this->get_direct_account_withdrawal_list_sum_data ($request);
				$result['footer'] = [[
					'order_no'              => '总计[不含信用]',
					'userId'                => '',
					'directdrawalComment'   => '',
					'directdrawalActProfit' => $_datasum[0]['directdrawalActProfit'],
					'directdrawalModifyTime'=> '',
				]];
			}
			
			return json_encode ($result);
		}
		
		//入金流水导出
		public function depositExport(Request $request)
		{
			$cellData = $this->get_direct_account_deposit_list ('sum', $request);
			
			if (!empty($cellData)) {
				return response()->json(['msg' => $this->_exte_export_excel('直属入金流水', $request->role, $cellData)]);
			}
			
			return response()->json(['msg' => 'FAIL']);
		}
		
		public function DownloadFile($file, $role)
		{
			$file = $this->_exte_export_excel_basic_path($role) . $file . '.' . $this->_exte_export_excel_format();
			
			return response()->download($file);
		}
		
		//入金流水search
		protected function get_account_self_deposit_list($search, $request)
		{
			$deposit_id             = $request->deposit_id;
			$deposit_source         = $request->deposit_source;
			$deposit_startdate      = $request->deposit_startdate;
			$deposit_enddate        = $request->deposit_enddate;
			
			$query_sql = Mt4Trades::selectRaw("
				mt4_trades.TICKET as order_no,
				mt4_trades.LOGIN as userId,
				mt4_trades.COMMENT as depositType,
				mt4_trades.COMMENT as depositComment,
				mt4_trades.PROFIT as depositActProfit,
				mt4_trades.MODIFY_TIME as modify_time
			")->whereIn('mt4_trades.CMD', array(6, 7))->where('mt4_trades.OPEN_PRICE', 0)->where('mt4_trades.PROFIT', '>', 0)
				->where('mt4_trades.COMMENT', 'NOT LIKE', '%-FY')
				->where('mt4_trades.LOGIN', $this->_user['user_id'])
				->where(function ($subWhere) use ($deposit_id, $deposit_startdate, $deposit_enddate) {
					if (!empty($deposit_startdate) && !empty($deposit_enddate) && $this->_exte_is_Date ($deposit_startdate) && $this->_exte_is_Date ($deposit_enddate)) {
						$subWhere->whereBetween('mt4_trades.MODIFY_TIME', [$deposit_startdate .' 00:00:00', $deposit_enddate . ' 23:59:59']);
					} else {
						if(!empty($deposit_startdate) && $this->_exte_is_Date ($deposit_startdate)) {
							$subWhere->where('mt4_trades.MODIFY_TIME',  '>= ', $deposit_startdate .' 23:59:59');
						}
						if(!empty($deposit_enddate) && $this->_exte_is_Date ($deposit_enddate)) {
							$subWhere->where('mt4_trades.MODIFY_TIME', '<', $deposit_enddate .' 00:00:00');
						}
					}
					
					if (!empty($deposit_id)) {
						$subWhere->where('mt4_trades.TICKET', 'like', '%' . $deposit_id . '%');
					}
				});
			
			if ($search == 'page') {
				$id_list = $query_sql->skip($this->_offset)->take($this->_pageSize)->orderBy('mt4_trades.MODIFY_TIME', 'desc')->get()->toArray();
			} else if ($search == 'count') {
				$id_list = $query_sql->count();
			} else if ($search == 'sum') {
				$id_list = $query_sql->get()->toArray();
			}
			
			return $id_list;
		}
		
		//入金流水条件汇总
		protected function get_account_self_deposit_list_sum_data($request)
		{
			$deposit_id             = $request->deposit_id;
			$deposit_source         = $request->deposit_source;
			$deposit_startdate      = $request->deposit_startdate;
			$deposit_enddate        = $request->deposit_enddate;
			
			$_datasum = Mt4Trades::selectRaw("
				sum( mt4_trades.PROFIT ) as depositActProfit
			")->whereIn('mt4_trades.CMD', array(6, 7))->where('mt4_trades.OPEN_PRICE', 0)->where('mt4_trades.PROFIT', '>', 0)
				->where('mt4_trades.COMMENT', 'NOT LIKE', '%-FY')
				->where('mt4_trades.LOGIN', $this->_user['user_id'])
				->where(function ($subWhere) use ($deposit_id, $deposit_startdate, $deposit_enddate) {
					if (!empty($deposit_startdate) && !empty($deposit_enddate) && $this->_exte_is_Date ($deposit_startdate) && $this->_exte_is_Date ($deposit_enddate)) {
						$subWhere->whereBetween('mt4_trades.MODIFY_TIME', [$deposit_startdate .' 00:00:00', $deposit_enddate . ' 23:59:59']);
					} else {
						if(!empty($deposit_startdate) && $this->_exte_is_Date ($deposit_startdate)) {
							$subWhere->where('mt4_trades.MODIFY_TIME',  '>= ', $deposit_startdate .' 23:59:59');
						}
						if(!empty($deposit_enddate) && $this->_exte_is_Date ($deposit_enddate)) {
							$subWhere->where('mt4_trades.MODIFY_TIME', '<', $deposit_enddate .' 00:00:00');
						}
					}
					
					if (!empty($deposit_id)) {
						$subWhere->where('mt4_trades.TICKET', 'like', '%' . $deposit_id . '%');
					}
				})->get()->toArray();
			
			return $_datasum;
		}
		
		//出金流水search
		protected function get_account_self_withdrawl_list($search, $request)
		{
			$withdraw_id            = $request->withdraw_id;
			$withdraw_source        = $request->withdraw_source;
			$withdraw_startdate     = $request->withdraw_startdate;
			$withdraw_enddate       = $request->withdraw_enddate;
			
			$query_sql = Mt4Trades::selectRaw("
				mt4_trades.TICKET as order_no,
				mt4_trades.LOGIN as userId,
				mt4_trades.COMMENT as withdrawalType,
				mt4_trades.PROFIT as withdrawalActProfit,
				mt4_trades.MODIFY_TIME as withdrawalDate
			")->whereIn('mt4_trades.CMD', array(6, 7))->where('mt4_trades.OPEN_PRICE', 0)->where('mt4_trades.PROFIT', '<', 0)
				->where('mt4_trades.COMMENT', 'NOT LIKE', '%-FY')
				->where('mt4_trades.LOGIN', $this->_user['user_id'])
				->where(function ($subWhere) use ($withdraw_id, $withdraw_startdate, $withdraw_enddate) {
					if (!empty($withdraw_startdate) && !empty($withdraw_enddate) && $this->_exte_is_Date ($withdraw_startdate) && $this->_exte_is_Date ($withdraw_enddate)) {
						$subWhere->whereBetween('mt4_trades.MODIFY_TIME', [$withdraw_startdate .' 00:00:00', $withdraw_enddate . ' 23:59:59']);
					} else {
						if(!empty($withdraw_startdate) && $this->_exte_is_Date ($withdraw_startdate)) {
							$subWhere->where('mt4_trades.MODIFY_TIME',  '>= ', $withdraw_startdate .' 23:59:59');
						}
						if(!empty($withdraw_enddate) && $this->_exte_is_Date ($withdraw_enddate)) {
							$subWhere->where('mt4_trades.MODIFY_TIME', '<', $withdraw_enddate .' 00:00:00');
						}
					}
					
					if (!empty($withdraw_id)) {
						$subWhere->where('mt4_trades.TICKET', 'like', '%' . $withdraw_id . '%');
					}
				});
			
			if ($search == 'page') {
				$id_list = $query_sql->skip($this->_offset)->take($this->_pageSize)->orderBy('mt4_trades.MODIFY_TIME', 'desc')->get()->toArray();
			} else if ($search == 'count') {
				$id_list = $query_sql->count();
			} else if ($search == 'sum') {
				$id_list = $query_sql->get()->toArray();
			}
			
			return $id_list;
		}
		
		protected function get_account_self_withdrawl_list_sum_data($request)
		{
			$withdraw_id            = $request->withdraw_id;
			$withdraw_source        = $request->withdraw_source;
			$withdraw_startdate     = $request->withdraw_startdate;
			$withdraw_enddate       = $request->withdraw_enddate;
			
			$_datasum = Mt4Trades::selectRaw("
				sum( mt4_trades.PROFIT ) as withdrawalActProfit
			")->whereIn('mt4_trades.CMD', array(6, 7))->where('mt4_trades.OPEN_PRICE', 0)->where('mt4_trades.PROFIT', '<', 0)
				->where('mt4_trades.COMMENT', 'NOT LIKE', '%-FY')
				->where('mt4_trades.LOGIN', $this->_user['user_id'])
				->where(function ($subWhere) use ($withdraw_id, $withdraw_startdate, $withdraw_enddate) {
					if (!empty($withdraw_startdate) && !empty($withdraw_enddate) && $this->_exte_is_Date ($withdraw_startdate) && $this->_exte_is_Date ($withdraw_enddate)) {
						$subWhere->whereBetween('mt4_trades.MODIFY_TIME', [$withdraw_startdate .' 00:00:00', $withdraw_enddate . ' 23:59:59']);
					} else {
						if(!empty($withdraw_startdate) && $this->_exte_is_Date ($withdraw_startdate)) {
							$subWhere->where('mt4_trades.MODIFY_TIME',  '>= ', $withdraw_startdate .' 23:59:59');
						}
						if(!empty($withdraw_enddate) && $this->_exte_is_Date ($withdraw_enddate)) {
							$subWhere->where('mt4_trades.MODIFY_TIME', '<', $withdraw_enddate .' 00:00:00');
						}
					}
					
					if (!empty($withdraw_id)) {
						$subWhere->where('mt4_trades.TICKET', 'like', '%' . $withdraw_id . '%');
					}
				})->get()->toArray();
			
			return $_datasum;
		}
		
		//出金申请
		protected function get_account_self_withdrawl_apply_list($search, $request)
		{
			$withdraw_apply_id             = $request->withdraw_apply_id;
			$withdraw_apply_status         = $request->withdraw_apply_status;
			$withdraw_apply_startdate      = $request->withdraw_apply_startdate;
			$withdraw_apply_enddate        = $request->withdraw_apply_enddate;
			
			$query_sql = DrawRecordLog::selectRaw("
				draw_record_log.record_id,
				draw_record_log.mt4_trades_no as order_no,
				draw_record_log.user_id as userId,
				draw_record_log.user_name as userName,
				draw_record_log.apply_amount as applyamount,
				draw_record_log.act_apply_amount as actapplyamount,
				draw_record_log.act_draw as actdraw,
				draw_record_log.draw_rate as drawrate,
				draw_record_log.draw_bank_no as drawbankno,
				draw_record_log.draw_bank_class as drawbankclass,
				draw_record_log.apply_status as applystatus,
				draw_record_log.apply_remark as applyremark,
				draw_record_log.draw_poundage as drawpoundage,
				draw_record_log.orderId_LOC as orderIdLOC,
				draw_record_log.orderId_OTC as orderIdOTC,
				draw_record_log.orderId_OTC_status as orderIdOTCstatus,
				draw_record_log.generate_order_date as generateorderdate,
				draw_record_log.voided,
				draw_record_log.rec_crt_date,
				draw_record_log.rec_upd_date,
				draw_record_log.rec_crt_user,
				draw_record_log.rec_upd_user
			")->where('draw_record_log.voided', '1')
			->where('draw_record_log.user_id', $this->_user['user_id'])
			->where(function ($subWhere) use ($withdraw_apply_id, $withdraw_apply_status, $withdraw_apply_startdate, $withdraw_apply_enddate) {
				if (!empty($withdraw_apply_startdate) && !empty($withdraw_apply_enddate) && $this->_exte_is_Date ($withdraw_apply_startdate) && $this->_exte_is_Date ($withdraw_apply_enddate)) {
					$subWhere->whereBetween('draw_record_log.rec_crt_date', [$withdraw_apply_startdate .' 00:00:00', $withdraw_apply_enddate . ' 23:59:59']);
				} else {
					if(!empty($withdraw_apply_startdate) && $this->_exte_is_Date ($withdraw_apply_startdate)) {
						$subWhere->where('draw_record_log.rec_crt_date',  '>=', $withdraw_apply_startdate .' 23:59:59');
					}
					if(!empty($withdraw_apply_enddate) && $this->_exte_is_Date ($withdraw_apply_enddate)) {
						$subWhere->where('draw_record_log.rec_crt_date', '<', $withdraw_apply_enddate .' 00:00:00');
					}
				}
				
				if (!empty($withdraw_apply_id)) {
					$subWhere->where('draw_record_log.mt4_trades_no', 'like', '%' . $withdraw_apply_id . '%');
				}
				
				if($withdraw_apply_status !== "") {
					$subWhere->where('draw_record_log.apply_status', $withdraw_apply_status);
				}
			});
			
			if ($search == 'page') {
				$id_list = $query_sql->skip($this->_offset)->take($this->_pageSize)->orderBy('draw_record_log.rec_crt_date', 'desc')->get()->toArray();
			} else if ($search == 'count') {
				$id_list = $query_sql->count();
			} else if ($search == 'sum') {
				$id_list = $query_sql->get()->toArray();
			}
			
			return $id_list;
		}
		
		protected function get_account_self_withdrawl_apply_list_sum_data($request)
		{
			$withdraw_apply_id             = $request->withdraw_apply_id;
			$withdraw_apply_status         = $request->withdraw_apply_status;
			$withdraw_apply_startdate      = $request->withdraw_apply_startdate;
			$withdraw_apply_enddate        = $request->withdraw_apply_enddate;
			
			$_datasum = DrawRecordLog::selectRaw("
				sum( draw_record_log.apply_amount ) as applyamount,
				sum( draw_record_log.act_apply_amount ) as actapplyamount
			")->where('draw_record_log.voided', '1')
				->where('draw_record_log.user_id', $this->_user['user_id'])
				->where(function ($subWhere) use ($withdraw_apply_id, $withdraw_apply_status, $withdraw_apply_startdate, $withdraw_apply_enddate) {
					if (!empty($withdraw_apply_startdate) && !empty($withdraw_apply_enddate) && $this->_exte_is_Date ($withdraw_apply_startdate) && $this->_exte_is_Date ($withdraw_apply_enddate)) {
						$subWhere->whereBetween('draw_record_log.rec_crt_date', [$withdraw_apply_startdate .' 00:00:00', $withdraw_apply_enddate . ' 23:59:59']);
					} else {
						if(!empty($withdraw_apply_startdate) && $this->_exte_is_Date ($withdraw_apply_startdate)) {
							$subWhere->where('draw_record_log.rec_crt_date',  '>=', $withdraw_apply_startdate .' 23:59:59');
						}
						if(!empty($withdraw_apply_enddate) && $this->_exte_is_Date ($withdraw_apply_enddate)) {
							$subWhere->where('draw_record_log.rec_crt_date', '<', $withdraw_apply_enddate .' 00:00:00');
						}
					}
					
					if (!empty($withdraw_apply_id)) {
						$subWhere->where('draw_record_log.mt4_trades_no', 'like', '%' . $withdraw_apply_id . '%');
					}
					
					if($withdraw_apply_status !== "") {
						$subWhere->where('draw_record_log.apply_status', $withdraw_apply_status);
					}
				})->get()->toArray();
			
			return $_datasum;
		}
		
		//获取直属客户入金记录 汇总不含XY信用
		protected function get_direct_account_deposit_list($search, $request)
		{
			$direct_deposit_id               = $request->direct_deposit_id;
			$direct_deposit_source           = $request->direct_deposit_source;
			$direct_deposit_startdate        = $request->direct_deposit_startdate;
			$direct_deposit_enddate          = $request->direct_deposit_enddate;
			
			if ($request->type == 'depositFlow') {
				$data                        = $request->data;
				$direct_deposit_id           = $data['direct_deposit_id'];
				$direct_deposit_source       = $data['direct_deposit_source'];
				$direct_deposit_startdate    = $data['direct_deposit_startdate'];
				$direct_deposit_enddate      = $data['direct_deposit_enddate'];
			}
			
			$query_sql = Mt4Trades::selectRaw("
				mt4_trades.TICKET as order_no,
				mt4_trades.LOGIN as userId,
				mt4_trades.PROFIT as directProfit,
				mt4_trades.COMMENT as directType,
				mt4_trades.COMMENT as directComment,
				mt4_trades.MODIFY_TIME as directModifyTime
			")->where('mt4_trades.OPEN_PRICE', 0)->where('mt4_trades.PROFIT', '>', 0)->where('mt4_trades.CMD', 6)
			->whereIn('mt4_trades.LOGIN', function ($whereIn) {
				$whereIn->select('mt4_users.LOGIN')->from('mt4_users')->where('mt4_users.ZIPCODE', $this->_user['user_id']);
			})->where(function ($query) {
				$query->orWhere(function ($subQuery) {
					$subQuery->orWhere('mt4_trades.COMMENT', 'like',  '%-ZH') //佣金转户
						->orWhere('mt4_trades.COMMENT', 'like', '%-TH') //佣金转户退回
						->orWhere('mt4_trades.COMMENT', 'like', '%-CZ') //充值
						//->orWhere('COMMENT', 'like', '%-FY') //返佣
						->orWhere('mt4_trades.COMMENT', 'like', '%-RJ') // 批量入金
						//->orWhere('mt4_trades.COMMENT', 'like', '%-XY') // 信用
						->orWhere('mt4_trades.COMMENT', 'like', '%-CJTH'); // 出金失败，退回
				})->orWhere(function ($orSubQuery) {
					$orSubQuery->orWhere('mt4_trades.COMMENT', 'like', '%Adj');
				});
			})->where(function ($subWhere) use ($direct_deposit_id, $direct_deposit_source, $direct_deposit_startdate, $direct_deposit_enddate) {
				if (!empty($direct_deposit_startdate) && !empty($direct_deposit_enddate) && $this->_exte_is_Date ($direct_deposit_startdate) && $this->_exte_is_Date ($direct_deposit_enddate)) {
					$subWhere->whereBetween('mt4_trades.MODIFY_TIME', [$direct_deposit_startdate .' 00:00:00', $direct_deposit_enddate . ' 23:59:59']);
				} else {
					if(!empty($direct_deposit_startdate) && $this->_exte_is_Date ($direct_deposit_startdate)) {
						$subWhere->where('mt4_trades.MODIFY_TIME',  '>=', $direct_deposit_startdate .' 23:59:59');
					}
					if(!empty($direct_deposit_enddate) && $this->_exte_is_Date ($direct_deposit_enddate)) {
						$subWhere->where('mt4_trades.MODIFY_TIME', '<', $direct_deposit_enddate .' 00:00:00');
					}
				}
				
				if (!empty($direct_deposit_id)) {
					$subWhere->where('mt4_trades.TICKET', 'like', '%' . $direct_deposit_id . '%');
				}
				
				if(!empty($direct_deposit_source)) {
					$subWhere->where('mt4_trades.COMMENT', 'like', '%' . $direct_deposit_source . '%');
				}
			});
			
			if ($search == 'page') {
				$id_list = $query_sql->skip($this->_offset)->take($this->_pageSize)->orderBy('mt4_trades.MODIFY_TIME', 'desc')->get()->toArray();
			} else if ($search == 'count') {
				$id_list = $query_sql->count();
			} else if ($search == 'sum') {
				$id_list = $query_sql->orderBy('mt4_trades.MODIFY_TIME', 'desc')->get()->toArray();
			}
			
			return $id_list;
		}
		
		//汇总不含XY信用
		protected function get_direct_account_deposit_list_sum_data ($request)
		{
			$direct_deposit_id               = $request->direct_deposit_id;
			$direct_deposit_source           = $request->direct_deposit_source;
			$direct_deposit_startdate        = $request->direct_deposit_startdate;
			$direct_deposit_enddate          = $request->direct_deposit_enddate;
			
			$_datasum = Mt4Trades::selectRaw("
				sum(case when mt4_trades.COMMENT not like '%-XY' and mt4_trades.PROFIT > 0 then mt4_trades.PROFIT else 0 end) as directProfit
			")->where('mt4_trades.OPEN_PRICE', 0)->where('mt4_trades.PROFIT', '>', 0)->where('mt4_trades.CMD', 6)
				->whereIn('mt4_trades.LOGIN', function ($whereIn) {
					$whereIn->select('mt4_users.LOGIN')->from('mt4_users')->where('mt4_users.ZIPCODE', $this->_user['user_id']);
				})->where(function ($query) {
					$query->orWhere(function ($subQuery) {
						$subQuery->orWhere('COMMENT', 'like',  '%-ZH') //佣金转户
						->orWhere('mt4_trades.COMMENT', 'like', '%-TH') //佣金转户退回
						->orWhere('mt4_trades.COMMENT', 'like', '%-CZ') //充值
						//->orWhere('COMMENT', 'like', '%-FY') //返佣
						->orWhere('mt4_trades.COMMENT', 'like', '%-RJ') // 批量入金
						//->orWhere('mt4_trades.COMMENT', 'like', '%-XY') // 信用
						->orWhere('mt4_trades.COMMENT', 'like', '%-CJTH'); // 出金失败，退回
					})->orWhere(function ($orSubQuery) {
						$orSubQuery->orWhere('mt4_trades.COMMENT', 'like', '%Adj');
					});
				})->where(function ($subWhere) use ($direct_deposit_id, $direct_deposit_source, $direct_deposit_startdate, $direct_deposit_enddate) {
					if (!empty($direct_deposit_startdate) && !empty($direct_deposit_enddate) && $this->_exte_is_Date ($direct_deposit_startdate) && $this->_exte_is_Date ($direct_deposit_enddate)) {
						$subWhere->whereBetween('mt4_trades.MODIFY_TIME', [$direct_deposit_startdate .' 00:00:00', $direct_deposit_enddate . ' 23:59:59']);
					} else {
						if(!empty($direct_deposit_startdate) && $this->_exte_is_Date ($direct_deposit_startdate)) {
							$subWhere->where('mt4_trades.MODIFY_TIME',  '>=', $direct_deposit_startdate .' 23:59:59');
						}
						if(!empty($direct_deposit_enddate) && $this->_exte_is_Date ($direct_deposit_enddate)) {
							$subWhere->where('mt4_trades.MODIFY_TIME', '<', $direct_deposit_enddate .' 00:00:00');
						}
					}
					
					if (!empty($direct_deposit_id)) {
						$subWhere->where('mt4_trades.LOGIN', 'like', '%' . $direct_deposit_id . '%');
					}
					
					if(!empty($direct_deposit_source)) {
						$subWhere->where('mt4_trades.COMMENT', 'like', '%' . $direct_deposit_source . '%');
					}
				})->get()->toArray();
			
			return $_datasum;
		}
		
		//获取直属客户出金记录 汇总不含XY信用
		protected function get_direct_account_withdrawal_list($search, $request)
		{
			$direct_withdraw_id              = $request->direct_withdraw_id;
			$direct_withdraw_startdate       = $request->direct_withdraw_startdate;
			$direct_withdraw_enddate         = $request->direct_withdraw_enddate;
			
			$query_sql = Mt4Trades::selectRaw("
				mt4_trades.TICKET as order_no,
				mt4_trades.LOGIN as userId,
				mt4_trades.PROFIT as directdrawalActProfit,
				mt4_trades.COMMENT as directdrawalComment,
				mt4_trades.MODIFY_TIME as directdrawalModifyTime
			")->where('mt4_trades.OPEN_PRICE', 0)->where('mt4_trades.PROFIT', '<', 0)->where('mt4_trades.CMD', 6)
			->whereIn('mt4_trades.LOGIN', function ($whereIn) {
				$whereIn->select('mt4_users.LOGIN')->from('mt4_users')->where('mt4_users.ZIPCODE', $this->_user['user_id']);
			})->where(function ($query) {
				$query->orWhere(function ($subQuery) {
					$subQuery->orWhere('mt4_trades.COMMENT', 'like',  '%-ZH') //佣金转户出金
					->orWhere('mt4_trades.COMMENT', 'like', '%-QK')
						->orWhere('mt4_trades.COMMENT', 'like', '%-RJTH'); //CZ入金退回
				})->orWhere(function ($orSubQuery) {
					$orSubQuery->orWhere('mt4_trades.COMMENT', 'like', 'Adj%');
				});
			})->where(function ($subWhere) use ($direct_withdraw_id, $direct_withdraw_startdate, $direct_withdraw_enddate) {
				if (!empty($direct_withdraw_startdate) && !empty($direct_withdraw_enddate) && $this->_exte_is_Date ($direct_withdraw_startdate) && $this->_exte_is_Date ($direct_withdraw_enddate)) {
					$subWhere->whereBetween('mt4_trades.MODIFY_TIME', [$direct_withdraw_startdate .' 00:00:00', $direct_withdraw_enddate . ' 23:59:59']);
				} else {
					if(!empty($direct_withdraw_startdate) && $this->_exte_is_Date ($direct_withdraw_startdate)) {
						$subWhere->where('mt4_trades.MODIFY_TIME',  '>=', $direct_withdraw_startdate .' 23:59:59');
					}
					if(!empty($direct_withdraw_enddate) && $this->_exte_is_Date ($direct_withdraw_enddate)) {
						$subWhere->where('mt4_trades.MODIFY_TIME', '<', $direct_withdraw_enddate .' 00:00:00');
					}
				}
				
				if (!empty($direct_withdraw_id)) {
					$subWhere->where('mt4_trades.TICKET', 'like', '%' . $direct_withdraw_id . '%');
				}
			});
			
			if ($search == 'page') {
				$id_list = $query_sql->skip($this->_offset)->take($this->_pageSize)->orderBy('mt4_trades.MODIFY_TIME', 'desc')->get()->toArray();
			} else if ($search == 'count') {
				$id_list = $query_sql->count();
			} else if ($search == 'sum') {
				$id_list = $query_sql->get()->toArray();
			}
			
			return $id_list;
		}
		
		protected function get_direct_account_withdrawal_list_sum_data($request)
		{
			$direct_withdraw_id              = $request->direct_withdraw_id;
			$direct_withdraw_startdate       = $request->direct_withdraw_startdate;
			$direct_withdraw_enddate         = $request->direct_withdraw_enddate;
			
			$_datasum = Mt4Trades::selectRaw("
				sum(case when mt4_trades.COMMENT not like '%-XY' and mt4_trades.PROFIT < 0 then mt4_trades.PROFIT else 0 end) as directdrawalActProfit
			")->where('mt4_trades.OPEN_PRICE', 0)->where('mt4_trades.PROFIT', '<', 0)->where('mt4_trades.CMD', 6)
				->whereIn('mt4_trades.LOGIN', function ($whereIn) {
					$whereIn->select('mt4_users.LOGIN')->from('mt4_users')->where('mt4_users.ZIPCODE', $this->_user['user_id']);
				})->where(function ($query) {
					$query->orWhere(function ($subQuery) {
						$subQuery->orWhere('mt4_trades.COMMENT', 'like',  '%-ZH') //佣金转户出金
						->orWhere('mt4_trades.COMMENT', 'like', '%-QK')
							->orWhere('mt4_trades.COMMENT', 'like', '%-RJTH'); //CZ入金退回
					})->orWhere(function ($orSubQuery) {
						$orSubQuery->orWhere('mt4_trades.COMMENT', 'like', '%Adj');
					});
				})->where(function ($subWhere) use ($direct_withdraw_id, $direct_withdraw_startdate, $direct_withdraw_enddate) {
					if (!empty($direct_withdraw_startdate) && !empty($direct_withdraw_enddate) && $this->_exte_is_Date ($direct_withdraw_startdate) && $this->_exte_is_Date ($direct_withdraw_enddate)) {
						$subWhere->whereBetween('mt4_trades.MODIFY_TIME', [$direct_withdraw_startdate .' 00:00:00', $direct_withdraw_enddate . ' 23:59:59']);
					} else {
						if(!empty($direct_withdraw_startdate) && $this->_exte_is_Date ($direct_withdraw_startdate)) {
							$subWhere->where('mt4_trades.MODIFY_TIME',  '>=', $direct_withdraw_startdate .' 23:59:59');
						}
						if(!empty($direct_withdraw_enddate) && $this->_exte_is_Date ($direct_withdraw_enddate)) {
							$subWhere->where('mt4_trades.MODIFY_TIME', '<', $direct_withdraw_enddate .' 00:00:00');
						}
					}
					
					if (!empty($direct_withdraw_id)) {
						$subWhere->where('mt4_trades.TICKET', 'like', '%' . $direct_withdraw_id . '%');
					}
				})->get()->toArray();
			
			return $_datasum;
		}
		
		/*protected function _exte_excel_data_structure_format($data)
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
		}*/
	}