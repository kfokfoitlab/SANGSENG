<?php
	
	namespace App\Controllers\IMJOB;
	use App\Controllers\BaseController as Base;
	use App\Models\IMJOB\IMJOBModel as Model;
	use App\Models\Database\DatabaseModel;
	
	class Lists extends Base
	{
		private $page_name = "인재관리 > 전체 목록";
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
				//,"data" => $this->model->getList()
			);
			
			echo view('Common/Header.html');
			echo view(_CONTROLLER.'/Index.html', $data);
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
		
		public function Detail()
		{ //{{{
			$data = $this->model->getWorkerInfo();
			$data = array(
				"data" => $data
			);
			echo view('Common/Header.html');
			echo view('IMJOB/Lists/Detail.html',$data);
			echo view('Common/Footer.html');
		} //}}}
		
		public function Update()
		{ //{{{

		} //}}}
		
		public function statusUpdate()
		{
            $jsonInput  = file_get_contents('php://input');
            var_dump($jsonInput);
            $arr = explode('&',$jsonInput);
            $idx = $arr[0];
            $status = $arr[1];
			$this->model->statusUpdate($idx,$status);
			echo "1";
		}
		
		public function downloadFileNew(){
			$this->model->downloadFileNew();
		}
		
		public function updateWorker(){
			
			$result = $this->model->Update($_POST,$_FILES);
			
			if($result == "1") {
				echo "
                <script>
                    alert('근로자정보가 수정되었습니다.');
					window.location.replace('/IMJOB/Lists');
                </script>
            ";
			}else{
				echo "
                <script>
                    alert('오류가 발생했습니다.다시 시도해주세요');
					history.back(-1);
                </script>
            ";
			}
		}
		
		public function deleteWorker(){
			
			$result = $this->model->delete();
			
			if($result == "1") {
				echo "
                <script>
                    alert('근로자정보가 삭제되었습니다.');
					window.location.replace('/IMJOB/Lists');
                </script>
            ";
			}else{
				echo "
                <script>
                    alert('오류가 발생했습니다.다시 시도해주세요');
					history.back(-1);
                </script>
            ";
			}
		}

        public function Workersform(){
            $result = $this->model->regFormUpload($_FILES);
            if($result == 1) {
                echo "
                <script>
                    alert('양식이 등록되었습니다.');
					window.location.replace('/IMJOB/Lists');
                </script>
            ";
            }else{
                echo "
                <script>
                    alert('오류가 발생했습니다.다시 시도해주세요');
					history.back(-1);
                </script>
            ";
            }
        }
	}
