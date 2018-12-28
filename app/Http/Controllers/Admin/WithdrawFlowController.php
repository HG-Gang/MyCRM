<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018/8/17
	 * Time: 16:45
	 */
	
	namespace App\Http\Controllers\Admin;
	
	
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	use Illuminate\Http\Request;
	use App\Model\Mt4Trades;
	
	class WithdrawFlowController extends Abstract_Mt4service_Controller
	{
		public function withdraw_flow()
		{
			return view('admin.withdraw_flow.withdraw_flow_browse');
		}
		
		public function withdrawFlowSearch(Request $request)
		{
			$data = array(
				'direct_withdraw_userId'       => $request->userId,
				'direct_withdraw_id'           => $request->withdraw_id,
				'direct_withdraw_source'       => $request->withdraw_source,
				'direct_withdraw_startdate'    => $request->deposit_startdate,
				'direct_withdraw_enddate'      => $request->deposit_enddate,
			);
			
			$result = array('rows' => '', 'total' => '');
			
			$_rs = $this->get_all_withdraw_list ('page', $data);
			
			if (!empty($_rs)) {
				$result['rows'] = $_rs;
				$result['total'] = $this->get_all_withdraw_list ('count', $data);
				/*$_datasum = $this->get_withdraw_list_sum_data ($request);
				$result['footer'] = [[
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
		
		protected function get_all_withdraw_list($totalType, $data)
		{
			$query_sql = Mt4Trades::selectRaw("
				mt4_trades.TICKET as order_no,
				mt4_trades.LOGIN as userId,
				mt4_trades.PROFIT as directProfit,
				mt4_trades.COMMENT as directType,
				mt4_trades.COMMENT as directComment,
				mt4_trades.MODIFY_TIME as directModifyTime
			")->where('mt4_trades.OPEN_PRICE', 0)->where('mt4_trades.PROFIT', '<', 0)->where('mt4_trades.CMD', 6)
				->where(function ($query) {
					$query->orWhere(function ($subQuery) {
						$subQuery->orWhere('mt4_trades.COMMENT', 'like',  '%-ZH') //佣金转户
						->orWhere('mt4_trades.COMMENT', 'like', '%-TH') //佣金转户退回
						->orWhere('mt4_trades.COMMENT', 'like', '%-QK') //取款
						->orWhere('COMMENT', 'like', '%-FY') //返佣
						//->orWhere('mt4_trades.COMMENT', 'like', '%-RJ') // 批量入金
						//->orWhere('mt4_trades.COMMENT', 'like', '%-XY') // 信用
						->orWhere('mt4_trades.COMMENT', 'like', '%-CJTH'); // 出金失败，退回
					})->orWhere(function ($orSubQuery) {
						$orSubQuery->orWhere('mt4_trades.COMMENT', 'like', '%Adj%');
					});
				})->where(function ($subWhere) use ($data) {
					$this->_exte_set_search_condition($subWhere, $data);
				});
			
			return $this->_exte_get_query_sql_data($query_sql, $totalType, 'mt4_trades.MODIFY_TIME');
		}
		
		protected function _exte_set_search_condition($subWhere, $data)
		{
			if (!empty($data['direct_withdraw_startdate']) && !empty($data['direct_withdraw_enddate']) && $this->_exte_is_Date ($data['direct_withdraw_startdate']) && $this->_exte_is_Date ($data['direct_withdraw_enddate'])) {
				$subWhere->whereBetween('mt4_trades.MODIFY_TIME', [$data['direct_withdraw_startdate'] .' 00:00:00', $data['direct_withdraw_enddate'] . ' 23:59:59']);
			} else {
				if(!empty($data['direct_withdraw_startdate']) && $this->_exte_is_Date ($data['direct_withdraw_startdate'])) {
					$subWhere->where('mt4_trades.MODIFY_TIME',  '>=', $data['direct_withdraw_startdate'] .' 23:59:59');
				}
				if(!empty($data['direct_withdraw_enddate']) && $this->_exte_is_Date ($data['direct_withdraw_enddate'])) {
					$subWhere->where('mt4_trades.MODIFY_TIME', '<', $data['direct_withdraw_enddate'] .' 00:00:00');
				}
			}
			
			if (!empty($data['direct_withdraw_userId'])) {
				$subWhere->where('mt4_trades.LOGIN', $data['direct_withdraw_userId']);
			}
			if (!empty($data['direct_withdraw_id'])) {
				$subWhere->where('mt4_trades.TICKET', 'like', '%' . $data['direct_withdraw_id'] . '%');
			}
			
			if(!empty($data['direct_withdraw_source'])) {
				$subWhere->where('mt4_trades.COMMENT', 'like', '%' . $data['direct_withdraw_source'] . '%');
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