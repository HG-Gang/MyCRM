<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018-05-11
	 * Time: 下午 6:09
	 */
	
	namespace App\Model;
	
	
	use Illuminate\Database\Eloquent\Model;
	
	class OperationLog extends Model
	{
		protected $table = 'operation_log';
		
		protected $primaryKey = 'id';
		
		protected $guarded = [];
		
		public  $timestamps = FALSE;
	}