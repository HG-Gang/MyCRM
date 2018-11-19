<?php
	/**
	 * Created by PhpStorm.
	 * User: Hank
	 * Date: 2018/3/29
	 * Time: 10:15
	 */
	
	namespace App\Model;
	
	use Illuminate\Database\Eloquent\Model;
	
	class CancelApply extends Model
	{
		protected $table = 'cancel_apply';
		
		protected $primaryKey = 'cancel_id';
		
		protected $guarded = [];
		
		public  $timestamps = FALSE;
	}