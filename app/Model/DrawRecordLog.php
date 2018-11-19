<?php
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/3/2
	 * Time: 11:17
	 */
	
	namespace App\Model;
	
	use Illuminate\Database\Eloquent\Model;
	
	class DrawRecordLog extends Model
	{
		protected $table = 'draw_record_log';
		
		protected $primaryKey = 'record_id';
		
		protected $guarded = [];
		
		public  $timestamps = FALSE;
		
		public static function _get_ytdDrawTotal ($uid) {
			if ((int)$uid >= 8000001) {
				/*普通客户登录则显示直属昨天累计出金*/
				$_drawTotal = DrawRecordLog::selectRaw('sum(apply_amount) as applyAmount, sum(act_draw) as actDraw')
					->whereIn('draw_record_log.rec_crt_user', function ($whereIn) use ($uid) {
						$whereIn->selectRaw('user.user_id from user where user.parent_id = ' . intval ($uid));
					})
					->leftJoin('mt4_trades', 'mt4_trades.TICKET', '=', 'draw_record_log.mt4_trades_no')
					->whereIn('draw_record_log.apply_status', array ('0', '1', '2'))
					->whereBetween('draw_record_log.rec_crt_date', [date("Y-m-d",strtotime("-1 day")) . ' 00:00:00', date("Y-m-d",strtotime("-1 day")) . ' 23:59:59'])
					->get()->toArray();
			} else {
				/*代理商登录则显示累计出金*/
				$_drawTotal = DrawRecordLog::selectRaw('sum(apply_amount) as applyAmount, sum(act_draw) as actDraw')
					->where('draw_record_log.rec_crt_user', $uid)
					->leftJoin('mt4_trades', 'mt4_trades.TICKET', '=', 'draw_record_log.mt4_trades_no')
					->whereIn('draw_record_log.apply_status', array ('0', '1', '2'))
					->get()->toArray();
			}
			
			return number_format($_drawTotal[0]['applyAmount'], '2', '.', '');
		}
		
	}