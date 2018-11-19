<?php
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/2/4
	 * Time: 16:07
	 */
	
	namespace App\Model;
	
	use Illuminate\Database\Eloquent\Model;
	
	class Mt4Trades extends Model
	{
		protected $table = 'mt4_trades';
		
		protected $primaryKey = 'TICKET';
		
		protected $guarded = [];
		
		public static function _get_depositTotal ($uid) {
			return Mt4Trades::selectRaw("
				sum(case when mt4_trades.COMMENT not like '%-XY' and mt4_trades.PROFIT > 0 then mt4_trades.PROFIT else 0 end) as sum_profit
			")->leftjoin('deposit_record_log', function ($leftjoin) {
				$leftjoin->on('deposit_record_log.dep_mt4_id', ' = ', 'mt4_trades.TICKET')
					->where('deposit_record_log.dep_status', ' = ', '02')->where('deposit_record_log.voided', ' = ', '02');
			})->where('mt4_trades.OPEN_PRICE', 0)->where('mt4_trades.PROFIT', '>', 0)
				->whereIn('mt4_trades.CMD', array('6'))
				->whereIn('mt4_trades.LOGIN', function ($whereIn) use ($uid) {
					$whereIn->select('mt4_users.LOGIN')->from('mt4_users')->where('mt4_users.ZIPCODE', $uid);
				})->whereBetween('MODIFY_TIME', [date("Y-m-d",strtotime('-1 day')) . '00:00:00', date('Y-m-d',strtotime('-1 day')) . '23:59:59'])
				->where(function ($query) {
					$query->orWhere(function ($subQuery) {
						$subQuery->orWhere('COMMENT', 'like',  '%-ZH') //佣金转户
						->orWhere('COMMENT', 'like', '%-TH') //佣金转户退回
						->orWhere('COMMENT', 'like', '%-CZ') //充值
						//->orWhere('COMMENT', 'like', '%-FY') //返佣
						->orWhere('COMMENT', 'like', '%-RJ') // 批量入金
						//->orWhere('COMMENT', 'like', '%-XY') // 信用
						->orWhere('COMMENT', 'like', '%-CJTH'); // 出金失败，退回
					})->orWhere(function ($orSubQuery) {
						$orSubQuery->orWhere('COMMENT', 'like', 'Adj%');
					});
				})->get()->toArray();
		}
		
		public static function _get_withdrawTotal ($uid) {
			return Mt4Trades::selectRaw("
				sum(case when mt4_trades.COMMENT not like '%-XY' and mt4_trades.PROFIT < 0 then mt4_trades.PROFIT else 0 end) as sum_withdrawal_profit
			")->leftjoin('draw_record_log', function ($leftjoin) {
				$leftjoin->on('draw_record_log.mt4_trades_no', ' = ', 'mt4_trades.TICKET')
					->where('draw_record_log.mt4_return_status', ' = ', '0')
					->where('draw_record_log.apply_status', ' = ', '2')
					->where('draw_record_log.voided', ' = ', '1');
			})->whereIn('mt4_trades.LOGIN', function ($whereIn) use ($uid) {
				$whereIn->select('mt4_users.LOGIN')->from('mt4_users')->where('mt4_users.ZIPCODE', $uid);
			})->where('mt4_trades.OPEN_PRICE', 0)->where('mt4_trades.PROFIT', '<', 0)->where('CMD', 6)
				->whereBetween('MODIFY_TIME', [date("Y-m-d",strtotime('-1 day')) . '00:00:00', date('Y-m-d',strtotime('-1 day')) . '23:59:59'])
				->where(function ($query) {
					$query->orWhere(function ($subQuery) {
						$subQuery->orWhere('COMMENT', 'like',  '%-ZH')
							->orWhere('COMMENT', 'like', '%-QK')
							->orWhere('COMMENT', 'like', '%-RJTH'); //CZ入金退回
					})->orWhere(function ($orSubQuery) {
						$orSubQuery->orWhere('COMMENT', 'like', 'Adj%');
					});
				})
				->get()->toArray();
		}
		
		public static function _get_closeTotal ($uid) {
			return Mt4Trades::where('LOGIN', $uid)->where('CLOSE_TIME', '>', '1970-01-01 00:00:00')->whereIn('CMD', array(0, 1, 2, 3, 4, 5))->where('CONV_RATE1', '<>', 0)->count();
		}
		
		public static function _get_openTotal ($uid) {
			return Mt4Trades::where('LOGIN', $uid)->where('CLOSE_TIME', '1970-01-01 00:00:00')->whereIn('CMD', array(0, 1, 2, 3, 4, 5))->where('CONV_RATE1', '<>', 0)->count();
		}
	}