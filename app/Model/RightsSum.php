<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018/7/10
	 * Time: 16:44
	 */
	
	namespace App\Model;
	
	use Illuminate\Database\Eloquent\Model;
	
	class RightsSum extends Model
	{
		protected $table = 'rights_sum';
		
		protected $primaryKey = 'rights_id';
		
		protected $guarded = [];
		
		public  $timestamps = FALSE;
	}