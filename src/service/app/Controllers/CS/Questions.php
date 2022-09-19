<?php
	
	namespace App\Controllers\CS;
	use App\Controllers\BaseController;
	use App\Models\DatabaseModel;
	use App\Models\CS\QuestionsModel as Model;
	use CodeIgniter\Session\Session;
	
	class Questions extends BaseController
	{
		
		private $model;
		private $database_model;
		
		public function __construct()
		{ //{{{
			$this->model = new Model;
			$this->database_model = new DatabaseModel;
		} //}}}
		
		public function index()
		{
			$data = $this->model->getListData();
			$data = array(
				"data" => $data["data"],
				"data_page_total_cnt" => $data["count"]
			);
			echo view("Common/Header.html");
			echo view('CS/Questions/Index.html',$data);
			echo view("Common/Footer.html");
		}
		
		public function Register()
		{
			if(@$_SESSION["login"] != "success"){
				echo "
				<script>
                	alert('로그인이 필요합니다.');
                	location.href = '/Auth/SignIn';
				</script>
            ";
				
				die();
			}
			echo view("Common/Header.html");
			echo view('CS/Questions/Register.html');
			echo view("Common/Footer.html");
		}
		
		public function RegisterSubmit()
		{
			if(@$_SESSION["login"] != "success"){
				echo "
				<script>
                	alert('로그인 후 등록가능합니다');
                	location.href = '/Auth/SignIn';
				</script>
            ";
				
				die();
			}
			
			$result = $this->model->Register($_POST);
			
			if($result == 1){
				echo "
				<script>
				alert('1:1 문의가 등록되었습니다');
				location.href = '/CS/Questions/';
				</script>
				";
			}else{
				echo"
				<script>
				alert('오류가 발생했습니다');
				history.back();
				</script>
				";
			}
		}
		
		public function questionsUpdateSubmit(){
			
			$result = $this->model->questionsUpdateSubmit($_POST);
			
			if($result == 1){
				echo "
					<script>
						alert('정상으로 수정되었습니다');
						history.back();
					</script>
				";
			}else{
				echo "
					<script>
						alert('오류가 발생했습니다.다시 시도해주세요');
					</script>
				";
			}
		}
		
		public function Detail()
		{
			$data = $this->model->getQuestionsBoard();
			$this->model->hitUpdate();
			echo view("Common/Header.html");
			echo view('CS/Questions/Detail.html',$data);
			echo view("Common/Footer.html");
		}
		
		public function Delete()
		{
			$data = $this->model->delete();
			echo "
			<script>
			alert('삭제되었습니다');
			location.href='/CS/Questions';
</script>
			";
		}
		
		public function downloadFileNew(){
			$this->model->downloadFileNew();
		}
		
	}
