<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018-05-17
	 * Time: 下午 3:45
	 */
	
	namespace App\Http\Controllers\Admin;
	
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	use App\Model\DepositRecordLog;
	use App\Model\Mt4Trades;
	use Illuminate\Http\Request;
	
	class DepositAmountController extends Abstract_Mt4service_Controller
	{
		public function deposit_flow ()
		{
			return view('admin.deposit_flow.deposit_flow_browse');
		}
		
		public function depositFlowSearch (Request $request)
		{
			$result = array('rows' => '', 'total' => '', 'footer' => '');
			
			$_rs = $this->get_all_deposit_list ('page', $request);
			
			if (!empty($_rs)) {
				//重新整理rs 结果集，将实际支付金额加进来，只有入金才有实际金额，其他均为0.00
				$data = $this->_exte_modify_data_structure($_rs);
				$result['rows'] = $data;
				$result['total'] = $this->get_all_deposit_list ('count', $request);
				$_datasum = $this->get_deposit_list_sum_data ($request);
				$result['footer'] = [[
					'order_no'          => '总计[不含信用]',
					'userId'            => '',
					'directProfit'      => $_datasum[0]['directProfit'],
					'depamount'         => '',
					'directType'        => '',
					'directComment'     => '',
					'depoutTrande'      => '',
					'directModifyTime'  => '',
				]];
			}
			
			return json_encode ($result);
		}
		
		public function depositExport(Request $request)
		{
			$cellData = $this->get_all_deposit_list ('sum', $request);
			$data = $this->_exte_modify_data_structure($cellData);
			
			if (!empty($cellData)) {
				return response()->json(['msg' => $this->_exte_export_excel('入金流水', $request->role, $data)]);
			}
			
			return response()->json(['msg' => 'FAIL']);
		}
		
		public function DownloadFile($file, $role)
		{
			$file = $this->_exte_export_excel_basic_path($role) . $file . '.' . $this->_exte_export_excel_format();
			
			return response()->download($file);
		}
		
		protected function get_all_deposit_list($totalType, $request)
		{
			$direct_deposit_userId           = $request->userId;
			$direct_deposit_id               = $request->deposit_id;
			$direct_deposit_source           = $request->direct_deposit_source; //入金来源
			$direct_deposit_startdate        = $request->deposit_startdate;
			$direct_deposit_enddate          = $request->deposit_enddate;
			
			//用于导出Excel的过滤条件取值方式
			if ($request->type == 'depositFlow') {
				$data                        = $request->data;
				$direct_deposit_userId       = $data['userId'];
				$direct_deposit_id           = $data['deposit_id'];
				$direct_deposit_source       = $data['direct_deposit_source'];
				$direct_deposit_startdate    = $data['deposit_startdate'];
				$direct_deposit_enddate      = $data['deposit_enddate'];
			}
			
			$query_sql = Mt4Trades::selectRaw("
				mt4_trades.TICKET as order_no,
				mt4_trades.LOGIN as userId,
				mt4_trades.PROFIT as directProfit,
				mt4_trades.COMMENT as directType,
				mt4_trades.COMMENT as directComment,
				mt4_trades.MODIFY_TIME as directModifyTime
			")->where('mt4_trades.OPEN_PRICE', 0)->where('mt4_trades.PROFIT', '>', 0)->where('mt4_trades.CMD', 6)
				->where(function ($query) {
					$query->orWhere(function ($subQuery) {
						$subQuery->orWhere('mt4_trades.COMMENT', 'like',  '%-ZH') //佣金转户
						->orWhere('mt4_trades.COMMENT', 'like', '%-TH') //佣金转户退回
						->orWhere('mt4_trades.COMMENT', 'like', '%-CZ') //充值
						->orWhere('COMMENT', 'like', '%-FY') //返佣
						->orWhere('mt4_trades.COMMENT', 'like', '%-RJ') // 批量入金
						//->orWhere('mt4_trades.COMMENT', 'like', '%-XY') // 信用
						->orWhere('mt4_trades.COMMENT', 'like', '%-CJTH'); // 出金失败，退回
					})->orWhere(function ($orSubQuery) {
						$orSubQuery->orWhere('mt4_trades.COMMENT', 'like', '%Adj%');
					});
				})->where(function ($subWhere) use ($direct_deposit_userId, $direct_deposit_id, $direct_deposit_source, $direct_deposit_startdate, $direct_deposit_enddate) {
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
					
					if (!empty($direct_deposit_userId)) {
						$subWhere->where('mt4_trades.LOGIN', $direct_deposit_userId);
					}
					if (!empty($direct_deposit_id)) {
						$subWhere->where('mt4_trades.TICKET', 'like', '%' . $direct_deposit_id . '%');
					}
					
					if($direct_deposit_source != "") {
						$subWhere->where('mt4_trades.COMMENT', 'like', '%' . $direct_deposit_source . '%');
					}
				});
			
			if ($totalType == 'page') {
				$id_list = $query_sql->skip($this->_offset)->take($this->_pageSize)->orderBy('mt4_trades.MODIFY_TIME', 'desc')->get()->toArray();
			} else if ($totalType == 'count') {
				$id_list = $query_sql->count();
			} else if ($totalType == 'sum') {
				$id_list = $query_sql->orderBy('mt4_trades.MODIFY_TIME', 'desc')->get()->toArray();
			}
			
			return $id_list;
		}
		
		//汇总不含XY信用
		protected function get_deposit_list_sum_data($request)
		{
			$direct_deposit_userId           = $request->userId;
			$direct_deposit_id               = $request->deposit_id;
			$direct_deposit_source           = $request->direct_deposit_source; //入金来源
			$direct_deposit_startdate        = $request->deposit_startdate;
			$direct_deposit_enddate          = $request->deposit_enddate;
			
			$_datasum = Mt4Trades::selectRaw("
				sum(case when mt4_trades.COMMENT not like '%-XY' and mt4_trades.PROFIT > 0 then mt4_trades.PROFIT else 0 end) as directProfit
			")->where('mt4_trades.OPEN_PRICE', 0)->where('mt4_trades.PROFIT', '>', 0)->where('mt4_trades.CMD', 6)
				->where(function ($query) {
					$query->orWhere(function ($subQuery) {
						$subQuery->orWhere('COMMENT', 'like',  '%-ZH') //佣金转户
						->orWhere('mt4_trades.COMMENT', 'like', '%-TH') //佣金转户退回
						->orWhere('mt4_trades.COMMENT', 'like', '%-CZ') //充值
						->orWhere('COMMENT', 'like', '%-FY') //返佣
						->orWhere('mt4_trades.COMMENT', 'like', '%-RJ') // 批量入金
						//->orWhere('mt4_trades.COMMENT', 'like', '%-XY') // 信用
						->orWhere('mt4_trades.COMMENT', 'like', '%-CJTH'); // 出金失败，退回
					})->orWhere(function ($orSubQuery) {
						$orSubQuery->orWhere('mt4_trades.COMMENT', 'like', '%Adj%');
					});
				})->where(function ($subWhere) use ($direct_deposit_userId, $direct_deposit_id, $direct_deposit_source, $direct_deposit_startdate, $direct_deposit_enddate) {
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
					
					if (!empty($direct_deposit_userId)) {
						$subWhere->where('mt4_trades.LOGIN', $direct_deposit_userId);
					}
					if (!empty($direct_deposit_id)) {
						$subWhere->where('mt4_trades.LOGIN', 'like', '%' . $direct_deposit_id . '%');
					}
					if($direct_deposit_source != "") {
						$subWhere->where('mt4_trades.COMMENT', 'like', '%' . $direct_deposit_source . '%');
					}
				})->get()->toArray();
			
			return $_datasum;
		}
		
		protected function _exte_modify_data_structure($_rs)
		{
			foreach ($_rs as $k => $v) {
				$data[$k] = DepositRecordLog::select('dep_amount', 'dep_outTrande', 'dep_outChannelNo')->where('dep_mt4_id', $v['order_no'])->where('dep_status', '02')->where('voided', '02')->get()->toArray();
				$_rs[$k]['depamount'] = (empty($data[$k])) ? 0 : $data[$k][0]['dep_amount'];
				$_rs[$k]['depoutTrande'] = (empty($data[$k])) ? '' : $data[$k][0]['dep_outTrande'];
				$_rs[$k]['depoutChannelNo'] = (empty($data[$k])) ? '' : $data[$k][0]['dep_outChannelNo'];
			}
			
			return $_rs;
		}
		
		protected function _exte_excel_data_structure_format($data)
		{
			$_rs = array();
			
			$_rs[0]['act_order_no']              = trans("systemlanguage.account_deposit_order_no");
			$_rs[0]['act_userId']                = trans("systemlanguage.account_deposit_no");
			$_rs[0]['act_directType']            = trans("systemlanguage.account_deposit_type");
			$_rs[0]['act_directComment']         = trans("systemlanguage.account_deposit_comment");
			$_rs[0]['act_directProfit']          = trans("systemlanguage.account_deposit_moneny");
			$_rs[0]['act_directProfitRMB']       = trans("systemlanguage.account_deposit_depamount");
			$_rs[0]['act_directflownumber']     = trans("systemlanguage.account_deposit_flownumber"); //充值流水号
			$_rs[0]['act_directModifyTime']      = trans("systemlanguage.account_deposit_datetme");
			
			foreach ($data as $key => $val) {
				$_rs[$key + 1]['act_order_no']          = $val['order_no'];
				$_rs[$key + 1]['act_userId']            = $val['userId'];
				$_rs[$key + 1]['act_directType']        = $val['directType'];
				$_rs[$key + 1]['act_directComment']     = $this->_exte_amount_source_desc($val['directComment']);
				$_rs[$key + 1]['act_directProfit']      = $val['directProfit'];
				$_rs[$key + 1]['act_directProfitRMB']   = $val['depamount'];
				$_rs[$key + 1]['act_directflownumber']  = $val['depoutTrande'];
				$_rs[$key + 1]['act_directModifyTime']  = $val['directModifyTime'];
			}
			
			return $_rs;
		}
	}