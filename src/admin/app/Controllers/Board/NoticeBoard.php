<?php
	namespace App\Controllers\Board;
	use App\Controllers\BaseController as Base;
	use App\Models\Board\NoticeBoardModel as Model;
	use App\Models\Database\DatabaseModel;
	
	class NoticeBoard extends Base
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
		
		public function getList()
		{ //{{{
			
			$start = $_POST["start"];
			$length = $_POST["length"];
			$limit = array(
				"start" => $start
			,"length" => $length
			);
			
			$result = $this->model->getListData($_POST);
			
			$data = array(
				"draw" => @$_POST["draw"]
			,"recordsTotal" => $result["records_total"]
			,"recordsFiltered" => $result["filtered_total"]
			,"data" => $result["data"]
			);
			
			echo json_encode($data, JSON_UNESCAPED_UNICODE);
			
			die();
			
		} //}}}
		
		public function noticeRegister()
		{ //{{{
			echo view('Common/Header.html');
			echo view('Board/NoticeBoard/NoticeRegister.html',);
			echo view('Common/Footer.html');
		} //}}}
		
		public function noticeRegisterSubmit()
		{ //{{{
			
			$result = $this->model->Register($_POST,$_FILES);
			
			if($result == 1){
				echo "
					<script>
						alert('정상 등록되었습니다');
						location.href = '/Board/NoticeBoard';
					</script>
				";
			}else{
				echo "
					<script>
						alert('오류가 발생했습니다.다시 시도해주세요');
					</script>
				";
			}
		} //}}}
		
		public function statusUpdate()
		{
			$data = array(
				"idx" => $_GET["idx"]
			,"status" => $_GET["status"]
			);
			$this->model->statusUpdate($data);
			echo "
            <script>
                history.back();
            </script>
        ";
		}
		
		public function noticeDetail()
		{
			$data = $this->model->getNoticeBoard($_GET);
			
			echo view('Common/Header.html');
			echo view('Board/NoticeBoard/NoticeDetail.html',$data);
			echo view('Common/Footer.html');
		}
		
		public function downloadFileNew(){
			$this->model->downloadFileNew();
		}
		
		public function noticeUpdate()
		{
			$result = $this->model->noticeUpdate($_POST,$_FILES);
			
			if($result == 1) {
				echo "
            <script>
            	alert('수정되었습니다')
                location.href = '/Board/NoticeBoard/';
            </script>
        	";
			}else{
				echo "
            <script>
            	alert('오류가 발생했습니다');
                history.back();
            </script>
        	";
			}
		}
		
	}