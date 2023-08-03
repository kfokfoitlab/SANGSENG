<?php
	
	namespace App\Controllers\Member;
	use App\Controllers\BaseController as Base;
	use App\Models\Member\ForgotInfoModel as Model;
	use App\Models\Database\DatabaseModel;
	
	class ForgotInfo extends Base
	{
		private $page_name = "회원관리 > ID/PW찾기";
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
		
		public function Detail($uuid)
		{ //{{{
			
			$data = $this->model->Detail($uuid);
			$impairments = $this->database_model->getImpairmentAll();
			
			$data = array(
				"page_name" => $this->page_name
			,"data" => $data
			,"profile_img_url" => "/Image/".$data["profile_img_uuid"]
			,"impairment_data" => json_decode($data["impairment"], true)
			,"impairments" => $impairments
			);
			
			echo view('Common/HeaderSub.html');
			echo view(_CONTROLLER.'/Detail.html', $data);
			//echo script_tag("assets/js/"._CONTROLLER."/Index.js");
			echo view('Common/Footer.html');
			
			
		} //}}}
		
		public function Update($uuid)
		{ //{{{
			
			$data = $this->model->Detail($uuid);
			$impairments = $this->database_model->getImpairmentAll();
			
			$data = array(
				"page_name" => $this->page_name
			,"data" => $data
			,"profile_img_url" => "/Image/".$data["profile_img_uuid"]
			,"impairment_data" => json_decode($data["impairment"], true)
			,"impairments" => $impairments
			);
			
			echo view('Common/HeaderSub.html');
			echo view(_CONTROLLER.'/Update.html', $data);
			echo script_tag("assets/js/"._CONTROLLER."/Update.js");
			echo view("Modal/SearchPost.html");
			echo view('Common/Footer.html');
			
		} //}}}
		
		public function UpdateSubmit()
		{ //{{{
			
			$this->model->Update(@$_FILES, $_POST);
			
			echo "
            <script>
                alert('수정하였습니다.');
                window.location.replace('/"._CONTROLLER."/Detail/".$_POST["uuid"]."');
            </script>
        ";
			
			die();
			
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
		
		public function searchId()
		{

			$data = $this->model->getCompanyEmail($_GET);
			if($data['user_id'] != "") {
				echo view(_CONTROLLER.'/result.html', $data);
			}else{
				$data['errorMsg'] = "등록된 아이디가 없습니다";
				echo view(_CONTROLLER.'/result.html', $data);
			}
		}
		
		public function resetPw()
		{
			$data = array(
				"uuid" => $_POST["uuid"],
				"phone" => $_POST["phone"],
				"email" => $_POST["email"],
				"type" => $_POST["type"]
			);
			$resetPw = $this->model->resetPw($data);
			echo $resetPw;
		}
		
	}
