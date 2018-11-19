<?php
	/**
	 * Created by PhpStorm.
	 * User: JMX
	 * Date: 2018/2/4
	 * Time: 16:32
	 */
	
	namespace App\Model;
	
	use Illuminate\Database\Eloquent\Model;
	
	class NewsList extends Model
	{
		protected $table = 'newslist';
		
		protected $primaryKey = 'news_id';
		
		protected $guarded = [];
		
		public  $timestamps = FALSE;
		
		protected static function _get_hotsNews ()
		{
			return NewsList::orderby('news_id', 'desc')->take(4)->get()->toArray();
		}
	}