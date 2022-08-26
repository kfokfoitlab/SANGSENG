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
			echo view("Common/Header.html");
			echo view('Seller/IMJOB.html');
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
	}