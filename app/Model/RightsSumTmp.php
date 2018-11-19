<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018/7/16
	 * Time: 15:53
	 */
	
	namespace App\Model;
	
	
	use Illuminate\Database\Eloquent\Model;
	
	class RightsSumTmp extends Model
	{
		protected $table = 'rights_sum_tmp';
		
		protected $primaryKey = 'rights_sum_tmpid';
		
		protected $guarded = [];
		
		public  $timestamps = FALSE;
	}