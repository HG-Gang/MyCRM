<?php
	/**
	 * Created by PhpStorm.
	 * User: Hank
	 * Date: 2018/3/30
	 * Time: 14:57
	 */
	
	namespace App\Model;
	
	use Illuminate\Database\Eloquent\Model;
	class UserImg extends Model
	{
		protected $table = 'user_img';
		
		protected $primaryKey = 'img_id';
		
		protected $guarded = [];
		
		public  $timestamps = FALSE;
	}