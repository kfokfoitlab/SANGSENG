<?php
	namespace App\Controllers\Board;
	use App\Controllers\BaseController as Base;
	use App\Models\Board\noticeBoardModel as Model;
	use App\Models\Database\DatabaseModel;
	
	class noticeBoard extends Base
	{
		private $page_name = "게시판관리 > 공지사항";
		private $model;
		private $database_model;
		
		public function __construct()
		{ //{{{
			$this->model = new Model;
			$this->database_model = new DatabaseModel;
		} //}}}
		
		public function Index()
		{ //{{{
			$data = array(
				"page_name" => $this->page_name
			);
			
			echo view('Common/Header.html');
			echo view(_CONTROLLER.'/Index.html',$data);
			echo script_tag("assets/js/"._CONTROLLER."/Index.js");
			echo view('Common/Footer.html');
			
		} //}}}
		
	}