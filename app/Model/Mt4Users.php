<?php
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/2/4
	 * Time: 11:40
	 */
	
	namespace App\Model;
	
	use Illuminate\Database\Eloquent\Model;
	
	class Mt4Users extends Model
	{
		protected $table = 'mt4_users';
		
		protected $primaryKey = 'LOGIN';
		
		protected $guarded = [];
		
		public static function _mt4_user_info ($uid)
		{
			return Mt4Users::select('LOGIN', 'NAME')->find($uid);
		}
	}