<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018-05-17
	 * Time: 下午 5:23
	 */
	
	namespace App\Http\Controllers\Admin;
	
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	
	use Illuminate\Http\Request;
	use App\Model\DrawRecordLog;
	
	class WithdrawAmountController extends Abstract_Mt4service_Controller
	{
		public function withdraw_apply()
		{
			return view('admin.withdraw_apply.withdraw_apply_browse');
		}
		
		public function withdrawOrderIdDetail($orderId)
		{
			$_order_info = DrawRecordLog::where('draw_record_log.record_id', $orderId)->where('draw_record_log.voided', '1')->get()->toArray();
			
			return view('admin.withdraw_apply.withdraw_apply_detail')->with(['_order_info' => $_order_info[0]]);
		}
		
		public function withdrawApplySearch (Request $request)
		{
			$data = array(
				'withdraw_userId'       => $request->userId,
				'withdraw_id'           => $request->withdraw_id,
				'withdraw_source'       => $request->withdraw_source,
				'withdraw_startdate'    => $request->withdraw_startdate,
				'withdraw_enddate'      => $request->withdraw_enddate,
			);
			
			$result = array('rows' => '', 'total' => '');
			
			$_rs = $this->get_all_withdraw_list ('page', $data);
			
			if (!empty($_rs)) {
				$result['rows'] = $_rs;
				$result['total'] = $this->get_all_withdraw_list ('count', $data);
				$_datasum = $this->get_withdraw_list_sum_data ($data);
				/*$result['footer'] = [[
					'order_no'          => '总计',
					'userId'            => '',
					'directProfit'      => $_datasum[0]['directProfit'],
					'directType'        => '',
					'directComment'     => '',
					'directModifyTime'  => '',
				]];*/
			}
			
			return json_encode ($result);
		}
		
		public function withdrawOrderStaus (Request $request) {
			
			$orderId            = $request->orderId;
			$orderStatus        = $request->orderStatus;
			$orderRemark        = $request->orderRemark;
			
			if (!in_array($orderStatus, array('0', '1', '2', '3'))) {
				return response()->json([
					'msg'               => 'FAIL',
					'err'               => 'invalidValue',
					'col'               => 'apply_status',
				]);
			}
			
			//TODO 出款失败，出款金额返回给原账户
			if ($orderStatus == 3) {
				$_order_info = DrawRecordLog::where('draw_record_log.record_id', $orderId)->where('draw_record_log.voided', '1')->get()->toArray();
				//开始出金退回处理
				$mt4 = $this->_exte_mt4_deposit_amount($_order_info[0]['user_id'], $_order_info[0]['apply_amount'], $_order_info[0]['user_id']. "-#" . $_order_info[0]['mt4_trades_no'] . self::CJTH);
				$info = $this->_exte_get_user_info($_order_info[0]['user_id']);
				$phone = substr($info['phone'], (stripos($info['phone'], '-') + 1));
				$data = array('user_name' => $info[0]['user_name'], 'user_id' => $info[0]['user_id'], 'amt' => $_order_info[0]['apply_amount']);
				if ($mt4['ret'] == '0') {
					$num = DrawRecordLog::where('draw_record_log.record_id', $orderId)->where('draw_record_log.voided', '1')
						->update([
							'apply_status'      => $orderStatus,
							'apply_remark'      => ($orderStatus == 2) ? '' : $orderRemark,
							'rec_upd_user'      => $this->_auser['username'],
							'rec_upd_date'      => date('Y-m-d H:i:s'),
						]);
					//出金退回短信通知
					$_phone = $this->_exte_send_phone_notify($phone, 'widthdrawTH', $data);
				}
			} else {
				$num = DrawRecordLog::where('draw_record_log.record_id', $orderId)->where('draw_record_log.voided', '1')
					->update([
						'apply_status'      => $orderStatus,
						'apply_remark'      => ($orderStatus == 2) ? '' : $orderRemark,
						'rec_upd_user'      => $this->_auser['username'],
						'rec_upd_date'      => date('Y-m-d H:i:s'),
					]);
			}
			
			if ($num) {
				return response()->json([
					'msg'               => 'SUC',
					'err'               => 'NOERR',
					'col'               => 'NOCOL',
				]);
			} else {
				return response()->json([
					'msg'               => 'FAIL',
					'err'               => 'UPDATEFAIL',
					'col'               => 'NOCOL',
				]);
			}
		}
		
		public function withdrawExport (Request $request)
		{
			$param                  = $request->data;
			$data = array(
				'withdraw_userId'       => $param['userId'],
				'withdraw_id'           => $param['withdraw_id'],
				'withdraw_source'       => $param['withdraw_source'],
				'withdraw_startdate'    => $param['withdraw_startdate'],
				'withdraw_enddate'      => $param['withdraw_enddate'],
				'witdraw_type'          => $request->type,
			);
			
			$cellData = $this->get_all_withdraw_list ('sum', $data);
			
			if (!empty($cellData)) {
				return response()->json(['msg' => $this->_exte_export_excel('出金申请', $request->role, $cellData)]);
			}
			
			return response()->json(['msg' => 'FAIL']);
		}
		
		public function withdraw_downloadfile($file, $role)
		{
			$file = $this->_exte_export_excel_basic_path($role) . $file . '.' . $this->_exte_export_excel_format();
			
			return response()->download($file);
		}
		
		protected function get_all_withdraw_list($totalType, $data)
		{
			$query_sql = DrawRecordLog::selectRaw("
				draw_record_log.record_id,
				draw_record_log.mt4_trades_no as mt4_ticket,
				draw_record_log.user_id as userId,
				draw_record_log.user_name as username,
				draw_record_log.apply_amount as applyamount,
				draw_record_log.act_apply_amount as actapplyamount,
				draw_record_log.act_pdg_rmb as actpdgrmb,
				draw_record_log.act_draw as actdraw,
				draw_record_log.draw_poundage as drawpoundage,
				draw_record_log.draw_rate as drawrate,
				draw_record_log.draw_bank_no as drawbankno,
				draw_record_log.draw_bank_class as drawbankclass,
				draw_record_log.apply_status as applystatus,
				draw_record_log.apply_remark,
				draw_record_log.rec_crt_user,
				draw_record_log.rec_upd_user,
				draw_record_log.rec_crt_date,
				draw_record_log.rec_upd_date,
				mt4_trades.TICKET as MT4TICKET,
				mt4_trades.LOGIN as MT4LOGIN
			")->leftjoin('mt4_trades', function ($leftjoin) {
				$leftjoin->on('draw_record_log.mt4_trades_no', ' = ', 'mt4_trades.TICKET')
					->where('mt4_trades.CMD', '=', 6)->where('mt4_trades.OPEN_PRICE', '=', 0)->where('mt4_trades.PROFIT', '<', 0)
					->where('mt4_trades.COMMENT', 'LIKE', '%-QK');
			})->where('draw_record_log.voided', '1')
				->where(function ($subWhere) use ($data) {
					$this->_exte_set_search_condition($subWhere, $data);
				});
			
			return $this->_exte_get_query_sql_data($query_sql, $totalType, 'draw_record_log.rec_crt_date');
		}
		
		protected function get_withdraw_list_sum_data($data)
		{
			$_sumdata = DrawRecordLog::selectRaw("
				/*总申请金额USD*/
				sum( apply_amount ) as total_apply_amount,
				/*实际取款金额RMB*/
				sum( act_draw ) as total_act_draw,
				/*总取款手续费RMB*/
				sum( act_pdg_rmb ) as total_act_pdg_rmb
			")->leftjoin('mt4_trades', function ($leftjoin) {
				$leftjoin->on('draw_record_log.mt4_trades_no', ' = ', 'mt4_trades.TICKET')
					->where('mt4_trades.CMD', '=', 6)->where('mt4_trades.OPEN_PRICE', '=', 0)->where('mt4_trades.PROFIT', '<', 0)
					->where('mt4_trades.COMMENT', 'LIKE', '%-QK');
			})->where('draw_record_log.voided', '1')
				->where(function ($subWhere) use ($data) {
					$this->_exte_set_search_condition($subWhere, $data);
				})->get()->toArray();
			
			return $_sumdata;
		}
		
		protected function _exte_excel_data_structure_format($data)
		{
			$_rs = array();
			
			$_rs[0]['mt4_ticket']               = trans("systemlanguage.account_apply_order_no");
			$_rs[0]['userId']                   = trans("systemlanguage.account_apply_userId");
			$_rs[0]['username']                 = trans("systemlanguage.account_apply_userName");
			$_rs[0]['applyamount']              = trans("systemlanguage.account_apply_amount");
			$_rs[0]['actapplyamount']           = trans("systemlanguage.account_apply_actapplyamount");
			$_rs[0]['actdraw']                  = trans("systemlanguage.account_apply_actdraw");
			$_rs[0]['drawrate']                 = trans("systemlanguage.account_apply_drawrate");
			$_rs[0]['drawpoundage']             = trans("systemlanguage.account_apply_drawpoundage");
			$_rs[0]['applystatus']              = trans("systemlanguage.account_apply_status");
			$_rs[0]['rec_crt_date']             = trans("systemlanguage.account_rec_crt_date");
			
			foreach ($data as $key => $val) {
				$_rs[$key + 1]['mt4_ticket']        = $val['mt4_ticket'];
				$_rs[$key + 1]['userId']            = $val['userId'];
				$_rs[$key + 1]['username']          = $val['username'];
				$_rs[$key + 1]['applyamount']       = $val['applyamount'];
				$_rs[$key + 1]['actapplyamount']    = $val['actapplyamount'];
				$_rs[$key + 1]['actdraw']           = $val['actdraw'];
				$_rs[$key + 1]['drawrate']          = $val['drawrate'];
				$_rs[$key + 1]['drawpoundage']      = $val['drawpoundage'];
				$_rs[$key + 1]['applystatus']       = $this->_exte_get_withdraw_apply_order_status($val['applystatus']);
				$_rs[$key + 1]['rec_crt_date']      = $val['rec_crt_date'];
			}
			
			return $_rs;
		}
		
		protected function _exte_get_withdraw_apply_order_status($val)
		{
			$status = "";
			
			if ($val == '0') {
				$status = '待处理';
			} else if ($val == '1') {
				$status = '正在处理';
			} else if ($val == '2') {
				$status = '已处理';
			} else if ($val == '3') {
				$status = '处理失败';
			} else {
				$status = '未知状态';
			}
			
			return $status;
		}
		
		protected function _exte_set_search_condition($subWhere, $data)
		{
			if (!empty($data['withdraw_startdate']) && !empty($data['withdraw_enddate']) && $this->_exte_is_Date ($data['withdraw_startdate']) && $this->_exte_is_Date ($data['withdraw_enddate'])) {
				$subWhere->whereBetween('mt4_trades.MODIFY_TIME', [$data['withdraw_startdate'] .' 00:00:00', $data['withdraw_enddate'] . ' 23:59:59']);
			} else {
				if(!empty($data['withdraw_startdate']) && $this->_exte_is_Date ($data['withdraw_startdate'])) {
					$subWhere->where('mt4_trades.MODIFY_TIME',  '>= ', $data['withdraw_startdate'] .' 23:59:59');
				}
				if(!empty($data['withdraw_enddate']) && $this->_exte_is_Date ($data['withdraw_enddate'])) {
					$subWhere->where('mt4_trades.MODIFY_TIME', '<', $data['withdraw_enddate'] .' 00:00:00');
				}
			}
			
			if (!empty($data['withdraw_userId'])) {
				$subWhere->where('draw_record_log.user_id', $data['withdraw_userId']);
			}
			
			if (!empty($data['withdraw_id'])) {
				$subWhere->where('draw_record_log.mt4_trades_no', $data['withdraw_id']);
			}
			
			if($data['withdraw_source'] != '') {
				$subWhere->where('draw_record_log.apply_status', $data['withdraw_source']);
			}
			
			return $subWhere;
		}
		
		protected function _exte_get_query_sql_data($sql, $totalType, $col, $orderBy = 'desc')
		{
			$id_list        = array ();
			
			if ($totalType == 'page') {
				$id_list = $sql->skip($this->_offset)->take($this->_pageSize)->orderBy($col, $orderBy)->get()->toArray();
			} else if ($totalType == 'count') {
				$id_list = $sql->count();
			} else if ($totalType == 'sum') {
				$id_list = $sql->orderBy($col, $orderBy)->get()->toArray();
			}
			
			return $id_list;
		}
	}