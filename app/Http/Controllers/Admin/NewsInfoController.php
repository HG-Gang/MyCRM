<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018/6/13
	 * Time: 14:53
	 */
	
	namespace App\Http\Controllers\Admin;
	
	
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	use Illuminate\Http\Request;
	use App\Model\NewsList;
	
	class NewsInfoController extends Abstract_Mt4service_Controller
	{
		public function news_list_browse ()
		{
			return view('admin.news.news_list_browse');
		}
		
		public function new_add_browse ()
		{
			return view('admin.news.news_add_browse');
		}
		
		public function newsListSearch (Request $request)
		{
			$data = array(
				'startdate'         => $request->startdate,
				'enddate'           => $request->enddate,
			);
			
			$result = array('rows' => '', 'total' => '');
			
			$_rs = $this->get_news_list('page', $data);

			if (!empty($_rs)) {
				$result['rows'] = $_rs;
				$result['total'] = $this->get_news_list ('count', $data);
			}
			
			return json_encode ($result);
		}
		
		public function news_save (Request $request)
		{
			$newsTitle          = $request->newsTitle;
			$ispush             = $request->ispush;
			$newsContent        = $request->newsContent;
			
			$num = NewsList::create([
				'news_title'            => $newsTitle,
				'news_content'          => $newsContent,
				'is_push'               => $ispush,
				'voided'                => '1',
				'news_user'             => $this->_auser['username'],
				'rec_crt_user'          => $this->_auser['username'],
				'rec_upd_user'          => $this->_auser['username'],
				'rec_crt_date'          => date('Y-m-d H:i:s'),
				'rec_upd_date'          => date('Y-m-d H:i:s'),
			]);
			
			if ($num) {
				return response()->json([
					'msg'           => 'SUC',
				]);
			} else {
				return response()->json([
					'msg'           => 'FAIL',
				]);
			}
		}
		
		public function news_edit (Request $request, $newsid)
		{
			$info = NewsList::where('news_id', $newsid)->where('voided', '1')->get()->toArray();
			
			return view('admin.news.news_edit_browse')->with(['newsInfo' => $info[0]]);
		}
		
		public function news_update (Request $request)
		{
			$newsId             = $request->newsId;
			$ispush             = $request->ispush;
			$newsTitle          = $request->newsTitle;
			$newsContent        = $request->newsContent;
			
			$num = NewsList::where('voided', '1')->where('news_id', $newsId)
				->update([
					'news_title'            => $newsTitle,
					'news_content'          => $newsContent,
					'is_push'               => $ispush,
					'news_user'             => $this->_auser['username'],
					'rec_upd_user'          => $this->_auser['username'],
					'rec_upd_date'          => date('Y-m-d H:i:s'),
				]);
			
			if ($num) {
				return response()->json([
					'msg'           => 'SUC',
				]);
			} else {
				return response()->json([
					'msg'           => 'FAIL',
				]);
			}
		}
		
		protected function get_news_list($totalType, $data)
		{
			$query_sql = NewsList::where('newslist.voided', '1')->whereIn('newslist.is_push', array('0', '1'))
				->where(function ($subWhere) use ($data) {
					$this->_exte_set_search_condition($subWhere, $data);
				});
			
			return $this->_exte_get_query_sql_data($query_sql, $totalType, 'newslist.rec_upd_date');
		}
		
		protected function _exte_set_search_condition($subWhere, $data)
		{
			if (!empty($data['startdate']) && !empty($data['enddate']) && $this->_exte_is_Date ($data['startdate']) && $this->_exte_is_Date ($enddate)) {
				$subWhere->whereBetween('newslist.rec_upd_date', [$data['startdate'] .' 00:00:00', $data['enddate'] . ' 23:59:59']);
			} else {
				if(!empty($data['startdate']) && $this->_exte_is_Date ($data['startdate'])) {
					$subWhere->where('newslist.rec_upd_date',  '>= ', $data['startdate'] .' 23:59:59');
				}
				if(!empty($enddate) && $this->_exte_is_Date ($data['enddate'])) {
					$subWhere->$data['enddate']('newslist.rec_upd_date', '<', $data['enddate'] .' 00:00:00');
				}
			}
			
			return $subWhere;
		}
	}