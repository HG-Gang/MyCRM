<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018/6/6
	 * Time: 11:10
	 */
	
	namespace App\Http\Controllers\Admin;
	
	use Illuminate\Http\Request;
	use App\Model\SystemConfig;
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	
	class ExchangeRateController extends Abstract_Mt4service_Controller
	{
		public function whpj_rate_browse()
		{
			//获取系统表现有的配置信息
			$sys_info = SystemConfig::where('voided', '1')->get()->toArray();
			
			return view('admin.whpj_rate.whpj_rate_browse')->with(['sys_info' => $sys_info[0]]);
		}
		
		public function whpj_rate_save(Request $request)
		{
			//$sys_poundage_money = $request->sys_poundage_money; //出金手续费
			$sys_deposit_rate       = $request->sys_deposit_rate; //入金汇率
			$sys_draw_rate          = $request->sys_draw_rate; //取款汇率
			
			$num = SystemConfig::find(1)->where('voided', '1')
				->update([
					//'sys_poundage_money'        => $sys_poundage_money,
					'sys_deposit_rate'          => $sys_deposit_rate,
					'sys_draw_rate'             => $sys_draw_rate,
					'rec_upd_user'              => $this->_auser['username'],
					'rec_upd_date'              => date ('Y-m-d H:i:s'),
				]);
			
			if ($num) {
				return response()->json([
					'msg'               => 'SUC',
				]);
			} else {
				return response()->json([
					'msg'               => 'FAIL',
				]);
			}
		}
	}