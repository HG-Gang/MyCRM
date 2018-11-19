<?php
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/2/4
	 * Time: 10:37
	 */
	
	namespace App\Model;
	
	use Illuminate\Database\Eloquent\Model;
	
	class Agents extends Model
	{
		protected $table = 'agents';
		
		protected $primaryKey = 'user_id';
		
		protected $guarded = [];
		
		public  $timestamps = FALSE;
		
		public function _user_info ($uid)
		{
			return Agents::whereIn('voided', array ('1', '2'))->whereIn('user_status', array ('0', '1', '2', '4'))->find($uid);
		}
		
		public function _update_user_voided($uid) {
			return Agents::whereIn('voided', array ('1', '2'))->whereIn('user_status', array ('0', '1', '2', '4'))->find($uid)
				->update([
					'voided'        => '1',
					'rec_upd_date'  => date ('Y-m-d H:i:s'),
				]);
		}
		
		protected static function _get_agentsTotal ($uid)
		{
			return Agents::where('parent_id', $uid)->whereIn('voided', array ('1', '2'))->whereIn('user_status', array ('0', '1', '2', '4'))->count();
		}
	}