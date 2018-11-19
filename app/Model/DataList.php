<?php
	/**
	 * Created by PhpStorm.
	 * User: Hank
	 * Date: 2018/4/4
	 * Time: 15:06
	 */
	
	namespace App\Model;
	
	use Illuminate\Database\Eloquent\Model;
	
	class DataList extends Model
	{
		protected $table = 'data_list';
		
		protected $primaryKey = 'user_id';
		
		protected $guarded = [];
		
		public  $timestamps = FALSE;
	}