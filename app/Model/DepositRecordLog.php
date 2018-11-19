<?php
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/3/2
	 * Time: 10:22
	 */
	
	namespace App\Model;
	
	use Illuminate\Database\Eloquent\Model;
	
	class DepositRecordLog extends Model
	{
		protected $table = 'deposit_record_log';
		
		protected $primaryKey = 'dep_id';
		
		protected $guarded = [];
		
		public  $timestamps = FALSE;
		
		public static function _get_ytdDepTotal ($uid) {
			if ((int)$uid >= 8000001) {
				/**普通客户登录则显示直属昨天累计入金*/
				$_depTotal = DepositRecordLog::selectRaw('sum(dep_amount) as depAmount, sum(dep_act_amount) as depActAmount')
					->whereIn('deposit_record_log.rec_crt_user', function ($whereIn) use ($uid) {
						$whereIn->selectRaw('user.user_id from user where user.parent_id = ' . intval ($uid));
					})
					->leftJoin('mt4_trades', 'mt4_trades.TICKET', '=', 'deposit_record_log.dep_mt4_id')
					->where('deposit_record_log.voided', '02')->where('deposit_record_log.dep_status', '02')
					->whereBetween('deposit_record_log.rec_crt_date', [date("Y-m-d",strtotime("-1 day")) . ' 00:00:00', date("Y-m-d",strtotime("-1 day")) . ' 23:59:59'])
					->get()->toArray();
			} else {
				/*代理商登录则显示累计入金*/
				$_depTotal = DepositRecordLog::selectRaw('sum(dep_amount) as depAmount, sum(dep_act_amount) as depActAmount')
					->where('deposit_record_log.rec_crt_user', $uid)
					->leftJoin('mt4_trades', 'mt4_trades.TICKET', '=', 'deposit_record_log.dep_mt4_id')
					->where('deposit_record_log.voided', '02')->where('deposit_record_log.dep_status', '02')
					->get()->toArray();
			}
			
			return number_format($_depTotal[0]['depActAmount'], '2', '.', '');
		}
	}