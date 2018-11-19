<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018/6/28
	 * Time: 15:20
	 */
	
	namespace App\Http\Controllers\Admin;
	
	
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	use Illuminate\Http\Request;
	
	class BatchAmountController extends Abstract_Mt4service_Controller
	{
		public function batch_operation_browse ()
		{
			return view('admin.batch_amount.batch_operation_browse');
		}
		
		public function batch_operation_withdraw_browse ()
		{
			return view('admin.batch_amount_withdraw.batch_operation_withdraw_browse');
		}
		
		public function batchOperation(Request $request)
		{
			$startdate              = time();
			$deposit_amount         = $request->deposit_amount;
			$deposit_comment        = $request->deposit_comment;
			$id_list                = $request->id_list;
			$i                      = 0;
			$order                  = '';
			
			foreach (explode(',', $id_list) as $k => $id) {
				$deo[$k] = $this->_exte_mt4_deposit_amount($id, $deposit_amount, $deposit_comment);
				$i ++;
				$order .= $deo[$k]['order'] . ',';
			}
			
			$enddate                = time();
			
			return response()->json([
				'no'            => $i,
				'time'          => ($enddate - $startdate),
				'order'         => rtrim($order, ','),
			]);
			
		}
		
		public function batchOperationWithdraw(Request $request)
		{
			$startdate              = time();
			$withdraw_amount        = $request->withdraw_amount;
			$withdraw_comment       = $request->withdraw_comment;
			$id_list                = $request->id_list;
			$i                      = 0;
			$order                  = '';
			
			foreach (explode(',', $id_list) as $k => $id) {
				$deo[$k] = $this->_exte_mt4_withdrawal_amount($id, $withdraw_amount, $withdraw_comment);
				$i ++;
				$order .= $deo[$k]['order'] . ',';
			}
			
			$enddate                = time();
			
			return response()->json([
				'no'            => $i,
				'time'          => ($enddate - $startdate),
				'order'         => rtrim($order, ','),
			]);
			
		}
	}