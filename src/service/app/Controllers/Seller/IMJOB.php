<?php
	
	namespace App\Controllers\Seller;
	use App\Controllers\BaseController;
	use App\Models\Management\Company\ApplicationModel;
	use App\Models\CompanyModel;
	use App\Models\DatabaseModel;
	use App\Models\Seller\IMJOBModel;
	
	class IMJOB extends BaseController
	{
		private $model;
		private $database_model;
		private $company_model;
		private $seller_model;
		
		public function __construct()
		{ //{{{
			$this->imjob_model = new IMJOBModel;
			$this->application_model = new ApplicationModel;
			$this->database_model = new DatabaseModel;
			$this->company_model = new CompanyModel;
		} //}}}
		
		public function List()
		{ // {{{
			$data = $this->imjob_model->getWorkerList();
			$data_cnt = $this->imjob_model->getWorkerCount();
			$data = array(
				"data" => $data,
				"data_cnt" => $data_cnt
			);
			echo view("Common/Header.html");
			echo view('Seller/IMJOB.html',$data);
			echo view("Common/Footer.html");
		} // }}}
		
		public function Manage()
		{ // {{{
			echo view("Common/Header.html");
			echo view('Seller/Manage.html');
			echo view("Common/Footer.html");
		} // }}}
		
		public function reg_worker(){
			
			$result = $this->imjob_model->Register($_POST,$_FILES);
			
			if($result == "1") {
				echo "
                <script>
                    alert('근로자가 등록되었습니다.');
					window.location.replace('/Seller/IMJOB/List');
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
		
		public function updateWorker(){
			
			$result = $this->imjob_model->Update($_POST,$_FILES);
			
			if($result == "1") {
				echo "
                <script>
                    alert('근로자정보가 수정되었습니다.');
					window.location.replace('/Seller/IMJOB/List');
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
			
			$result = $this->imjob_model->delete();
			
			if($result == "1") {
				echo "
                <script>
                    alert('근로자정보가 삭제되었습니다.');
					window.location.replace('/Seller/IMJOB/List');
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
		
		public function IMJOBView(){
			
			$data = $this->imjob_model->getWorkerInfo();
			$data = array(
				"data" => $data
			);
			echo view("Common/Header.html");
			echo view('Seller/IMJOBView.html',$data);
			echo view("Common/Footer.html");
		}
		
		public function downloadFileNew(){
			
			$target_Dir = ROOTPATH."/public/uploads/upload_files/";
			$file = $_GET["fileName"];
			$down = $target_Dir . $file;
			$filesize = filesize($down);
			
			if (file_exists($down)) {
				header("Content-Type:application/octet-stream");
				header("Content-Disposition:attachment;filename=$file");
				header("Content-Transfer-Encoding:binary");
				header("Content-Length:" . filesize($target_Dir . $file));
				header("Cache-Control:cache,must-revalidate");
				header("Pragma:no-cache");
				header("Expires:0");
				if (is_file($down)) {
					$fp = fopen($down, "r");
					while (!feof($fp)) {
						$buf = fread($fp, 8096);
						$read = strlen($buf);
						print($buf);
						flush();
					}
					fclose($fp);
				}
			} else {
        echo "
				<script>alert('존재하지 않는 파일입니다.');</script>
				";
			}
			
		}
	}
  ?>