<?php
	
	namespace App\Http\Controllers\Admin;
	
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	use App\Model\Agents;
	use Illuminate\Http\Request;
	use App\Model\RightsSum;
	use App\Model\RightsSumTmp;
	use App\Model\Mt4Trades;
	
	class RightsSummaryController extends Abstract_Mt4service_Controller
	{
		public function rights_summary_browse()
		{
			
			return view('admin.rights_summary.rights_summary_browse')->with(['role' => $this->Role()]);
		}
		
		public function RightsSummarySearch(Request $request)
		{
			$result = array('rows' => '', 'total' => '');
			
			$_rs = $this->get_rights_sum_list('page', $request);
			
			if (!empty($_rs)) {
				foreach ($_rs as $k => $v) {
					$realtotal = $this->get_rights_sum_list_detail_total($v['rightsUserId'], $v['rightsSumStatus'], $v['rightsSumDate']);
					$_rs[$k]['realamt'] = $realtotal[0]['rightsSumSubRealamtTotal'];
				}
				$result['rows']     = $_rs;
				$result['total']    = count($this->get_rights_sum_list('count', $request));
			}
			
			return json_encode($result);
		}
		
		//权益结算明细
		public function RightsSummarySearchDetail($uid, $status, $sumdata)
		{
			$result = array('rows' => '', 'total' => '');
			
			$_rs = $this->get_rights_sum_list_detail('page', $uid, $status, $sumdata);
			$finalTotal = $finalshouxifei = $finalswaps = 0;
			
			if (!empty($_rs)) {
				$_datasum = $this->get_rights_sum_list_detail_total($uid, $status, $sumdata);
				
				$result['rows']     = $_rs;
				$result['footer']   = [[
					'rightsUserId'              => '',
					'rightsSumRemarks'          => '总计',
					'rightsUserProfit'          => $_datasum[0]['rightsSumSubProfit'],
					'rightsUserVolume'          => $_datasum[0]['rightsUserVolumeTotal'],
					'rightsUserValue'           => '',
					'rightsUserValueDiff'       => '',
					'rightsUseCycle'            => '',
					'rightsSumReturnamt'        => '',//$_datasum[0]['rightsSumReturnamtTotal'],
					'rightsSumShouxufei'        => $_datasum[0]['rightsSumSubShouxufeiTotal'],
					'rightsSumSwaps'            => $_datasum[0]['rightsSumSubSwapsTotal'],
					'rightsSumMoney'            => '',//$_datasum[0]['rightsSumMoneyTotal'],
					'rightsSumYajin'            => '',//$_datasum[0]['rightsSumYajinTotal'],
					'rightsSumRealamt'          => $_datasum[0]['rightsSumSubRealamtTotal'],
				]];
			}
			
			return json_encode($result);
		}
		
		//权益结算
		public function ConfirmWithdrawOrdeposit(Request $request)
		{
			$uid        = $request->uid;
			$real_amt   = $request->real_amt; //实返金额
			$other_amt  = $request->other_amt; // 扣款金额
			$amount     = $request->amount; //应得金额
			$sumdata    = $request->sumdata; //统计日期
			$status     = $request->status; //订单状态
			$type       = $request->type;
			
			if ($other_amt == '') {
				$amount = $real_amt;
			}
			
			//查找当前统计记录信息详情
			$_info = RightsSum::where('rights_user_id', $uid)->where('rights_sum_date', $sumdata)
				->where('voided', '1')->where('rights_sum_status', '1')
				->where('rights_sum_datetype', 'mainData')->get()->toArray();
			
			if (empty($_info)) {
				return response()->json([
					'msg'        => 'FAIL',
					'err'        => 'errflowrecord', //错误的流水记录
					'col'        => 'NOCOL',
				]);
			}
			
			if (!in_array($type, array('deposit', 'withdraw'), true)) {
				return response()->json([
					'msg'        => 'FAIL',
					'err'        => 'errparams', //错误的参数
					'col'        => 'NOCOL',
				]);
			}
			
			$realtotal = $this->get_rights_sum_list_detail_total($uid, $status, $sumdata);
			//$cmt = substr($_info[0]['rights_sum_remarks'], 0, strpos($_info[0]['rights_sum_remarks'], $sumdata)) . $sumdata;
			if ($amount != $realtotal[0]['rightsSumSubRealamtTotal'] && $other_amt == '') {
				return response()->json([
					'msg'        => 'FAIL',
					'err'        => 'errrightsSumMoney', //结算金额与实际金额不匹配
					'col'        => 'NOCOL',
				]);
			}
			
			$chk_mt4 = Mt4Trades::where('LOGIN', $_info[0]['rights_user_id'])->where('PROFIT', abs($amount))->where('COMMENT', $_info[0]['rights_sum_remarks'])->where('CMD', 6)->first();
			if ($chk_mt4 != null) {
				return response()->json([
					'msg'        => 'FAIL',
					'err'        => 'erralreadybalance', //该记录的结算周期已结算过
					'col'        => 'NOCOL',
				]);
			}
			
			/*
		 * $str = 'Adj-151-201807300805-100003';
			dd(strpos($str, '201807300805'));
			dd(substr($str, 0, strpos($str, '201807300805')));
		 * */
			if ($type == 'deposit') {
				//账户入金 Adj-0000000000-000000000000-100004
				$mt4 = $this->_exte_mt4_deposit_amount($_info[0]['rights_user_id'], abs($amount), $_info[0]['rights_sum_remarks']);
			} else if ($type == 'withdraw') {
				//账户出金 Adj-0000000000-000000000000-100004
				//substr('deposit-Adj-1-201807160722', stripos('deposit-Adj-1-201807160722', '-'));
				//$tmp_cmt = $type . substr($_info[0]['rights_sum_remarks'], stripos($_info[0]['rights_sum_remarks'], '-'));
				$mt4 = $this->_exte_mt4_withdrawal_amount($_info[0]['rights_user_id'], abs($amount), $_info[0]['rights_sum_remarks']);
			}
			
			if (is_array($mt4) && $mt4['ret'] == 0) {
				$_ag_info = $this->_exte_get_user_info(($_info[0]['rights_user_id']));
				$data['user_name']  = $_ag_info['user_name'];
				$data['user_id']    = $_ag_info['user_id'];
				$data['phone']      = substr($_ag_info['phone'], (stripos($_ag_info['phone'], '-') + 1));
				$data['sum_date']   = $_info[0]['rights_sum_date'];
				$data['sum_amt']    = ($type == 'deposit') ? '入金' . abs($amount) : '扣除金额' . abs($amount);
				//$_rs = $this->_exte_send_phone_notify($data['phone'], 'RightsSum', $data);
				$upd = RightsSum::where('rights_user_id', $uid)->where('voided', '1')
					->where('rights_sum_date', $sumdata)
					->where('rights_sum_status', '1')->update([
						'rights_mt4_orderId'            => $mt4['order'],
						'rights_sum_status'             => '2',
						'rec_upd_date'                  => date('Y-m-d H:i:s'),
						'rec_upd_user'                  => $this->_auser['username'],
					]);
				
				return response()->json([
					'msg'        => 'SUC',
					'err'        => 'NOERR',
					'col'        => 'NOCOL',
				]);
			} else {
				return response()->json([
					'msg'        => 'FAIL',
					'err'        => 'erroptions', //操作失败
					'col'        => 'NOCOL',
				]);
			}
		}
		
		//手动结算权益
		public function ManualConfirmWithdrawOrdeposit (Request $request)
		{
			$manual_uid        = $request->manual_uid;
			$manual_sumdata    = $request->manual_sumdata; //统计日期
			$manual_status     = $request->manual_status; //订单状态
			$manual_reason     = $request->manual_reason; //手动结算权益 必填
			
			$upd = RightsSum::where('rights_user_id', $manual_uid)->where('voided', '1')
				->where('rights_sum_date', $manual_sumdata)
				->where('rights_sum_status', '1')->update([
					'rights_manual_reason'          => $manual_reason,
					'rights_sum_status'             => '2',
					'rec_upd_date'                  => date('Y-m-d H:i:s'),
					'rec_upd_user'                  => $this->_auser['username'],
				]);
			
			if ($upd) {
				return response()->json([
					'msg'        => 'SUC',
					'err'        => 'NOERR',
					'col'        => 'NOCOL',
				]);
			} else {
				return response()->json([
					'msg'        => 'FAIL',
					'err'        => 'erroptions', //操作失败
					'col'        => 'NOCOL',
				]);
			}
		}
		
		//导出Excel
		public function rightsSumExport(Request $request)
		{
			$cellData = $this->get_rights_sum_list ('sum', $request);
			
			if (!empty($cellData)) {
				foreach ($cellData as $k => $v) {
					$realtotal = $this->get_rights_sum_list_detail_total($v['rightsUserId'], $v['rightsSumStatus'], $v['rightsSumDate']);
					$cellData[$k]['realamt'] = $realtotal[0]['rightsSumSubRealamtTotal'];
				}
				return response()->json(['msg' => $this->_exte_export_excel('权益结算统计', $request->role, $cellData)]);
			}
			
			return response()->json(['msg' => 'FAIL']);
		}
		
		public function DownloadFile($file, $role)
		{
			$file = $this->_exte_export_excel_basic_path($role) . $file . '.' . $this->_exte_export_excel_format();
			
			return response()->download($file);
		}
		
		//定时统计线上结算模式的代理商上个星期一 -- 星期五 的 权益结算金额
		/*
			新的：
			
			100001（90）--100002（85）--100003（82）--100004（80）
							300			200				100  80
					5		3			2			80 100-80=20  90
			所有的插入数据前 需进行验证是否有相同记录
			
			汇总查询取到100004 进行汇总（代理商表PID=100004,客户表PID=100004 的所有交易数据）
			
			1、根据公式计算出100004的实际权益金额  Adj-index-date
				
				应返金额=((盈亏+利息+手续费)*权益值)*-1   （25912.1+(-3719）)*80%*-1= -17754.48
				
				押金金额=((盈亏+手续费+利息)*权益值)*10%  （（25912.1+（-44315）+（-3719））*80%）*10%= -1769.752
				
				实返金额=应返金额-押金金额  -17754.48-(-1769.75)=-15984.73
				
			2、继续进行查找100004的上级，得到100003，然后根据公式计算上级100003从100004得到的权益差金额   Adj-0000000000-000000000000-100004
			
				上级权益差金额=((盈亏+利息+手续费))*（上下级权益差） （25912.1+(-3719）)*（82%-80%）*-1
				
				应返金额=((盈亏+利息+手续费)*权益值)*-1   （25912.1+(-3719）)*80%*-1= -17754.48
				
				押金金额=（盈亏+手续费+利息*权益值）*10%  （（25912.1+（-44315）+（-3719））*80%）*10%= -1769.752
				
				实返金额=应返金额-押金金额  -17754.48-(-1769.75)=-15984.73
		*/
		public function sum_agents_online_settlement_amount()
		{
			$last_mon = getLastMonday(); //获取上个星期一日期
			$last_fir = getLastSunday(); //获取上个星期日日期
			
			//先查找出所有线上结算模式代理商ID, 权益值，结算周期
			$ag_info = $this->_exte_regresión_get_agt_id();
			
			$d = date('Ymd', strtotime($last_mon)) . date('md', strtotime($last_fir));
			
			//统计这些线上结算ID的 盈亏值，交易量 在上周一 到 上周五 值
			foreach ($ag_info as $key => $vdata) {
				if (RightsSumTmp::where('rights_sum_tmpcomm', $vdata['self']['user_id'] . '-' . $d)->where('voided', '1')->first() == null) {
					$tp[$key] = RightsSumTmp::create([
						'rights_sum_tmpuserId'          => $vdata['self']['user_id'],
						'rights_sum_tmp_date'           => $d,
						'rights_sum_tmpcomm'            => $vdata['self']['user_id'] . '-' . $d,
						'rights_sum_tmpstatus'          => '1',
						'voided'                        => '1',
						'rec_crt_date'                  => date('Y-m-d H:i:s'),
						'rec_upd_date'                  => date('Y-m-d H:i:s'),
						'rec_crt_user'                  => env('COMPANY_CODE'),
						'rec_upd_user'                  => env('COMPANY_CODE'),
					]);
					
					$rights_tmp[$key] = $vdata;
				}
			}
			
			if (!empty($rights_tmp)) {
				//$tmp_info = RightsSumTmp::where('rights_sum_tmpstatus', '1')->where('voided', '1')->get()->toArray();
				
				foreach ($rights_tmp as $key => $vdata) {
					//if (RightsSum::where('rights_sum_comm', $tmp_info[$key]['rights_sum_tmpcomm'])->first() == null) {
					//计算自己和自己的直属客户
					$_one_sumdata[$vdata['self']['user_id']]['all_total'] = Mt4Trades::selectRaw("
					/*代理商盈亏*/
					sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.PROFIT else 0 end ) as all_total_profit,
					/*代理商手数 == 代理商交易量*/
					sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as all_total_volume,
					/*代理商手续费*/
					sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.COMMISSION else 0 end ) as all_total_comm,
					/*代理商利息*/
					sum( case when mt4_trades.SWAPS < 0 and mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.SWAPS else 0 end ) as all_total_swaps
				")->where(function ($subQuery) use ($vdata) {
						$subQuery->whereIn('mt4_trades.LOGIN', function ($whereIn2) use ($vdata) {
							$whereIn2->selectRaw("
								/*普通客户*/
								user.user_id from user where user.parent_id = " . intval($vdata['self']['user_id']) . " and user.voided = '1' and user.user_status in ('0','1','2','4')
								/*代理商*/
								UNION
								select agents.user_id from agents where agents.parent_id = " . intval($vdata['self']['user_id']) . " and agents.voided = '1' and agents.user_status in ('0','1','2','4')
							");
						});
					})->where(function ($query2) use ($last_mon, $last_fir) {
						$query2->whereBetween('mt4_trades.CLOSE_TIME', [$last_mon . ' 00:00:00', $last_fir . ' 23:59:59']);
					})->get()->toArray();
					
					$user_profit[$key] = number_format($_one_sumdata[$vdata['self']['user_id']]['all_total'][0]['all_total_profit'], '2', '.', '');
					$user_swaps[$key] = number_format($_one_sumdata[$vdata['self']['user_id']]['all_total'][0]['all_total_swaps'], '2', '.', '');
					$user_comm[$key] = number_format($_one_sumdata[$vdata['self']['user_id']]['all_total'][0]['all_total_comm'], '2', '.', '');
					$rights_ary[$key] = $this->_exte_get_user_money($user_profit[$key], $user_swaps[$key], $user_comm[$key], $vdata['self']);
					
					$num[$key] = RightsSum::create([
						'rights_user_id'            => $vdata['self']['user_id'],
						'rights_user_pid'           => $vdata['self']['parent_id'],
						'rights_user_ident'         => strtoupper(md5($vdata['self']['user_id'] . date('YmdHis') . rand(123456, 999999))),
						'rights_user_profit'        => $user_profit[$key],
						'rights_user_volume'        => $_one_sumdata[$vdata['self']['user_id']]['all_total'][0]['all_total_volume'] / 100,
						'rights_user_value'         => $rights_tmp[$key]['self']['rights'] . ' / ' . $rights_tmp[$key]['self']['comm_prop'],
						'rights_user_cycle'         => $rights_tmp[$key]['self']['cycle'],
						'rights_mt4_orderId'        => '',
						'rights_sum_date'           => date('Ymd', strtotime($last_mon)) . date('md', strtotime($last_fir)),
						'rights_sum_status'         => '1',
						'rights_sum_shouxufei'      => $user_comm[$key],
						'rights_sum_swaps'          => $user_swaps[$key],
						'rights_sum_returnamt'      => abs(number_format(($user_comm[$key] * ($rights_tmp[$key]['self']['comm_prop'] / 100)), '2', '.', '')),
						'rights_sum_money'          => $rights_ary[$key]['rights_money'],
						'rights_sum_comm'           => $rights_tmp[$key]['self']['user_id'] . '-' .$d,
						'rights_sum_yajin'          => $rights_ary[$key]['rights_yajin'],
						'rights_sum_realamt'        => $rights_ary[$key]['rights_realamt'],
						'rights_sum_datetype'       => 'mainData',
						'voided'                    => '1',
						'rec_crt_date'              => date('Y-m-d H:i:s'),
						'rec_upd_date'              => date('Y-m-d H:i:s'),
						'rec_crt_user'              => env('COMPANY_CODE'),
						'rec_upd_user'              => env('COMPANY_CODE'),
					]);
					
					$upd[$key] = RightsSum::where('rights_user_ident', $num[$key]->rights_user_ident)->where('rights_user_id', $num[$key]->rights_user_id)->update([
						'rights_sum_remarks' => 'Adj-' . $num[$key]->rights_id . '-' . date('Ymd', strtotime($last_mon)) . date('md', strtotime($last_fir)) . self::FY,
					]);
					
					if (!empty($vdata['parentId'])) {
						foreach ($vdata['parentId'] as $pk => $pv) {
							$tmp = date('Ymd', strtotime($last_mon)) . date('md', strtotime($last_fir)) . '-' . $vdata['self']['parent_id'] . self::FY;
						//	if (RightsSum::where('rights_sum_remarks', 'like', '%' . $tmp . '%')->where('voided', '1')->first() == null) {
								$pidnum[$pk] = RightsSum::create([
									'rights_user_id'            => $pv['user_id'],
									'rights_user_pid'           => $pv['parent_id'],
									'rights_user_ident'         => strtoupper(md5($pv['user_id'] . date('YmdHis') . rand(123456, 999999))),
									'rights_user_profit'        => $user_profit[$key],
									'rights_user_volume'        => $_one_sumdata[$vdata['self']['user_id']]['all_total'][0]['all_total_volume'] / 100,
									'rights_user_value'         => $pv['rights'] .' / ' . $rights_tmp[$key]['self']['rights'],
									'rights_user_value_diff'    => ($pv['rights_diff']) ? $pv['rights_diff'] : 0,
									'rights_user_cycle'         => $pv['cycle'],
									'rights_mt4_orderId'        => '',
									'rights_sum_date'           => date('Ymd', strtotime($last_mon)) . date('md', strtotime($last_fir)),
									'rights_sum_status'         => '1',
									'rights_sum_shouxufei'      => $user_comm[$key],
									'rights_sum_swaps'          => $user_swaps[$key],
									'rights_sum_returnamt'      => 0,
									'rights_sum_money'          => 0,
									'rights_sum_comm'           => $pv['user_id'] . '-' .$d,
									'rights_sum_yajin'          => 0,
									'rights_sum_realamt'        => number_format((($user_profit[$key] + $user_swaps[$key] + $user_comm[$key]) * ($pv['rights_diff'] / 100)* -1), '2', '.', ''),
									'rights_sum_datetype'       => 'subData',
									'voided'                    => '1',
									'rec_crt_date'              => date('Y-m-d H:i:s'),
									'rec_upd_date'              => date('Y-m-d H:i:s'),
									'rec_crt_user'              => env('COMPANY_CODE'),
									'rec_upd_user'              => env('COMPANY_CODE'),
								]);
								
								$pidupd[$pk] = RightsSum::where('rights_user_ident', $pidnum[$pk]->rights_user_ident)->where('rights_user_id', $pidnum[$pk]->rights_user_id)->update([
									'rights_sum_remarks'        => 'Adj-' . $pidnum[$pk]->rights_id . '-' . date('Ymd', strtotime($last_mon)) . date('md', strtotime($last_fir)) . '-' . $vdata['self']['user_id'] . self::FY,
								]);
						//	}
						}
					}
					
					$upd2[$key] = RightsSumTmp::where('rights_sum_tmpuserId', $vdata['self']['user_id'])->where('voided', '1')->update([
						'rights_sum_tmpstatus'          => '2',
					]);
					
					$_info[$key] = RightsSum::where('rights_id', $num[$key]->rights_id)->get()->toArray();
					$this->debugfile($_info[$key], '权益周期结算', 'RightsSum-' . $d);
					/*} else {
						$upd3[$key] = RightsSumTmp::where('rights_sum_tmpuserId', $vdata['self']['user_id'])->where('voided', '1')->update([
							'rights_sum_tmpstatus'          => '2',
						]);
					}*/
				}
			}
		}
		
		protected function get_rights_sum_list($totalType, $request)
		{
			$userId         = $request->userId;
			$orderstatus    = $request->orderstatus;
			$rightsUserCycle= $request->rightsUserCycle;
			$startdate      = $request->startdate;
			$enddate        = $request->enddate;
			
			$query_sql = RightsSum::selectRaw("
				rights_sum.rights_id as rightsId,
				rights_sum.rights_user_ident as rightsUserIdent,
				rights_sum.rights_user_id as rightsUserId,
				rights_sum.rights_user_pid as rightsUserPId,
				rights_sum.rights_user_profit as rightsUserProfit,
				rights_sum.rights_user_volume as rightsUserVolume,
				rights_sum.rights_user_value as rightsUserValue,
				rights_sum.rights_user_value_diff as rightsUserValueDiff,
				rights_sum.rights_user_cycle as rightsUseCycle,
				rights_sum.rights_mt4_orderId as rightsMt4OrderId,
				rights_sum.rights_sum_date as rightsSumDate,
				rights_sum.rights_sum_status as rightsSumStatus,
				rights_sum.rights_manual_reason as rightsManualReason,
				rights_sum.rights_sum_returnamt as rightsSumReturnamt,
				rights_sum.rights_sum_shouxufei as rightsSumShouxufei,
				rights_sum.rights_sum_swaps as rightsSumSwaps,
				rights_sum.rights_sum_money as rightsSumMoney,
				rights_sum.rights_sum_yajin as rightsSumYajin,
				rights_sum.rights_sum_realamt as rightsSumRealamt,
				rights_sum.rights_sum_comm as rightsSumComm,
				rights_sum.rights_sum_remarks as rightsSumRemarks,
				rights_sum.rights_sum_datetype as rightsSumDatetype,
				rights_sum.voided as voided,
				rights_sum.rec_crt_date as rec_crt_date,
				rights_sum.rec_upd_date as rec_upd_date,
				rights_sum.rec_crt_user as rec_crt_user,
				rights_sum.rec_upd_user as rec_upd_user
			")->where('rights_sum.voided', '1')->where('rights_sum_datetype', 'mainData')
				->where(function ($subWhere) use ($userId, $orderstatus, $rightsUserCycle, $startdate, $enddate) {
					if (!empty($startdate) && !empty($enddate) && $this->_exte_is_Date($startdate) && $this->_exte_is_Date($enddate)) {
						$subWhere->whereBetween('rights_sum.rec_crt_date', [$startdate . ' 00:00:00', $enddate . ' 23:59:59']);
					} else {
						if (!empty($startdate) && $this->_exte_is_Date($startdate)) {
							$subWhere->where('rights_sum.rec_crt_date', '>= ', $startdate . ' 23:59:59');
						}
						if (!empty($enddate) && $this->_exte_is_Date($enddate)) {
							$subWhere->where('rights_sum.rec_crt_date', '<', $enddate . ' 00:00:00');
						}
					}
					
					if (!empty($userId)) {
						$subWhere->where('rights_sum.rights_user_id', $userId);
					}
					if ($orderstatus != '') {
						$subWhere->where('rights_sum.rights_sum_status', $orderstatus);
					}
					if ($rightsUserCycle != '') {
						$subWhere->where('rights_sum.rights_user_cycle', $rightsUserCycle);
					}
				});
			
			if ($totalType == 'page') {
				$id_list = $query_sql->skip($this->_offset)->take($this->_pageSize)->orderBy('rights_sum.rec_crt_date', 'desc')->get()->toArray();
			} else if ($totalType == 'count') {
				$id_list = $query_sql->get()->toArray();
			} else if ($totalType == 'sum') {
				$id_list = $query_sql->get()->toArray();
			}
			
			return $id_list;
		}
		
		protected function get_rights_sum_list_detail($totalType, $uid, $status, $sumdata)
		{
			$query_sql = RightsSum::selectRaw("
					rights_sum.rights_id as rightsId,
					rights_sum.rights_user_ident as rightsUserIdent,
					rights_sum.rights_user_id as rightsUserId,
					rights_sum.rights_user_pid as rightsUserPId,
					rights_sum.rights_user_profit as rightsUserProfit,
					rights_sum.rights_user_volume as rightsUserVolume,
					rights_sum.rights_user_value as rightsUserValue,
					rights_sum.rights_user_value_diff as rightsUserValueDiff,
					rights_sum.rights_user_cycle as rightsUseCycle,
					rights_sum.rights_mt4_orderId as rightsMt4OrderId,
					rights_sum.rights_sum_date as rightsSumDate,
					rights_sum.rights_sum_status as rightsSumStatus,
					rights_sum.rights_manual_reason as rightsManualReason,
					rights_sum.rights_sum_returnamt as rightsSumReturnamt,
					rights_sum.rights_sum_shouxufei as rightsSumShouxufei,
					rights_sum.rights_sum_swaps as rightsSumSwaps,
					rights_sum.rights_sum_money as rightsSumMoney,
					rights_sum.rights_sum_yajin as rightsSumYajin,
					rights_sum.rights_sum_realamt as rightsSumRealamt,
					rights_sum.rights_sum_comm as rightsSumComm,
					rights_sum.rights_sum_remarks as rightsSumRemarks,
					rights_sum.rights_sum_datetype as rightsSumDatetype,
					rights_sum.voided as voided
				")->where('rights_sum.rights_user_id', $uid)
				->where('rights_sum.rights_sum_date', $sumdata)
				->where('rights_sum.voided', '1')
				->where('rights_sum.rights_sum_status', $status);
			
			if ($totalType == 'page') {
				$id_list = $query_sql->orderBy('rights_sum.rec_crt_date', 'desc')->get()->toArray();
			} else if ($totalType == 'count') {
				//
			} else if ($totalType == 'sum') {
				//
			}
			
			return $id_list;
		}
		
		protected function get_rights_sum_list_detail_total($uid, $status, $sumdate)
		{
			$_datasum = RightsSum::selectRaw("
					sum(rights_user_profit) as rightsSumSubProfit,
					sum(rights_sum_shouxufei) as rightsSumSubShouxufeiTotal,
					sum(rights_sum_swaps) as rightsSumSubSwapsTotal,
					sum(rights_sum_realamt) as rightsSumSubRealamtTotal,
					sum(rights_user_volume) as rightsUserVolumeTotal
					/*sum(rights_sum_money) as rightsSumMoneyTotal,
					sum(rights_sum_returnamt) as rightsSumReturnamtTotal,
					sum(rights_sum_yajin) as rightsSumYajinTotal,*/
				")->where('rights_sum.voided', '1')
				->where('rights_sum.rights_sum_status', $status)
				->where('rights_sum.rights_user_id', $uid)
				->where('rights_sum.rights_sum_date', $sumdate)
				->get()->toArray();
			
			return $_datasum;
		}
		
		protected function _exte_get_user_money($amt, $swaps, $comm, $ag_info)
		{
			$_rs = array();
			if ($ag_info['trans_mode'] == '1') {
				//权益模式
				$tmp_rights_money = ($amt + $swaps + $comm) * ($ag_info['rights'] / 100) * -1;
				$tmp_rights_yajin = ($amt + $swaps + $comm) * ($ag_info['rights'] / 100) * 0.1 * -1;
				$_rs['rights_money']    = number_format($tmp_rights_money, '2', '.', '');
				$_rs['rights_yajin']    = number_format($tmp_rights_yajin, '2', '.', '');
				$_rs['rights_realamt']  = number_format(($tmp_rights_money - $tmp_rights_yajin), '2', '.', '');
			}
			
			return $_rs;
		}
		
		protected function _exte_excel_data_structure_format($data)
		{
			$_rs = array();
			
			$_rs[0]['rightsUserId']                     = trans("systemlanguage.rights_sum_userId");
			$_rs[0]['rightsUserProfit']                 = trans("systemlanguage.rights_sum_profit");
			$_rs[0]['rightsSumShouxufei']               = trans("systemlanguage.rights_sum_shouxufei");
			$_rs[0]['rightsSumSwaps']                   = trans("systemlanguage.rights_sum_swaps");
			$_rs[0]['rightsUserVolume']                  = trans("systemlanguage.rights_sum_volume");
			$_rs[0]['rightsUserValue']                  = trans("systemlanguage.rights_sum_userVal");
			$_rs[0]['rightsSumReturnamt']               = trans("systemlanguage.rights_sum_returnamt");
			$_rs[0]['rightsSumMoney']                   = trans("systemlanguage.rights_sum_money");
			$_rs[0]['rightsSumYajin']                   = trans("systemlanguage.rights_sum_yajin");
			$_rs[0]['rightsSumRealamt']                 = trans("systemlanguage.rights_sum_realamt");
			$_rs[0]['rightsSumDate']                    = trans("systemlanguage.rights_sum_date");
			$_rs[0]['rightsSumStatus']                  = trans("systemlanguage.rights_sum_status");
			$_rs[0]['rightsMt4OrderId']                 = trans("systemlanguage.rights_sum_mt4OrderId");
			$_rs[0]['rightsSumRemarks']                 = trans("systemlanguage.rights_sum_remarks");
			$_rs[0]['rightsUseCycle']                   = trans("systemlanguage.rights_sum_userCycle");
			$_rs[0]['rec_crt_date']                     = trans("systemlanguage.rights_sum_reccrtdate");
			
			foreach ($data as $key => $val) {
				$_rs[$key + 1]['rightsUserId']          = $val['rightsUserId'];
				$_rs[$key + 1]['rightsUserProfit']      = number_format($val['rightsUserProfit'], '2', '.', '');
				$_rs[$key + 1]['rightsSumShouxufei']    = number_format($val['rightsSumShouxufei'], '2', '.', '');
				$_rs[$key + 1]['rightsSumSwaps']        = number_format($val['rightsSumSwaps'], '2', '.', '');
				$_rs[$key + 1]['rightsUserVolume']      = $val['rightsUserVolume'];
				$_rs[$key + 1]['rightsUserValue']       = $val['rightsUserValue'];
				$_rs[$key + 1]['rightsSumReturnamt']    = $val['rightsSumReturnamt'];
				$_rs[$key + 1]['rightsSumMoney']        = $val['rightsSumMoney'];
				$_rs[$key + 1]['rightsSumYajin']        = $val['rightsSumYajin'];
				$_rs[$key + 1]['rightsSumRealamt']      = $val['realamt'];
				$_rs[$key + 1]['rightsSumDate']         = ' ' . $val['rightsSumDate'];
				$_rs[$key + 1]['rightsSumStatus']       = $this->_exte_get_rights_sum_status($val['rightsSumStatus']);
				$_rs[$key + 1]['rightsMt4OrderId']      = $val['rightsMt4OrderId'];
				$_rs[$key + 1]['rightsSumRemarks']      = $val['rightsSumRemarks'];
				$_rs[$key + 1]['rightsUseCycle']        = $this->_exte_get_rights_sum_cycle($val['rightsUseCycle']);
				$_rs[$key + 1]['rec_crt_date']          = $val['rec_crt_date'];
			}
			
			return $_rs;
		}
		
		protected function _exte_get_rights_sum_status($status)
		{
			$str = '';
			if ($status == '1') {
				$str = '等待结算';
			} else if ($status == '2') {
				$str = '已经结算';
			} else {
				$str = '未知状态';
			}
			
			return $str;
		}
		
		protected function _exte_get_rights_sum_cycle($cycle)
		{
			$str = '';
			if ($cycle == '1') {
				$str = '周结';
			} else if ($cycle == '2') {
				$str = '半月结';
			} else if ($cycle == '3') {
				$str = '月结';
			} else {
				$str = '未知结算周期';
			}
			
			return $str;
		}
		
		protected function _exte_regresión_get_agt_id()
		{
			$_rs = [];
			
			$listId = Agents::select('agents.user_id', 'agents.parent_id', 'agents.rights', 'agents.cycle', 'agents.trans_mode', 'agents.comm_prop')
				->where('agents.voided', '1')->whereIn('agents.user_status', array('0', '1', '2', '4'))
				->where('agents.trans_mode', '1')->where('agents.settlement_model', '1')
				->get()->toArray();
			
			if (!empty($listId)) {
				foreach ($listId as $k => $v) {
					$_rs[$k]['self']['user_id']          = $v['user_id'];
					$_rs[$k]['self']['parent_id']       = $v['parent_id'];
					$_rs[$k]['self']['rights']          = $v['rights'];
					$_rs[$k]['self']['cycle']           = $v['cycle'];
					$_rs[$k]['self']['trans_mode']      = $v['trans_mode'];
					$_rs[$k]['self']['comm_prop']       = $v['comm_prop'];
					$_rs[$k]['parentId']                = $this->_exte_get_all_sub_listId($v['parent_id'], 0, $v['rights']);
				}
			}

			return $_rs;
		}
		
		protected function _exte_get_all_sub_listId($pid, $k, $self_rights)
		{
			global $_listId;
			
			if ($pid != 0) {
				$info = Agents::select('agents.user_id', 'agents.parent_id', 'agents.rights', 'agents.cycle', 'agents.trans_mode', 'agents.comm_prop')
					->where('agents.user_id', $pid)
					->where('agents.voided', '1')->whereIn('agents.user_status', array('0', '1', '2', '4'))
					->where('agents.trans_mode', '1')->where('agents.settlement_model', '1')
					->get()->toArray();
				
				if(count($info) > 0) {
					$_listId[$k]['user_id']				= $info[0]['user_id'];
					$_listId[$k]['parent_id']			= $info[0]['parent_id'];
					$_listId[$k]['rights']				= $info[0]['rights'];
					$_listId[$k]['cycle']				= $info[0]['cycle'];
					$_listId[$k]['trans_mode']		    = $info[0]['trans_mode'];
					$_listId[$k]['comm_prop']		    = $info[0]['comm_prop'];
					$_listId[$k]['rights_diff']			= $info[0]['rights'] - $self_rights;
					
					if((int)$info[0]['parent_id'] != 0) {
						self::_exte_get_all_sub_listId($info[0]['parent_id'], $k + 1, $info[0]['rights']);
					}
				}
				
				$rala = $_listId;
				unset($GLOBALS['_listId']);
				return $rala;
			} else {
				return array();
			}
		}
	}
