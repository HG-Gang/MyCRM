<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018-04-20
	 * Time: 下午 4:51
	 */
	
	namespace App\Model;
	
	use Illuminate\Database\Eloquent\Model;
	
	class AgentsGroup extends Model
	{
	
		protected $table = 'agents_group';
		
		protected $primaryKey = 'group_id';
		
		protected $guarded = [];
		
		public  $timestamps = FALSE;
	}