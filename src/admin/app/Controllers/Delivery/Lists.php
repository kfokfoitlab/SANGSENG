<?php
	
	namespace App\Controllers\Delivery;
	use App\Controllers\BaseController as Base;
	use App\Models\Delivery\DeliveryModel as Model;
	use App\Models\Database\DatabaseModel;
	
	class Lists extends Base
	{
		private $page_name = "배송관리 > 전체 목록";
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
		
		public function Detail($idx)
		{ //{{{
			$data = $this->model->Detail($idx);
            $contract = $this->model->Contract($idx);
			$data = array(
				"data" => $data
                ,"contract" => $contract
			);
			echo view('Common/Header.html');
			echo view('Delivery/Lists/Detail.html',$data);
			echo view('Common/Footer.html');
		} //}}}
		
		public function DateUpdate()
		{ //{{{

            $result = $this->model->DateUpdate($_GET);
            if($result == "1") {
                echo "
                <script>
                    alert('수정되었습니다.');
					window.location.replace('/Delivery/Lists/Detail/".$_GET["cidx"]."');
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
		} //}}}

        public function DeliveryDel(){
            $result = $this->model->DeliveryDel($_GET);
            if($result == "1") {
                echo "
                <script>
                    alert('삭제 되었습니다.');
					window.location.replace('/Delivery/Lists/Detail/".$_GET["cidx"]."');
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

		public function downloadFileNew(){
			$this->model->downloadFileNew();
		}

		
	}
