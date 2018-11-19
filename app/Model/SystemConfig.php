<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018-04-23
	 * Time: 下午 3:32
	 */
	
	namespace App\Model;
	
	use Illuminate\Database\Eloquent\Model;
	
	class SystemConfig extends Model
	{
		protected $table = 'system_config';
		
		protected $primaryKey = 'sys_id';
		
		protected $guarded = [];
		
		public  $timestamps = FALSE;
	}