<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Requests;
//use App\Http\Controllers\Controller;
use App\Model\Agents;
use App\Model\Mt4Users;
use App\Model\Mt4Trades;
use App\Model\UserGroup;
use App\Model\User;
use DB;
use App\Model\Admin;
use App\Model\AgentsGroup;
use App\Model\OperationLog;
use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;

class AgentController extends Abstract_Mt4service_Controller {

	protected $_str_rala            = '';
	
    /**
     * Display a listing of the resource.
     * 首页
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $group_id = '';  //默认为一级代理
        $user_id = !empty($request->user_id) ? trim($request->user_id) : ''; //查找直属代理商条件
        //查询
        $form = !empty($request->form) ? $request->form : ''; //判断是否是表单提交查询
        $user_name = !empty($request->user_name) ? trim($request->user_name) : ''; //交易账户名查询
        $startdate = !empty($request->startdate) ? trim($request->startdate) : ''; //交易开户时间开始
        $enddate = !empty($request->enddate) ? trim($request->enddate) : ''; //交易开户时间结束
        $user_status = !empty($request->user_status) ? trim($request->user_status) : '2'; //是否认证
        $trans_mode = !empty($request->trans_mode) ? trim($request->trans_mode) : '2'; //账户模式
        $list = Agents::from('agents as A')->leftjoin('mt4_users as B', 'A.user_id', '=', 'B.LOGIN')->whereIn('A.voided', ['1', '2'])->whereIn('A.user_status', array('0', '1', '2', '4'))->orderBy('B.REGDATE', 'desc')->select('A.user_id', 'A.user_name', 'A.user_money', 'A.user_status', 'A.group_id', 'A.parent_id', 'A.mt4_grp', 'A.trans_mode', 'A.rights', 'A.is_confirm_agents_lvg', 'B.BALANCE', 'B.EQUITY', 'B.REGDATE');
        $date = array();
        //判断是否是表单提交查询
            
        if (!empty($form)) {
            if (!empty($user_id)) {  //按交易账号查询
                $list = $list->where('A.user_id', $user_id);
            }
            if (!empty($user_name)) {//按交易账户姓名查询
                $list = $list->where('A.user_name', $user_name);
            }
            if (!empty($startdate)) {//开始时间
                $list = $list->where('B.REGDATE', '>', $startdate);
            }
            if (!empty($enddate)) {//开始时间
                $list = $list->where('B.REGDATE', '<', $enddate);
            }
            if ($user_status == 1 || $user_status == 2) {//是否认证
                if ($user_status == 2)
                    $user_status = 0;
                $list = $list->where('A.user_status', $user_status);
            }
            if ($trans_mode == 1 || $trans_mode == 2) {//账户模式
                if ($trans_mode == 2)
                    $trans_mode = 0;
                $list = $list->where('A.trans_mode', $trans_mode);
            }
            $list = $list->get()->toArray();
        } else {
            //不是表单提交查询
            if (empty($user_id)) {
                $group_id = 1;  //默认为一级代理
                $list = $list->where('A.group_id', $group_id)->get()->toArray(); //第一次进入页面 显示所有一级用户
            } else {
                $list = $list->where('A.parent_id', $user_id)->get()->toArray(); //按条件查询到直属代理商
                $user = Agents::select('user_id', 'user_name', 'group_id', 'parent_id')->find($user_id); //查找当前位置
                for ($i = 0; $i < $user->group_id; $i++) {
                    $user_1 = Agents::where('user_id', $user->parent_id)->select('user_id', 'user_name', 'group_id', 'parent_id')->first(); //查找上一级位置
                    $date[$i] = $user_1;
                }
                $date[$user->group_id - 1] = $user;
            }
        }
        $mun_s = count($list);
        //统计直属代理商数量
        foreach ($list as $key => $v) {
            $mun = Agents::where('parent_id', $v['user_id'])->whereIn('user_status', array('0', '1', '2', '4'))->whereIn('voided', ['1', '2'])->count();
            $list[$key]['mun'] = $mun;
        }
        //统计直客户数量
        foreach ($list as $key => $v) {
            $user_mun = User::where('parent_id', $v['user_id'])->whereIn('user_status', array('0', '1', '2', '4'))->whereIn('voided', ['1', '2'])->count();
            $list[$key]['user_mun'] = $user_mun;
        }
        //统计返佣 入金 出金 ；
        foreach ($list as $key => $v) {
            $money = DB::select('select sum(case when mt4_trades.CMD = 6 and mt4_trades.COMMENT LIKE "%-FY" then mt4_trades.PROFIT else 0 end) as total_fy,sum(case when mt4_trades.PROFIT > 0 and mt4_trades.CMD = 6 and mt4_trades.COMMENT NOT LIKE "%-FY" and mt4_trades.COMMENT NOT LIKE "%Adj%" then mt4_trades.PROFIT else 0 end) as total_rj,sum(case when mt4_trades.PROFIT < 0 and mt4_trades.CMD = 6 then mt4_trades.PROFIT else 0 end) as total_qk from `mt4_trades` where `MT4_TRADES`.`LOGIN` =' . $v['user_id']);
            $list[$key]['money'] = $money;
        }
        //计算返佣总和 出金总和 入金总和 余额总和 净值总和
        $total_balance = 0; //余额总和
        $total_net_value = 0; //净值总和
        $total_fy_zong = 0; //返佣总和
        $total_rj_zong = 0; //入金总和
        $total_qk_zong = 0; //出金总和
        foreach ($list as $key => $v) {
            $total_balance+=$v['BALANCE'];
            $total_net_value+=$v['EQUITY'];
            $total_fy_zong+=$v['money']['0']->total_fy;
            $total_rj_zong+=$v['money']['0']->total_rj;
            $total_qk_zong+=$v['money']['0']->total_qk;
        }
        //保留2位小数点
        $total_fy_zong = sprintf('%.2f', $total_fy_zong);
        $total_rj_zong = sprintf('%.2f', $total_rj_zong);
        $total_qk_zong = sprintf('%.2f', $total_qk_zong);
        
        
       //判断当前用户角色
        $state=$this->Role();
        return view('admin.agent.list', [
            'list' => $list,
            'mun_s' => $mun_s,
            'total_fy_zong' => $total_fy_zong,
            'total_rj_zong' => $total_rj_zong,
            'total_qk_zong' => $total_qk_zong,
            'total_net_value' => $total_net_value,
            'total_balance' => $total_balance,
            'group_id' => $group_id,
            'date' => $date,
            'request' => $request,
           'state'=>$state
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * 查看直属客户
     * @return \Illuminate\Http\Response
     */
    public function CustomerList($id) {
        $user = User::from("user as A")->leftjoin('mt4_users as B', 'A.user_id', '=', 'B.LOGIN')->whereIn('A.voided', ['1', '2'])->whereIn('A.user_status', array('0', '1', '2', '4'))->where('A.parent_id', $id)->select('A.user_id', 'A.user_name', 'A.mt4_grp', 'B.BALANCE', 'B.EQUITY', 'B.REGDATE')->orderBy('B.REGDATE', 'desc')->get();
        //统计 入金 出金 ；
        foreach ($user as $key => $v) {
            $money = DB::select('select sum(case when mt4_trades.PROFIT > 0 and mt4_trades.CMD = 6 and mt4_trades.COMMENT NOT LIKE "%-FY" and mt4_trades.COMMENT NOT LIKE "%Adj%"  then mt4_trades.PROFIT else 0 end) as total_rj,sum(case when mt4_trades.PROFIT < 0 and mt4_trades.CMD = 6 then mt4_trades.PROFIT else 0 end) as total_qk from `mt4_trades` where `MT4_TRADES`.`LOGIN` =' . $v->user_id);
            $user[$key]['money'] = $money[0];
        }

        //手续费 盈亏 贵金属 外汇 原油 指数 利息 总交易量

        foreach ($user as $key => $v) {
            $money = DB::select('select '
                            //手续费
                            . 'sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.COMMISSION else 0 end ) as all_total_comm,'         //盈亏
                            . 'sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.PROFIT else 0 end ) as all_total_profit,'          //贵金属
                            . 'sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 1 and voided = "1" ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as all_total_noble_metal,'
                            //外汇
                            . 'sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 2 and voided = "1" ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as all_total_for_exca,'
                            //原油
                            . 'sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 3 and voided = "1" ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as all_total_crud_oil,'
                            //指数
                            . 'sum( case when mt4_trades.SYMBOL in ( select sym_symbol from symbol_prices where sym_grp_id = 4 and voided = "1" ) and mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.CLOSE_TIME > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as all_total_index,'
                            //利息
                            . 'sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 then mt4_trades.VOLUME else 0 end ) as all_total_volume,'          //总交易量
                            . 'sum( case when mt4_trades.CMD in (0, 1, 2, 3, 4, 5) and mt4_trades.close_time > "1970-01-01 00:00:00" and mt4_trades.CONV_RATE1 <> 0 and mt4_trades.SWAPS < 0 then mt4_trades.SWAPS else 0 end ) as all_total_swaps '
                            . 'from `mt4_trades` where `MT4_TRADES`.`LOGIN` =' . $v->user_id);
            $user[$key]['category'] = $money[0];
        }
        //   dd($user);
        //计算返佣总和 出金总和 入金总和 余额总和 净值总和
        $total_balance = 0; //余额总和
        $total_net_value = 0; //净值总和
        $total_rj_zong = 0; //入金总和
        $total_qk_zong = 0; //出金总和
        $all_total_comm_zong = 0; //手续费总和
        $all_total_profit_zong = 0; //盈亏总和
        $all_total_noble_metal_zong = 0; //贵金属总和
        $all_total_for_exca_zong = 0; //外汇总和
        $all_total_crud_oil_zong = 0; //原油总和
        $all_total_index_zong = 0; //指数总和
        $all_total_volume_zong = 0; //利息总和
        $all_total_swaps_zong = 0; //总交易量总和
        //计算各总和
        foreach ($user as $v) {
            $total_balance+=$v->BALANCE;
            $total_net_value+=$v->EQUITY;
            $total_rj_zong+=$v->money->total_rj;
            $total_qk_zong+=$v->money->total_qk;
            $all_total_comm_zong+=$v->category->all_total_comm;
            $all_total_profit_zong+=$v->category->all_total_profit;
            $all_total_noble_metal_zong+=$v->category->all_total_noble_metal;
            $all_total_for_exca_zong+=$v->category->all_total_for_exca;
            $all_total_crud_oil_zong+=$v->category->all_total_crud_oil;
            $all_total_index_zong+=$v->category->all_total_index;
            $all_total_volume_zong+=$v->category->all_total_volume;
            $all_total_swaps_zong+=$v->category->all_total_swaps;
        }
        $data = array(
            'user' => $user,
            'total_balance' => sprintf("%.2f", $total_balance),
            'total_net_value' => sprintf("%.2f", $total_net_value),
            'total_rj_zong' => sprintf("%.2f", $total_rj_zong),
            'total_qk_zong' => sprintf("%.2f", $total_qk_zong),
            'all_total_comm_zong' => sprintf("%.2f", $all_total_comm_zong),
            'all_total_profit_zong' => sprintf("%.2f", $all_total_profit_zong),
            'all_total_noble_metal_zong' => sprintf("%.2f", $all_total_noble_metal_zong),
            'all_total_for_exca_zong' => sprintf("%.2f", $all_total_for_exca_zong),
            'all_total_crud_oil_zong' => sprintf("%.2f", $all_total_crud_oil_zong),
            'all_total_index_zong' => sprintf("%.2f", $all_total_index_zong),
            'all_total_volume_zong' => sprintf("%.2f", $all_total_volume_zong),
            'all_total_swaps_zong' => sprintf("%.2f", $all_total_swaps_zong),
        );
        return view('admin.agent.customer', $data);
    }

    /**
     * Store a newly created resource in storage.
     * 显示编辑代理商信息页面
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function AgentEdir($id) {
        $group = UserGroup::get();
        $user = Agents::find($id);
        $state=$this->Role();
        $agentsgroup=  AgentsGrup::where('voided',1)->get()->toArray();
        //  dd($user);
        return view('admin.agent.agent-edit', [
            'group' => $group,
            'user' => $user,
            'state'=>$state,
            'agentsgroup'=>$agentsgroup,
        ]);
    }

    



    /**
     * Display the specified resource.
     * 保存更新代理商信息
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function AgentUpdate(Request $request) {
       //获取前台提交数据
        $data=$request->re;
       //获取上级ID号
        $parent_id=$data['parent_id'];
      //获取当前代理商上级
       Agents::where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))->first();
        
        
        
        
        
        
        

        print_r($data);
        die;
        
        
        
        
        
        
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

//    protected function _exte_sync_mt4_update_user_info ($user_id, $col_ary, $act='update_user')
//		{
//			$query = $this->_exte_mt4_same ($act) . '&acc=' . $user_id. '&idn=' . $col_ary['idcar_no'];
//			return $this->_exte_mt4_query_request($query);
//		}


	public function agents_list_browse ()
	{
		return view('admin.agent.agents_list_browse')->with(['role' => $this->Role()]);
	}
	
	//编辑代理商信息视图
	public function agents_edit_info($uid)
	{
		//得到当前编辑的代理商信息
		
		$ag_info = $this->_exte_get_user_info($uid);
		
		$ag_lvl = UserGroup::select('user_group_id', 'user_group_name')->where('voided', 1)->get()->toArray();
		
		//通过当前代理商parent_id 得到当前代理商的组别情况，这块主要得出当前代理商的下拉组别可以选择哪些
		if ($ag_info['parent_id'] != 0) {
			//得到当前代理商的上级代理信息
			$p_ag_info = Agents::where('user_id', $ag_info['parent_id'])
				->where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))->first();
			
			if ($ag_info['is_confirm_agents_lvg'] == '0') {
				//代理级别未确认
				$ag_grp = AgentsGroup::select('group_id', 'group_name')->where(function ($query) use($ag_info, $p_ag_info) {
					$query->where('group_id', '>', $p_ag_info['group_id'])->where('group_id', '<=', 4);
				})->where('voided', '1')->get()->toArray();
			} else if ($ag_info['is_confirm_agents_lvg'] == '1') {
				$ag_grp = AgentsGroup::select('group_id', 'group_name')->where(function ($query) use($ag_info, $p_ag_info) {
					$query->where('group_id', '>=', $p_ag_info['group_id'])->where('group_id', '<=', $ag_info['group_id']);
				})->where('voided', '1')->get()->toArray();
			}
		} else {
			$ag_grp = AgentsGroup::select('group_id', 'group_name')
				->where(function ($query) use($ag_info) {
					$query->where('group_id', '<=', $ag_info['group_id']);
				})->where('voided', '1')->get()->toArray();
		}
		
		// 1 超管视图，2 客服视图，3 财务视图
		return view('admin.agent.agents_edit_info_' . $this->Role())->with([
			'ag_info'       => $ag_info,
			'ag_grp'        => $ag_grp,
			'ag_lvl'        => $ag_lvl,
			'auser'         => $this->_auser,
		]);
	}
	
	//编辑保存
	public function agents_edit_save (Request $request)
	{
		$data           = $request->data;
		$userId         = $data['userId'];
		$username       = $data['username'];
		$password       = $data['password'];
		$userIdcardNo   = $data['userIdcardNo'];
		$userphoneNo    = $data['userphoneNo'];
		$useremail      = $data['useremail'];
		$usergrpId      = $data['usergrpId'];
		$usertype       = $data['usertype'];
		$userrights     = $data['userrights'];
		$usercycle      = $data['usercycle'];
		$cust_lvg       = $data['cust_lvg'];
		$userparentId   = $data['userparentId'];
		$useragtId      = $data['useragtId'];
		$userrebate     = $data['userrebate'];
		$userremark     = $data['userremark'];
		$reccrtdate     = $data['reccrtdate'];
		$usercountry    = $data['usercountry'];
		$usergrpName    = $request->usergrpName;
		$useragtName    = $request->useragtName;
		$enable         = $request->enable;
		$enablereadonly = $request->enablereadonly;
		$isoutmoney     = $request->isoutmoney;
		$settlementmodel= $request->settlementmodel;
		$datausercycle  = $request->datausercycle;
		$_modules       = '86';
		$col_ary        = array();
		$_error         = array();
		
		$curr_info      = $this->_exte_get_user_info($userId);
		
		if ($curr_info['phone'] != $_modules . '-' . $userphoneNo && $this->Role() == 1) {
			//手机有变化，检查手机唯一性
			$_tel = $this->_exte_verify_phone($userphoneNo);
			if ($_tel) {
				//手机号已存在
				return response()->json([
					'msg'        => 'FAIL',
					'err'        => 'Existphone',
					'col'        => 'userphoneNo',
				]);
			}
			//$col_ary['phone'] = $_modules . '-' . $userphoneNo;
		}
		
		if ($curr_info['IDcard_no'] != $userIdcardNo && ($this->Role() == 1 || $this->Role() == 2)) {
			//身份证有变化
			$_ido = $this->_exte_verify_idno ($userIdcardNo);
			if ($_ido) {
				//身份证已存在
				return response()->json([
					'msg'        => 'FAIL',
					'err'        => 'Existidcard',
					'col'        => 'userIdcardNo',
				]);
			}
			//$col_ary['id'] = $userIdcardNo;
		}
		
		if ($curr_info['email'] != $useremail && $this->Role() == 1) {
			//邮件有变化
			$_eml = $this->_exte_verify_email ($useremail);
			if ($_eml) {
				//邮箱已存在
				return response()->json([
					'msg'        => 'FAIL',
					'err'        => 'Existemail',
					'col'        => 'useremail',
				]);
			}
			//$col_ary['email'] = $useremail;
		}
		
		//检查当前代理商结算模式是否有变，有变化则进行相应的检查
		if ($settlementmodel != $curr_info['settlement_model'] && $curr_info['parent_id'] == 0) {
			//parent_id = 0 的代理商结算模式有变化
			if ($this->_exte_check_agents_direct_customer_open_order($userId) > 0) {
				return response()->json([
					'msg'        => 'FAIL',
					'err'        => 'directExistOrder',
					'col'        => 'settlement_model',
				]);
			}
		}
		
		//检查用户选择的结算周期值是否合法性，账户模式是权益模式（=1）才检查
		if (!in_array($datausercycle, array('1', '2', '3')) && $usertype == 1) {
			return response()->json([
				'msg'           => 'FAIL',
				'err'           => 'INVALIDCYCLE', //无效的周期结算值
				'col'           => 'usercycle',
			]);
		}
		
		////检查 用户组， 上级代理 代理级别 数据的合法性
		$all_agt = AgentsGroup::select('group_id', 'group_name', 'agents_comm_prop')->whereIn('group_id', array(1, 2, 3, 4))->where('voided', '1')->get()->toArray();
		$chk_grp = UserGroup::where('user_group_id', $usergrpId)->where('user_group_name', $usergrpName)->where('voided', '1')->first();
		if($userparentId != '0') {
			$chk_pid = Agents::where('user_id', $userparentId)->where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))->first();
		} else {
			$chk_pid['group_id'] = $userparentId; //$userparentId = 0
		}
		
		//检查添加代理商的代理商级别是否高于或等于当前邀请人的代理商级别
		if ($chk_pid['group_id'] >= $useragtId && ($this->Role() == 1 || $this->Role() == 2)) {
			//添加的代理商级别大于等于邀请人代理商级别
			return response()->json([
				'msg'        => 'FAIL',
				'err'        => 'ThanAndEqualInviter',
				'col'        => 'useragtId',
			]);
		}
		
		$chk_agt = AgentsGroup::where('group_id', $useragtId)->where('group_name', $useragtName)->where('voided', '1')->first();
		
		//TODO 获取当前此人最大最小可调返佣比例, 如果pid=0，则无限制大小
		$_max_comm_prop = Agents::selectRaw('max(comm_prop) as max_comm_prop')->where('user_id', $userparentId)->where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))->first();
		$_min_comm_prop = Agents::selectRaw('max(comm_prop) as min_comm_prop')->where('parent_id', $userId)->where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))->first();
		
		if($userparentId == 0) {
			$_max_comm_prop['max_comm_prop'] = $all_agt[0]['agents_comm_prop'];
		}
		if ($curr_info['group_id'] == 4) {
			$_min_comm_prop['min_comm_prop'] = 0;
		}
		if($_max_comm_prop['max_comm_prop'] == null) {
			$_min_comm_prop['min_comm_prop'] = 0;
		}
		if ($_min_comm_prop['min_comm_prop'] == null) {
			//此时当前代理商没有下级代理商，但有可能有客户
			$_min_comm_prop['min_comm_prop'] = 0;
		}
		
		if ($chk_grp == null) {
			$_error['grp'] = 'err_grp';
		} elseif ($chk_pid == null || $userparentId == $userId || $chk_pid['group_id'] > $useragtId) {
			$_error['pid']	= 'err_pid';
		} elseif ($chk_agt == null) {
			$_error['gid']	= 'err_gid';
		} else if ((int)$userrebate > (int)$_max_comm_prop['max_comm_prop'] || (int)$userrebate < (int)$_min_comm_prop['min_comm_prop']) {
			$_error['comm_prop']	= 'err_comm_prop';
			$_error['max']          = $_max_comm_prop['max_comm_prop']; // 80
			$_error['min']	        = $_min_comm_prop['min_comm_prop']; // 0
		}
		
		//此时已经确定当前代理商没有下级代理商
		if (is_array($_error) && isset($_error['min']) && $_error['min'] == 0) {
			//查找当前代理商是否已经存在客户,有客户的代理商返佣比例不能低于50
			$is_client = User::where('parent_id', $userId)->where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))->first();
			if ($is_client != null) {
				return response()->json([
					'msg'       => 'FAIL',
					'err'       => $_error['act'] = 'EXISTACCOUNT',
					'col'       => 'data_vali_err',
				]);
			}
		}
		
		if(!empty($_error)) {
			return response()->json([
				'msg'           => 'FAIL',
				'err'           => $_error,
				'col'           => 'data_vali_err',
			]);
		}
		
		//判定用户组是否更改
		if ($usergrpName != $curr_info['mt4_grp'] && ($this->Role() == 1 || $this->Role() == 2)) {
			//组别已被改变, 检查当前userId是否还有持仓单
			$is_orderNO = Mt4Trades::where('LOGIN', $userId)->where('CLOSE_TIME', '1970-01-01 00:00:00')->whereIn('CMD', array(0, 1, 2, 3, 4, 5))->count();
			if ($is_orderNO > 0) {
				return response()->json([
					'msg'       => 'FAIL',
					'err'       => 'ACCOUNTEXISTORDER',
					'col'       => 'usergrpId', //存在订单，无法更改用户组
				]);
			} else {
				$col_ary['group'] = $usergrpName;
			}
		}
		
		//判定账户是否只读
		if ($curr_info['enable_readonly'] != $enablereadonly) {
			//账户只读状态已经改变
			$col_ary['enable_read_only'] = $enablereadonly;
		}
		
		//判定账户启用状态 1 启用, 0 禁用
		if ($curr_info['enable'] != $enable && ($this->Role() == 1 || $this->Role() == 2)) {
			//账户启用状态已经改变, 同步MT4更新状态
			$enable_col_ary['login'] = $userId;
			$enable_col_ary['enable'] = $enable;
			$mt4_en = $this->_exte_mt4_update_user($enable_col_ary);
			/*if ($enable == 1) {
				//启用
				$mt4_en = $this->_exte_mt4_active_enable_user($userId);
			} else if ($enable == 0) {
				//禁用
				$mt4_en = $this->_exte_mt4_active_disable_user($userId);
			}*/
			
			if (is_array($mt4_en) && $mt4_en['ret'] != 0) {
				return response()->json([
					'msg'       => 'FAIL',
					'err'       => 'ENABLEUPDFAIL', //更新账户状态失败
					'col'       => 'enable',
				]);
			} else if (!is_array($mt4_en)) {
				return response()->json([
					'msg'       => 'FAIL',
					'err'       => 'NETERR', //网络故障,无法更新账户状态
					'col'       => 'enable',
				]);
			}
		}
		
		//检查密码是否被更改
		if ($password != '********' && base64_decode($curr_info['password']) != $password && $this->Role() == 1) {
			//已经被修改, 同步MT4修改密码
			$mt4 = $this->_exte_mt4_reset_user_pwd($userId, $password);
			if (is_array($mt4) && $mt4['ret'] == '0') {
				//更改成功，短信通知
				//$_rs = $this->_exte_send_phone_notify($userphoneNo, 'resetPassword',array('password' => $password));
			} else {
				return response()->json([
					'msg'       => 'FAIL',
					'err'       => 'PSWUPDFAIL',
					'col'       => 'password', //密码更新失败
				]);
			}
		}
		
		//同步MT4更新相关列
		//$col_ary['leverage'] = $cust_lvg;
		$col_ary['login'] = $userId;
		if ($this->Role() == 1) {
			$col_ary['name'] = $this->_exte_mt4_username_convert_encode($username);
		}
		$mt4_upd = $this->_exte_mt4_update_user($col_ary);
		
		if (is_array($mt4_upd) && $mt4_upd['ret'] != '0') {
			return response()->json([
				'msg'       => 'FAIL',
				'err'       => 'MT4OHTERUPDFAIL', //同步MT4更新其他列失败
				'col'       => 'userphoneNo',
			]);
		} else if (!is_array($mt4_upd)) {
			return response()->json([
				'msg'       => 'FAIL',
				'err'       => 'NETERRUPDFAIL', //网络故障,无法更新账户状态
				'col'       => 'MT4OHTER',
			]);
		}
		
		//如果上面都执行正确，开始更新本地表数据
		$upd_num = Agents::where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))->find($userId)->update([
			'user_name'             => ($this->Role() == 1) ? $username : $curr_info['user_name'],
			'password'              => ($password != '********' && $this->Role() == 1) ? base64_encode($password) : $curr_info['password'],
			'email'                 => ($this->Role() == 1) ? $useremail : $curr_info['email'],
			'phone'                 => ($this->Role() == 1) ? $_modules . '-' . $userphoneNo : $curr_info['phone'],
			'IDcard_no'			    => $userIdcardNo,
			'comm_prop'             => $userrebate,
			'mt4_grp'               => $usergrpName, //用户组
			'parent_id'             => $userparentId, //上级代理
			'cust_lvg'              => $cust_lvg, //杠杆
			'group_id'              => $useragtId,
			'is_confirm_agents_lvg' => '1',
			//'country'               => $col_ary['country'],
			'enable'                => $enable,
			'enable_readonly'       => $enablereadonly,
			'is_out_money'          => $isoutmoney, //是否允许出金
			'trans_mode'            => $usertype, //账户类型
			'settlement_model'      => $settlementmodel,
			'cycle'                 => ($usertype == 1) ? $datausercycle : 0, //结算周期
			'rights'                => ($usertype == 1) ? $userrights : 0, //权益值
			'remark'                => $userremark,
			'rec_upd_user'          => $this->_auser['username'],
			'rec_upd_date'          => date('Y-m-d H:i:s'),
		]);
		
		//更新客户层级关系
		$mt4_upd_country = $this->_exte_mt4_update_user2 ($userId);
		$upd_country = Agents::where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))->find($userId)->update([
			'country'           => $this->_str_rala,
		]);
		
		if ($upd_num) {
			//TODO 更新了某个人的记录信息
			$loginIp = $this->_exte_get_user_loginIp();
			$crt_log = OperationLog::create([
				'name'                  => $this->_auser['username'],
				'user_id'               => $userId,
				'order_number'          => 0,
				'content'               => '[' . $this->_auser['username'] . '] ' . ' 成功修改了 ' . $curr_info['user_name'] . ' [' . $curr_info['user_id'] . '] ' . '账户信息',
				'handle_ip'             => $loginIp . '( ' . $this->_exte_get_user_loginIpCity($loginIp) . ' )',
				'created_on'            => time(),
				'type'                  => '0',
				'role_class'			=> $this->_auser['username'],
			]);
			
			return response()->json([
				'msg'           => 'SUC',
				'err'           => 'NOERR',
				'col'           => 'NOCOL',
			]);
		} else {
			return response()->json([
				'msg'           => 'FAIL',
				'err'           => 'INFOUPDATEFAIL',
				'col'           => 'NOCOL',
			]);
		}
	}
	
	public function agentsListSearch(Request $request)
	{
		$data = array(
			'userId'            => $request->userId,
			'userPid'           => $request->userPid,
			'userstatus'        => $request->userstatus,
			'transmode'         => $request->transmode,
			'startdate'         => $request->startdate,
			'enddate'           => $request->enddate,
			'searchtype'        => $request->searchtype,
		);
		
		$result = array('rows' => '', 'total' => '');
		
		$_rs = $this->get_agents_id_list('page', $data);
		
		if (!empty($_rs)) {
			$_upd = $this->_exte_mt4_batch_update_user_info ($_rs, new Agents());
			$_ag_sumdadta = $this->get_current_page_agents_id_list_sumdata($_rs);
			$_datasum = $this->get_current_all_agents_id_list_sumdata($data);
			
			for ($i = 0; $i < count ($_rs); $i ++) {
				$_rs[$i]['agentsTotal']         = Agents::where('parent_id', $_rs[$i]['user_id'])->whereIn('voided', array ('1', '2'))->whereIn('user_status', array('0', '1', '2', '4'))->count();
				$_rs[$i]['accountTotal']        = User::where('parent_id', $_rs[$i]['user_id'])->whereIn('voided', array ('1', '2'))->whereIn('user_status', array('0', '1', '2', '4'))->count();;
				$_rs[$i]['fy_money']            = number_format ($_ag_sumdadta[$_rs[$i]['user_id']][0]['total_fy'], '2', '.', '');
				$_rs[$i]['rj_money']            = number_format ($_ag_sumdadta[$_rs[$i]['user_id']][0]['total_rj'], '2', '.', '');
				$_rs[$i]['qk_money']            = number_format ($_ag_sumdadta[$_rs[$i]['user_id']][0]['total_qk'], '2', '.', '');
			}
			
			$result['rows']                     = $_rs;
			$result['total']                    = $this->get_agents_id_list ('count', $data);
			$result['footer']                   = [[
				'user_id'                       => '总计',
				'username'                      => '',
				'groupId'                       => '',
				'userstatus'                    => '',
				'isconfirmagtlvg'               => '',
				'parentId'                      => '',
				'agentsTotal'                   => '',
				'accountTotal'                  => '',
				'usermoney'                     => $_datasum['all_total_bal'],
				'custeqy'                       => $_datasum['all_total_eqy'],
				'fy_money'                      => $_datasum['all_total_fy'],
				'rj_money'                      => $_datasum['all_total_rj'],
				'qk_money'                      => $_datasum['all_total_qk'],
				'rec_crt_date'                  => '',
				'options'                       => ''
			]];
		}
		
		return json_encode ($result);
	}
	
	public function agents_add_browse()
	{
		$usergrpId  = UserGroup::where('voided', '1')->get()->toArray();
		$userlvl    = AgentsGroup::where('voided', '1')->where('group_id', '<=', 4)->get()->toArray();
		
		return view('admin.agent.agents_add_browse')->with([
			'usergrpId'     => $usergrpId,
			'userlvl'       => $userlvl,
		]);
	}
	
	//保存,注册
	public function agents_save (Request $request) {
    	
    	$data           = $request->data;
    	$username       = $data['username'];
		$sex            = $data['sex'];
		$userInviterId  = $data['userInviterId'];
		$userphoneNo    = $data['userphoneNo'];
		$userIdcardNo   = $data['userIdcardNo'];
		$useremail      = $data['useremail'];
		$usergrpId      = $data['usergrpId'];
		$usergrpName    = $request->usergrpName;
		$useragtId      = $data['useragtId'];
		$useragtName    = $request->useragtName;
		$userrebate     = $data['userrebate'];
		$usertype       = $data['usertype'];
		$userrights     = $data['userrights'];
		$usercycle      = $request->usercycle;
		$password       = $data['password'];
		$againpassword  = $data['againpassword'];
		$settlementmodel= $data['settlement_model']; //结算模式
		$_modules       = '86';
		
		//查看介绍人ID合法性
		if ($userInviterId == "0") {
			$chkInviterId['group_id'] = 0;
		} else {
			$chkInviterId   = Agents::where('voided', '1')->whereIn('user_status', array('0', '1', '2', '4'))->where('user_id', $userInviterId)->first();
		}
		
		if (!in_array($settlementmodel, array('1', '2'))) {
			return response()->json([
				'msg'        => 'FAIL',
				'err'        => 'settlmodvalerr',
				'col'        => 'settlement_model',
			]);
		}
		
		if ($userInviterId != "0" && ($settlementmodel != $chkInviterId['settlement_model'])) {
			return response()->json([
				'msg'        => 'FAIL',
				'err'        => 'Diffsettlmod',
				'col'        => 'settlement_model',
			]);
		}
		
		//检查代理级别合法性及返佣比例
		$chkuseragtId   = AgentsGroup::where('voided', '1')->where('group_id', $useragtId)->where('group_name', $useragtName)->first();
		$all_agt        = AgentsGroup::select('group_id', 'group_name', 'agents_comm_prop')->whereIn('group_id', array(1, 2, 3, 4))->where('voided', '1')->get()->toArray();
		
		//检查用户组别合法性
		$chkusergrp     = UserGroup::where('voided', '1')->where('user_group_id', $usergrpId)->where('user_group_name', $usergrpName)->first();
		
		//验证身份证，电话，邮箱 唯一性
		$_ido           = $this->_exte_verify_idno ($userIdcardNo);
		$_tel           = $this->_exte_verify_phone ($_modules . '-' . $userphoneNo);
		$_eml           = $this->_exte_verify_email ($useremail);
		
		if ($chkInviterId == null) {
			//无效的介绍人ID
			return response()->json([
				'msg'        => 'FAIL',
				'err'        => 'NonExist',
				'col'        => 'userInviterId',
			]);
		}
		
		//检查添加代理商的代理商级别是否高于或等于当前邀请人的代理商级别
		if ($chkInviterId['group_id'] >= $useragtId) {
			//添加的代理商级别大于等于邀请人代理商级别
			return response()->json([
				'msg'        => 'FAIL',
				'err'        => 'ThanAndEqualInviter',
				'col'        => 'useragtId',
			]);
		}
		
		if ($_ido) {
			//身份证已存在
			return response()->json([
				'msg'        => 'FAIL',
				'err'        => 'Existidcard',
				'col'        => 'userIdcardNo',
			]);
		}
		
		if ($_tel) {
			//手机号已存在
			return response()->json([
				'msg'        => 'FAIL',
				'err'        => 'Existphone',
				'col'        => 'userphoneNo',
			]);
		}
		
		if ($_eml) {
			//邮箱已存在
			return response()->json([
				'msg'        => 'FAIL',
				'err'        => 'Existemail',
				'col'        => 'useremail',
			]);
		}
		
		if ($chkusergrp == null) {
			//无效的用户组别
			return response()->json([
				'msg'        => 'FAIL',
				'err'        => 'Invalidgrp',
				'col'        => 'usergrpId',
			]);
		}
		
		if ($chkuseragtId == null) {
			//无效的用户组别
			return response()->json([
				'msg'        => 'FAIL',
				'err'        => 'InvalidagtId',
				'col'        => 'useragtId',
			]);
		}
		
		/*if ($useragtId == 4) {
			$min_comm_prop = 50;
		} else {
			$min_comm_prop = $all_agt[$chkuseragtId['group_id']]['agents_comm_prop'];
		}
		
		if ($chkuseragtId != null && ($userrebate > $chkuseragtId['agents_comm_prop'] || $userrebate < $min_comm_prop)) {
			//返佣比例大于系统预设值
			return response()->json([
				'msg'        => 'FAIL',
				'err'        => 'ThanSysDefault',
				'col'        => 'userrebate',
			]);
		}*/
		
		//如果邀请码0,权益和返佣比例可以随意大于0，如果有上级,那判断<=上级的返佣比例值就可以
		if ($userInviterId != "0" && $userrebate > $chkInviterId['comm_prop']) {
			//不能大于上级的返佣比例
			return response()->json([
				'msg'        => 'FAIL',
				'err'        => 'Nothanrebate',
				'col'        => 'userrebate',
			]);
		}
		
		if ($userInviterId != "0" && $userrights > $chkInviterId['rights']) {
			//不能大于上级的权益比例
			return response()->json([
				'msg'        => 'FAIL',
				'err'        => 'Nothanrights',
				'col'        => 'userrights',
			]);
		}
		
		$num = Agents::create([
			'user_name'             => $username,
			'password'              => base64_encode($password),
			'sex'                   => $sex,
			'phone'                 => $_modules . '-' . $userphoneNo,
			'IDcard_no'			    => $userIdcardNo,
			'email'				    => $useremail,
			'group_id'              => $useragtId,
			'parent_id'             => $userInviterId,
			'user_money'		    => '0',
			'cust_eqy'              => '0',
			'effective_cdt'         => '0',
			'comm_prop'			    => $userrebate,
			'mt4_grp'			    => $usergrpName,
			'trans_mode'            => $usertype, // 交易模式，0 佣金模式，1 保证金模式
			//'bond_money'          => '0/1', // 保证金金额
			'IDcard_status'		    => '0', // 默认 0 没通过审核，1 通过审核，2 正在审核中
			'user_status'           => '0', //0 未认证，1 已认证，-1 禁用
			'is_confirm_agents_lvg' => '1',
			'enable_readonly'       => '0', //默认， 能登录能交易(0=未勾上)，能登录 不能交易(1 = 只读 勾上)
			'is_out_money'          => '0', //default (0) 允许出金	1 不允许
			'enable'                => '1', //默认启用(能登录能交易, 1 = 勾上)， 不能登录(0 = 未勾上)
			'bank_status'           => '0',
			'IDcard_status'			=> '0',
			'cust_lvg'              => 100,
			'rights'                => $userrights, //权益比例
			'cycle'                 => ($usercycle) ? $usercycle : 0,//结算周期
			'settlement_model'      => $settlementmodel, //结算模式
			'voided'                => '1', //注册后允许登录
			'rec_crt_date'          => date('Y-m-d H:i:s'),
			'rec_upd_date'          => date('Y-m-d H:i:s'),
			'rec_crt_user'          => $this->_auser['username'],
			'rec_upd_user'          => $this->_auser['username'],
		]);
		
		if ($num) {
			//本地创建用户成功后更新当前用户country列
			$no = $num->find($num->user_id)->update(['mt4_code' => $num->user_id, 'country' => $this->_exte_show_account_relationship_chain($num->user_id, '-', 'id', 'admin')]);
			
			//本地新增用户成功，同步MT4注册
			$data = $this->_exte_get_user_info($num->user_id);
			$mt4_grpId = $this->_exte_get_mt4_grpId($data['mt4_grp']);
			$data['mt4_grpId'] = $mt4_grpId[0]['user_group_name'];
			$mt4 = $this->_exte_sync_mt4_reigster2($data);
			//TODO, 上线后测试
			//发送短信，邮件通知
			$_phone = $this->_exte_send_phone_notify($userphoneNo, 'registerSucInfo', array ('user_id' => $num['user_id'], 'password' => $password));
			$_email = $this->_exte_send_email_notify($useremail, '注册成功', $data, 'registerSuc', 'verifyphone');
			
			if (is_array($mt4) && $mt4['0'] == 'OK') {
				//注册成功
				return response()->json([
					'msg'        => 'SUC',
					'err'        => 'NOERR',
					'col'        => 'NOTCOL',
				]);
			} else {
				return response()->json([
					'msg'        => 'FAIL',
					'err'        => $mt4,
					'col'        => 'NOTCOL',
				]);
			}
		}
	}
	
	public function againSendSms(Request $request)
	{
		$_user_info = $this->_exte_get_user_info($request->userId);
	
		$phone = substr($_user_info['phone'], (stripos($_user_info['phone'], '-') + 1));
		
		$_rs = $this->_exte_send_phone_notify($phone, 'registerSucInfo', array ('user_id' => $_user_info->user_id, 'password' => base64_decode ($_user_info->password)));
		
		if($_rs) {
			return response()->json([
					'msg'        => 'SUC',
					'err'        => 'NOERR',
					'col'        => 'NOTCOL',
			]);
		} else {
			return response()->json([
					'msg'        => 'FAIL',
					'err'        => 'SENDFAIL',
					'col'        => 'NOTCOL',
			]);
		}
	}
	
	protected function get_agents_id_list($totalType, $data)
	{
		$query_sql = Agents::select(
			'agents.user_id as user_id', 'agents.user_name as username', 'agents.parent_id as parentId', 'agents.group_id as groupId',
			'agents.user_money as usermoney', 'agents.cust_eqy as custeqy', 'agents.user_status as userstatus', 'agents.IDcard_status as idcardstatus',
			'agents.bank_status as bankstatus', 'agents.mt4_grp as mt4grp', 'agents.trans_mode as transmode', 'agents.rights as rights', 'agents.comm_prop as commprop',
			'agents.is_confirm_agents_lvg as isconfirmagtlvg', 'agents.settlement_model as settlementmodel', 'agents.rec_crt_date',
			'user_group.user_group_id as usergrp_id', 'user_group.user_group_name as usergrp_name'
		)->leftjoin('user_group', function ($leftjoin) {
			$leftjoin->on('agents.mt4_grp', ' = ', 'user_group.user_group_name')->where('user_group.voided', ' = ', '1');
		})->where('agents.voided', '1')->whereIn('agents.user_status', array('0', '1', '2', '4'))
			->where(function ($subWhere) use ($data) {
				$this->_exte_set_search_condition($subWhere, $data);
			});
		
		return $this->_exte_get_query_sql_data($query_sql, $totalType, 'agents.rec_crt_date');
	}
	
	protected function get_current_page_agents_id_list_sumdata($data)
	{
		$_sumdata = array ();
		
		foreach ($data as $key => $vdata) {
			//分页返佣，入金，出金
			$_sumdata[$vdata['user_id']] = Mt4Trades::selectRaw('
				/*返佣*/
				sum(case when mt4_trades.CMD = 6 and mt4_trades.COMMENT LIKE "%-FY" then mt4_trades.PROFIT else 0 end) as total_fy,
				/*入金*/
				sum(case when mt4_trades.PROFIT > 0 and mt4_trades.CMD = 6 and mt4_trades.COMMENT NOT LIKE "%-FY" and mt4_trades.COMMENT NOT LIKE "%Adj%" then mt4_trades.PROFIT else 0 end) as total_rj,
				/*取款 出金*/
				sum(case when mt4_trades.PROFIT < 0 and mt4_trades.CMD = 6 and mt4_trades.COMMENT NOT LIKE "%Adj%" then mt4_trades.PROFIT else 0 end) as total_qk
				')->where('MT4_TRADES.LOGIN', $vdata['user_id'])->get()->toArray();
		}
		
		return $_sumdata;
	}
	
	protected function get_current_all_agents_id_list_sumdata($data)
	{
		$_all_sumdata['fy_rj_qk'] = Mt4Trades::selectRaw('
				/*返佣*/
				sum(case when mt4_trades.CMD = 6 and mt4_trades.COMMENT LIKE "%-FY" then mt4_trades.PROFIT else 0 end) as all_total_fy,
				/*入金*/
				sum(case when mt4_trades.PROFIT > 0 and mt4_trades.CMD = 6 and mt4_trades.COMMENT NOT LIKE "%-FY" and mt4_trades.COMMENT NOT LIKE "%Adj%" then mt4_trades.PROFIT else 0 end) as all_total_rj,
				/*取款 出金*/
				sum(case when mt4_trades.PROFIT < 0 and mt4_trades.CMD = 6 and mt4_trades.COMMENT NOT LIKE "%Adj%" then mt4_trades.PROFIT else 0 end) as all_total_qk
			')
			->whereIn('mt4_trades.LOGIN', function ($whereIn) use ($data) {
				$whereIn->select('agents.user_id')->from('agents')
					->where(function ($subWhere)use ($data) {
						$this->_exte_set_search_condition($subWhere, $data);
					})->whereIn('agents.voided', array ('1', '2'))
					->whereIn('agents.user_status', array('0', '1', '2', '4'));
			})->get()->toArray();
		
		//总余额，净值
		$_all_sumdata['bal_eqy'] = Mt4Users::selectRaw('
				/*余额*/
				sum(mt4_users.BALANCE) as all_total_bal,
				/*净值*/
				sum(mt4_users.EQUITY) as all_total_eqy
			')->whereIn('mt4_users.LOGIN', function ($whereIn) use ($data) {
			$whereIn->select('agents.user_id')->from('agents')
				->where(function ($subWhere)use ($data) {
					$this->_exte_set_search_condition($subWhere, $data);
				})->whereIn('agents.voided', array ('1', '2'))
				->whereIn('agents.user_status', array('0', '1', '2', '4'));
			})->get()->toArray();
		
		$_all_sumdata = array (
			'all_total_fy'          => number_format ($_all_sumdata['fy_rj_qk'][0]['all_total_fy'], '2', '.', ''),
			'all_total_rj'          => number_format ($_all_sumdata['fy_rj_qk'][0]['all_total_rj'], '2', '.', ''),
			'all_total_qk'          => number_format ($_all_sumdata['fy_rj_qk'][0]['all_total_qk'], '2', '.', ''),
			'all_total_bal'         => number_format ($_all_sumdata['bal_eqy'][0]['all_total_bal'], '2', '.', ''),
			'all_total_eqy'         => number_format ($_all_sumdata['bal_eqy'][0]['all_total_eqy'], '2', '.', ''),
		);
		
		return $_all_sumdata;
	}
	
	protected function _exte_mt4_update_user2($userId, $fill = false)
	{
		$this->_str_rala = $this->_exte_show_account_relationship_chain($userId, '-', 'id', 'admin');
		$col_ary['country'] = $this->_str_rala;
		$col_ary['login'] = $userId;
		$mt4_upd = $this->_exte_mt4_update_user($col_ary);
	}
	
	protected function _exte_check_agents_direct_customer_open_order($uid)
	{
		return Mt4Trades::whereIn('mt4_trades.LOGIN', function ($whereIn) use ($uid) {
			$whereIn->selectRaw("user.user_id from user where user.parent_id = " . intval($uid) . " and user.voided = 1 and user.user_status in ('0','1','2','4')");
		})->where('mt4_trades.CLOSE_TIME', '1970-01-01 00:00:00')->where('mt4_trades.CONV_RATE1', '<>', 0)->whereIn('mt4_trades.CMD', array(0, 1, 2, 3, 4, 5))->count();
	}
	
	public function test_info()
	{
		$param = array('UserLoginID' => 1102, 'UserEnableReadonly' => 1, 'UserIRD' => 'updRD');
		//$mt41 = $this->_exte_mt4_deposit_amount(1096, 1000000,  self::CZ);
		//$mt4 = $this->_exte_mt4_transfer_amount(1096, 1098, 100);
		//$mt4 = $this->_exte_mt4_withdrawal_amount(1098, 1000, 1098 . self::QK);
		//$mt4 = $this->_exte_mt4_update_credit(1098, -500);
		//$mt4 = $this->_exte_mt4_active_disable_user(1102);
		//$mt4 = $this->_exte_mt4_active_enable_user(1102);
		//$mt4 = $this->_exte_mt4_update_account($param, false);
		$mt4 = $this->_exte_mt4_verify_password(1102, 'abcd123456');
		
		dd($mt4);
		//dd($mt4);
	}
	
	protected function _exte_set_search_condition($subWhere, $data)
	{
		if ($data['searchtype'] == 'autoSearch' || $data['searchtype'] == 'clickSearch') {
			if (!empty($data['startdate']) && !empty($data['enddate']) && $this->_exte_is_Date ($data['startdate']) && $this->_exte_is_Date ($data['enddate'])) {
				$subWhere->whereBetween('agents.rec_crt_date', [$data['startdate']  . ' 00:00:00', $data['enddate'] . ' 23:59:59']);
			} else {
				if(!empty($data['startdate']) && $this->_exte_is_Date ($data['startdate'])) {
					$subWhere->where('agents.rec_crt_date',  '>= ', $data['startdate'] .' 23:59:59');
				}
				if(!empty($data['enddate']) && $this->_exte_is_Date ($data['enddate'])) {
					$subWhere->where('agents.rec_crt_date', '<', $data['enddate'] . ' 00:00:00');
				}
			}
			
			if ($data['searchtype'] == 'autoSearch') {
				$subWhere->where('agents.parent_id', 0);
			} else if ($data['searchtype'] == 'clickSearch') {
				if (!empty($data['userId'])) {
					$subWhere->where(function ($subOrWhere) use ($data) {
						$subOrWhere->where('agents.user_id', $data['userId'])
							->orWhere('agents.IDcard_no', 'like', '%' . $data['userId'] . '%')
							->orWhere('agents.user_name', 'like', '%' . $data['userId'] . '%');
					});
					//$subWhere->where('agents.user_id', $data['userId']);
				} /*else {
							$subWhere->where('agents.parent_id', 0);
						}*/
			}
			
			if(!empty($data['transmode'])) {
				$subWhere->where('agents.trans_mode', $data['transmode']);
			}
			if(!empty($data['userstatus'])) {
				$subWhere->where('agents.user_status', $data['userstatus']);
			}
		}
		
		if ($data['searchtype'] == 'showSubAgents') {
			$subWhere->where('agents.parent_id', $data['userPid']);
		}
		
		return $subWhere;
	}
}
