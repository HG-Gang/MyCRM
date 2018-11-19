<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018-05-16
	 * Time: 下午 3:40
	 */
	
	namespace App\Http\Controllers\Admin;
	
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	use Illuminate\Http\Request;
	use App\Model\Mt4Trades;
	
	class AdminRealCommissionController extends Abstract_Mt4service_Controller
	{
		public function real_commission_list ()
		{
			return view('admin.real_commission_list.real_commission_list_browse');
		}
		
		public function realCommissionListSearch (Request $request)
		{
			$data = array(
				'userId'            => $request->userId,
				'orderId'           => $request->orderId,
				'startdate'         => $request->startdate,
				'enddate'           => $request->enddate,
			);
			
			$result = array ('rows' => '', 'total' => '');
			
			$_rs = $this->get_rebate_order_id_list ('page', $data);
			
			if (!empty($_rs)) {
				$result['rows'] = $_rs;
				$result['total'] = $this->get_rebate_order_id_list ('count', $data);
				$_datasum = $this->get_rebate_order_id_sum_data ($data);
				$result['footer'] = [[
					'ticket'      => '总计',
					'login'       => '',
					'profit'      => $_datasum['search_total_profit'],
					'comment'     => '',
					'modify_time' => '',
				]];
			}
			
			return json_encode ($result);
		}
		
		protected function get_rebate_order_id_list ($totalType, $data)
		{
			$query_sql = Mt4Trades::selectRaw("
				MT4_TRADES.TICKET as ticket, MT4_TRADES.LOGIN as login, MT4_TRADES.PROFIT as profit,
				MT4_TRADES.COMMENT as comment, MT4_TRADES.MODIFY_TIME as modify_time"
			)->where('mt4_trades.CMD', 6)
				->where('mt4_trades.COMMENT', 'like', '%-FY')
				->where(function ($subWhere) use ($data) {
					$this->_exte_set_search_condition($subWhere, $data);
				});
			
			return $this->_exte_get_query_sql_data($query_sql, $totalType, 'mt4_trades.MODIFY_TIME');
		}
		
		protected function get_rebate_order_id_sum_data ($data)
		{
			$_all_rs        = array ();
			
			$_allsumdata['search_total'] = Mt4Trades::selectRaw ("
					/*总返佣*/
					sum( case when mt4_trades.CMD = 6 and mt4_trades.COMMENT like '%-FY' and mt4_trades.PROFIT > 0 then mt4_trades.PROFIT else 0 end ) as total_profit
				")->where('mt4_trades.CMD', 6)
				->where('mt4_trades.COMMENT', 'like', '%-FY')
				->where(function ($subWhere) use ($data) {
					$this->_exte_set_search_condition($subWhere, $data);
				})->get()->toArray();
			
			if (!empty($_allsumdata) && count ($_allsumdata) > 0) {
				/*返佣*/
				$_all_rs['search_total_profit']             = number_format($_allsumdata['search_total'][0]['total_profit'], 2, '.', '');
			}
			
			return $_all_rs;
		}
		
		protected function _exte_set_search_condition($subWhere, $data)
		{
			if (!empty($data['startdate']) && !empty($data['enddate']) && $this->_exte_is_Date ($data['startdate']) && $this->_exte_is_Date ($data['enddate'])) {
				$subWhere->whereBetween('mt4_trades.MODIFY_TIME', [$data['startdate'] .' 00:00:00', $data['enddate'] . ' 23:59:59']);
			} else {
				if(!empty($data['startdate']) && $this->_exte_is_Date ($data['startdate'])) {
					$subWhere->where('mt4_trades.MODIFY_TIME',  '>= ', $data['startdate'] .' 23:59:59');
				}
				if(!empty($data['enddate']) && $this->_exte_is_Date ($data['enddate'])) {
					$subWhere->where('mt4_trades.MODIFY_TIME', '<', $data['enddate'] .' 00:00:00');
				}
			}
			
			if (!empty($data['userId'])) {
				$subWhere->where('mt4_trades.LOGIN', 'like', '%' . $data['userId'] . '%');
			}
			if (!empty($data['orderId'])) {
				$subWhere->where('mt4_trades.TICKET', 'like', '%' . $data['orderId'] . '%');
			}
		}
	}