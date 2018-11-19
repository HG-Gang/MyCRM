<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018-04-26
	 * Time: 上午 11:30
	 */
	
	namespace App\Model;
	
	
	use Illuminate\Database\Eloquent\Model;
	
	class UserTrades extends Model
	{
		protected $table = 'user_trades';
		
		protected $primaryKey = 'trades_id';
		
		protected $guarded = [];
		
		public  $timestamps = FALSE;
	}