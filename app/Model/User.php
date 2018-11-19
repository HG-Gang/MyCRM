<?php
	
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/2/4
	 * Time: 10:30
	 */
	namespace App\Model;
	
	use Illuminate\Database\Eloquent\Model;
	
	class User extends Model
	{
		protected $table = 'user';
		
		protected $primaryKey = 'user_id';
		
		protected $guarded = [];
		
		public  $timestamps = FALSE;
		
		public function _user_info ($uid)
		{
			 return User::whereIn('voided', array ('1', '2'))->whereIn('user_status', array ('0', '1', '2', '4'))->find($uid);
			
		}
		
		public function _update_user_voided($uid) {
			return User::whereIn('voided', array ('1', '2'))->whereIn('user_status', array ('0', '1', '2', '4'))->find($uid)
				->update([
					'voided'        => '1',
					'rec_upd_date'  => date ('Y-m-d H:i:s'),
				]);
		}
		
		public static function _get_accountTotal ($uid)
		{
			return User::where('parent_id', $uid)->whereIn('voided', array ('1', '2'))->whereIn('user_status', array ('0', '1', '2', '4'))->count();
		}
	}