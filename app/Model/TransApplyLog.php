<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018/5/23
	 * Time: 12:07
	 */
	
	namespace App\Model;
	
	use Illuminate\Database\Eloquent\Model;
	class TransApplyLog extends Model
	{
		protected $table = 'trans_apply_log';
		
		protected $primaryKey = 'trans_id';
		
		protected $guarded = [];
		
		public  $timestamps = FALSE;
	}