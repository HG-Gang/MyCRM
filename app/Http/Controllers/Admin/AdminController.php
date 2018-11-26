<?php
	
	namespace App\Http\Controllers\admin;
	
	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\Http\Controllers\Controller;
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	use App\Model\Admin;
	use App\Model\Agents;
	use App\Model\User;
	use App\Model\Mt4Trades;
	use App\Model\DepositRecordLog;
	use App\Model\DrawRecordLog;
	use App\Model\DataList;
	
	class AdminController extends Abstract_Mt4service_Controller
	{
		
		/**
		 * Display a listing of the resource.
		 * 显示导航栏
		 * @return \Illuminate\Http\Response
		 */
		public function index()
		{
			$admin_menu = config('admin_menu');
			$permissions = session('permissions');
			//dd($admin_menu);
			$id = session('id');
			$admin = Admin::find($id);
			if (RoleName($admin->role_id) == "超级管理员" && empty($permissions)) {
				$p = 1;
			} else {
				$p = 0;
			}
			
			return view('admin.index', ['admin' => $admin, 'p' => $p, 'admin_menu' => $admin_menu, 'permissions' => $permissions]);
		}
		
		/**
		 * Show the form for creating a new resource.
		 * 显示内容首页
		 * @return \Illuminate\Http\Response
		 */
		public function create()
		{
			/*$result = array();
			for($i = 0; $i <= 6 ;$i++){
				$result[] = strftime('%e',strtotime("-$i day"));
			}*/
			
			//分别计算出7天内出入金情况
			for ($i = 0; $i <= 6; $i++) {
				$date[$i] = strftime('%Y-%m-%d',strtotime("-$i day"));
				$days[$i] = strftime('%e',strtotime("-$i day"));
				$deposit[$i]['yuerjCZ'] = DepositRecordLog::selectRaw("
					sum( case when deposit_record_log.dep_status = '02' and deposit_record_log.voided = '02' then deposit_record_log.dep_act_amount else 0 end ) as yuerjCZ
				")->leftjoin('mt4_trades',function ($leftjoin) {
						$leftjoin->on('deposit_record_log.dep_mt4_id', ' = ', 'mt4_trades.TICKET');
					})->where('mt4_trades.OPEN_PRICE', 0)
					->where(function ($query) use ($date, $i) {
						$query->whereBetween('deposit_record_log.rec_upd_date', [$date[$i] . ' 00:00:00', $date[$i] . ' 23:59:59']);
					})->get()->toArray();
				
				$deposit[$i]['yuecjQK'] = DrawRecordLog::selectRaw("
					sum( case when mt4_trades.PROFIT < 0 and mt4_trades.CMD = 6 and mt4_trades.COMMENT LIKE '%-QK' then mt4_trades.PROFIT else 0 end ) as yuecjQK
				")->leftjoin('mt4_trades',function ($leftjoin) {
						$leftjoin->on('draw_record_log.mt4_trades_no', ' = ', 'mt4_trades.TICKET');
				})->where('mt4_trades.OPEN_PRICE', 0)
					->where(function ($query) use ($date, $i) {
					$query->whereBetween('draw_record_log.rec_crt_date', [$date[$i] . ' 00:00:00', $date[$i] . ' 23:59:59']);
				})->get()->toArray();
				
				//七日交易手数数据 持、平仓单 笔数 / 单量
				$deposit[$i]['open']['count'] = Mt4Trades::selectRaw("count(*) as open_count")
					->whereIn('mt4_trades.CMD', array(0, 1, 2, 3, 4, 5))
					->where('mt4_trades.CLOSE_TIME', '1970-01-01 00:00:00')
					->where('mt4_trades.CONV_RATE1', '<>', 0)->where(function ($query) use ($date, $i) {
						$query->whereBetween('mt4_trades.OPEN_TIME', [$date[$i] . ' 00:00:00', $date[$i] . ' 23:59:59']);
					})->where(function ($subWhere) {
						$this->_exte_set_search_condition($subWhere, array('user_id' => $this->_agentsIdIndex));
					})->count();
				
				$deposit[$i]['open']['volume'] = Mt4Trades::selectRaw("
					sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME = '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as open_volume
				")->where(function ($query) use ($date, $i) {
						$query->whereBetween('mt4_trades.OPEN_TIME', [$date[$i] . ' 00:00:00', $date[$i] . ' 23:59:59']);
					})->where(function ($subWhere) {
						$this->_exte_set_search_condition($subWhere, array('user_id' => $this->_agentsIdIndex));
					})->get()->toArray();
				
				$deposit[$i]['close']['count'] = Mt4Trades::selectRaw("count(*) as close_count")
					->whereIn('mt4_trades.CMD', array (0,1,2,3,4,5))
					->where('mt4_trades.CLOSE_TIME', '>', '1970-01-01 00:00:00')
					->where('mt4_trades.CONV_RATE1', '<>', 0)
					->where(function ($query) use ($date, $i) {
						$query->whereBetween('mt4_trades.CLOSE_TIME', [$date[$i] . ' 00:00:00', $date[$i] . ' 23:59:59']);
					})->where(function ($subWhere) {
						$this->_exte_set_search_condition($subWhere, array('user_id' => $this->_agentsIdIndex));
					})->count();
				
				$deposit[$i]['close']['volume'] = Mt4Trades::selectRaw("
					sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as close_volume
				")->where(function ($query) use ($date, $i) {
					$query->whereBetween('mt4_trades.CLOSE_TIME', [$date[$i] . ' 00:00:00', $date[$i] . ' 23:59:59']);
				})->where(function ($subWhere) {
					$this->_exte_set_search_condition($subWhere, array('user_id' => $this->_agentsIdIndex));
				})->get()->toArray();
				
				//七日内的返佣
				$deposit[$i]['fanYong'] = Mt4Trades::selectRaw ("
					sum( case when mt4_trades.CMD = 6 AND mt4_trades.COMMENT LIKE '%-FY' THEN mt4_trades.PROFIT ELSE 0 END ) as todayRebateTotal
				")->where(function ($query) use ($date, $i) {
					$query->whereBetween('mt4_trades.MODIFY_TIME', [$date[$i] . ' 00:00:00', $date[$i] . ' 23:59:59']);
				})->get()->toArray();
				
				$deposit[$i] =  array(
					'yuerjCZ'       => $deposit[$i]['yuerjCZ'][0]['yuerjCZ'],
					'yuecjQK'       => $deposit[$i]['yuecjQK'][0]['yuecjQK'],
					'close_volume'  => $deposit[$i]['close']['volume'][0]['close_volume'] / 100,
					'close_count'   => $deposit[$i]['close']['count'],
					'open_volume'   => $deposit[$i]['open']['volume'][0]['open_volume'] / 100,
					'open_count'    => $deposit[$i]['open']['count'],
					'fanYong'       => number_format($deposit[$i]['fanYong'][0]['todayRebateTotal'] / 10000, '2', '.', ''),
				);
			}
			
			//整理数据格式
			foreach ($deposit as $k2 => $v2) {
				if(empty($v2['yuerjCZ']) || $v2['yuerjCZ'] == 0) {
					$_rs[$k2]['yuerjCZ'] = 0.00;
				} else {
					$_rs[$k2]['yuerjCZ'] = number_format(($v2['yuerjCZ'] / 10000),  '2', '.', '');
				}
				
				if(empty($v2['yuecjQK'])) {
					$_rs[$k2]['yuecjQK'] = 0.00;
				} else {
					$_rs[$k2]['yuecjQK'] = abs(number_format((abs($v2['yuecjQK']) / 10000), '2', '.', ''));
				}
				
				$_rs[$k2]['close_volume'] = $v2['close_volume'];
				$_rs[$k2]['close_count'] = $v2['close_count'];
				$_rs[$k2]['open_volume'] = $v2['open_volume'];
				$_rs[$k2]['open_count'] = $v2['open_count'];
				$_rs[$k2]['fanYong']    = $v2['fanYong'];
			}
			
			//查找代理商，普通客户总数，总共有效注册人数，共认证人数，待审核人数
			$agentsTotal    = Agents::whereIn('voided', array ('1', '2'))->whereIn('user_status', array('0', '1', '2', '4'))->count();
			$agentsAuth    = Agents::where('voided', '1')->where('user_status', '1')->where('IDcard_status', '2')->where('bank_status', '2')->count();
			$userTotal      = User::whereIn('voided', array ('1', '2'))->whereIn('user_status', array('0', '1', '2', '4'))->count();
			$userAuth    = User::where('voided', '1')->where('user_status', '1')->where('IDcard_status', '2')->where('bank_status', '2')->count();
			$allTotal       = DataList::whereIn('voided', array ('1', '2'))->whereIn('user_status', array('0', '1', '2', '4'))->count();
			$authTotal      = DataList::where('voided', '1')->where('user_status', '1')->where('IDcard_status', '2')->where('bank_status', '2')->count();
			$pendingTotal   = DataList::where(function ($where) {
				$where->where('data_list.IDcard_status', '1')->whereNotIn('data_list.IDcard_status', array('0', '2', '4'))
					->Orwhere(function ($where) {
						$where->where('data_list.bank_status', '1')->whereNotIn('data_list.bank_status', array('0', '2', '4'));
					});
			})->whereIn('data_list.user_status', array('0', '1', '2', '4'))->count();
			
			//累计出入金总数
			$depositTotal = DepositRecordLog::selectRaw("
					sum( case when deposit_record_log.dep_status = '02' and deposit_record_log.voided = '02' then deposit_record_log.dep_act_amount else 0 end ) as depositTotal
				")->leftjoin('mt4_trades',function ($leftjoin) {
				$leftjoin->on('deposit_record_log.dep_mt4_id', ' = ', 'mt4_trades.TICKET');
			})->where('mt4_trades.OPEN_PRICE', 0)->get()->toArray();
			
			$withdrawTotal = DrawRecordLog::selectRaw("
					sum( case when mt4_trades.PROFIT < 0 and mt4_trades.CMD = 6 and mt4_trades.COMMENT LIKE '%-QK' then mt4_trades.PROFIT else 0 end ) as withdrawTotal
				")->leftjoin('mt4_trades',function ($leftjoin) {
				$leftjoin->on('draw_record_log.mt4_trades_no', ' = ', 'mt4_trades.TICKET');
			})->where('mt4_trades.OPEN_PRICE', 0)->get()->toArray();
			
			//累计返佣
			$rebateTotal = Mt4Trades::selectRaw ("
					sum( case when mt4_trades.CMD = 6 AND mt4_trades.COMMENT LIKE '%-FY' THEN mt4_trades.PROFIT ELSE 0 END ) as rebateTotal
				")->where(function ($subWhere) {
					$this->_exte_set_search_condition($subWhere, array('user_id' => $this->_agentsIdIndex));
				})->get()->toArray();
			
			//累计持/平仓单
			$volTotal = Mt4Trades::selectRaw("
					sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME > '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as closeVolTotal,
					sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5 ) and mt4_trades.CLOSE_TIME = '1970-01-01 00:00:00' and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as openVolTotal
				")->where(function ($subWhere) {
					$this->_exte_set_search_condition($subWhere, array('user_id' => $this->_agentsIdIndex));
				})->get()->toArray();
			
			$data = array(
				'agentsTotal'           => $agentsTotal,
				'agentsAuth'            => $agentsAuth,
				'userTotal'             => $userTotal,
				'userAuth'              => $userAuth,
				'allTotal'              => $allTotal,
				'authTotal'             => $authTotal,
				'pendingTotal'          => $pendingTotal,
				'todayDepWithdraw'      => array('todaydep' => $_rs[0]['yuerjCZ'], 'todaywithdraw' => $_rs[0]['yuecjQK']),
				'todayVol'              => array('closeVol' => $_rs[0]['close_volume'], 'closeCount' => $_rs[0]['close_count'], 'openVol' => $_rs[0]['open_volume'], 'openCount' => $_rs[0]['open_count']),
				'ytdayVol'              => array('closeVol' => $_rs[1]['close_volume'], 'openVol' => $_rs[1]['open_volume']),
				'volTotal'              => array('closeVolTotal' => $volTotal[0]['closeVolTotal'] / 100, 'openVolTotal' => $volTotal[0]['openVolTotal'] / 100),
				'depositTotal'          => number_format($depositTotal[0]['depositTotal'] / 10000, '2', '.', ''),
				'withdrawTotal'         => number_format((abs($withdrawTotal[0]['withdrawTotal']) / 10000), '2', '.', ''),
				'rebateTotal'           => number_format($rebateTotal[0]['rebateTotal'] / 10000, '2', '.', ''),
				'todayFanYong'          => $_rs[0]['fanYong'],
			);
			//dd($data);
			krsort($_rs);
			//dd($_rs);
			return view('admin.home')->with([
				'amount'        => $_rs,
				'days'          => $days,
				'data'          => $data,
				'user'          => $this->_auser,
				'role'          => RoleName($this->_auser->role_id),
				'loginIP'       => $this->_exte_get_user_loginIp(),
			]);
		}
		
		/**
		 * Store a newly created resource in storage.
		 * 个人资料
		 * @param  \Illuminate\Http\Request $request
		 * @return \Illuminate\Http\Response
		 */
		public function UserInfo(Request $request)
		{
			$cookie = session('id');
			$admin = Admin::find($cookie);
			return view('admin.administrators.userinfo', [
				'admin' => $admin
			]);
		}
		
		/**
		 * Display the specified resource.
		 * 保存修改个人资料
		 * @param  int $id
		 * @return \Illuminate\Http\Response
		 */
		public function UserIfoSave(Request $request)
		{
			$monile = $request->mobile;
			$email = $request->email;
			$id = session('id');
			//验证手机号
			if (!$this->isMobile($monile)) {
				return json_encode(['msg' => '手机号不合法', 'state' => 0]);
			}
			//验证邮箱
			if (!$this->isEmail($email)) {
				return json_encode(['msg' => '邮箱格式不正确', 'state' => 0]);
			}
			$admin = Admin::find($id);
			$admin->mobile = $monile;
			$admin->email = $email;
			if ($admin->save()) {
				return json_encode(['msg' => '成功', 'state' => 1]);
			} else {
				return json_encode(['msg' => '失败', 'state' => 0]);
			}
		}
		
		/**
		 * Show the form for editing the specified resource.
		 * 修改个人密码
		 * @param  int $id
		 * @return \Illuminate\Http\Response
		 */
		public function UserPwd()
		{
			$cookie = session('id');
			$admin = Admin::find($cookie);
			return view('admin.administrators.userpwd', [
				'admin' => $admin
			]);
		}
		
		/**
		 * Update the specified resource in storage.
		 * 保存修改密码
		 * @param  \Illuminate\Http\Request $request
		 * @param  int $id
		 * @return \Illuminate\Http\Response
		 */
		public function UserPewdSave(Request $request)
		{
			$pwd = $request->pwd;
			$npwd = $request->npwd;
			$id = session('id');
			$admin = Admin::find($id);
			//判断旧密码是否正确
			if (!password_verify($pwd, $admin->password)) {
				return json_encode(['msg' => '旧密码错误', 'state' => 0]);
			}
			$admin->password = bcrypt($npwd);
			if ($admin->save()) {
				return json_encode(['msg' => '成功', 'state' => 1]);
			} else {
				return json_encode(['msg' => '失败', 'state' => 0]);
			}
			
			
		}
		
		/**
		 * Remove the specified resource from storage.
		 *
		 * @param  int $id
		 * @return \Illuminate\Http\Response
		 */
		public function destroy($id)
		{
			//
		}
		
		/**
		 * 检查是否手机号
		 * @param string $mobile 手机号
		 * @return boolean
		 */
		protected function isMobile($mobile)
		{
			//大陆手机号
			if (preg_match("/^1[34578]{1}\d{9}$/", $mobile))
				return true;
			else
				return false;
		}
		
		/**
		 * 检查是否邮箱格式是否正确
		 * @param string $mobile 手机号
		 * @return boolean
		 */
		protected function isEmail($email)
		{
			if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/", $email))
				return true;
			else
				return false;
		}
		
		protected function _exte_set_search_condition($subWhere, $data)
		{
			$subWhere->whereNotIn('mt4_trades.LOGIN',function ($subWhere2) use($data) {
				$subWhere2->selectRaw("
								/*普通客户*/
								user.user_id from user where parent_id in (
									select agents.user_id  from agents where agents.parent_id in (
										select agents.user_id  from agents where agents.parent_id in (
											select agents.user_id  from agents where agents.parent_id = " . intval ($data['user_id']) . " and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($data['user_id']) . "
										) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($data['user_id']) . "
									) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($data['user_id']) . "
								)
								/*代理商*/
								UNION
								select agents.user_id  from agents where agents.parent_id in (
									select agents.user_id  from agents where agents.parent_id in (
										select agents.user_id  from agents where agents.parent_id = " . intval ($data['user_id']) . " and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($data['user_id']) . "
									) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($data['user_id']) . "
								) and agents.voided = '1' and agents.user_status in ('0','1','2','4') or agents.user_id = " . intval ($data['user_id']) . "
							");
			});
			
			return $subWhere;
		}
		
	}
