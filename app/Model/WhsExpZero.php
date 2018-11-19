<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018-05-16
	 * Time: 下午 4:37
	 */
	
	namespace App\Model;
	
	
	use Illuminate\Database\Eloquent\Model;
	
	class WhsExpZero extends Model
	{
		protected $table = 'whs_exp_zero';
		
		protected $primaryKey = 'wez_id';
		
		protected $guarded = [];
		
		public  $timestamps = FALSE;
	}