<?php
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/2/4
	 * Time: 14:51
	 */
	
	namespace App\Model;
	
	use Illuminate\Database\Eloquent\Model;
	
	class SystemLoginLog extends Model
	{
		protected $table = 'system_login_log';
		
		protected $primaryKey = 'login_id';
		
		protected $guarded = [];
		
		public  $timestamps = FALSE;
	}