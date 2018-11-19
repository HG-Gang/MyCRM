<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018/6/15
	 * Time: 11:11
	 */
	
	namespace App\Http\Controllers\User;
	
	
	use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
	use App\Model\NewsList;
	use Illuminate\Http\Request;
	
	class NewsListController extends Abstract_Mt4service_Controller
	{
		public function news_list_browse() {
			return view('user.news_list.news_list_browse');
		}
		
		//新闻详情
		public function news_detail(Request $request, $newsId)
		{
			$info = NewsList::where('news_id', $newsId)->where('voided', '1')->whereIn('newslist.is_push', array('0', '1'))->get()->toArray();
			
			return view('admin.news.news_detail')->with(['newsInfo' => $info[0]]);
		}
		
		public function newsListSearch (Request $request)
		{
			$result = array('rows' => '', 'total' => '');
			
			$_rs = $this->get_news_list('page', $request);
			
			if (!empty($_rs)) {
				$result['rows'] = $_rs;
				$result['total'] = $this->get_news_list ('count', $request);
			}
			
			return json_encode ($result);
		}
		
		protected function get_news_list($searchType, $request)
		{
			$startdate      = $request->startdate;
			$enddate        = $request->enddate;
			
			$query_sql = NewsList::where('newslist.voided', '1')->whereIn('newslist.is_push', array('0', '1'))
				->where(function ($subWhere) use ($startdate, $enddate) {
					if (!empty($startdate) && !empty($enddate) && $this->_exte_is_Date ($startdate) && $this->_exte_is_Date ($enddate)) {
						$subWhere->whereBetween('newslist.rec_upd_date', [$startdate .' 00:00:00', $enddate . ' 23:59:59']);
					} else {
						if(!empty($startdate) && $this->_exte_is_Date ($startdate)) {
							$subWhere->where('newslist.rec_upd_date',  '>= ', $startdate .' 23:59:59');
						}
						if(!empty($enddate) && $this->_exte_is_Date ($enddate)) {
							$subWhere->where('newslist.rec_upd_date', '<', $enddate .' 00:00:00');
						}
					}
				});
			
			if ($searchType == 'page') {
				$id_list = $query_sql->skip($this->_offset)->take($this->_pageSize)->orderBy('newslist.rec_upd_date', 'desc')->get()->toArray();
			} else if ($searchType == 'count') {
				$id_list = $query_sql->count();
			} else if ($searchType == 'sum') {
				$id_list = $query_sql->get()->toArray();
			}
			
			return $id_list;
		}
	}