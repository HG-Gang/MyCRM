<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018-04-23
	 * Time: 下午 4:12
	 */
	
	namespace App\Model;
	
	use Illuminate\Database\Eloquent\Model;
	
	class SystemParam extends Model
	{
		protected $table = 'system_param';
		
		protected $primaryKey = 'sys_id';
		
		protected $guarded = [];
		
		public  $timestamps = FALSE;
	}