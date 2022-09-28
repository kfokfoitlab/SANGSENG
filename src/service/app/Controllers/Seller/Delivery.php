<?php
	
	namespace App\Controllers\Seller;
	use App\Controllers\BaseController;
	use App\Models\Management\Company\ApplicationModel;
	use App\Models\CompanyModel;
	use App\Models\DatabaseModel;
	use App\Models\Seller\IMJOBModel;
	
	class Delivery extends BaseController
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
			$data_page_total_cnt = count($data);
			$data = array(
				"data" => $data["data"],
				"data_cnt" => $data_cnt,
				"data_page_total_cnt" => $data["count"]
			);
			$_SESSION["disabledCount"]  = $data_cnt;
			echo view("Common/Header.html");
			echo view('Seller/IMJOB.html',$data);
			echo view("Common/Footer.html");
		} // }}}
		
		public function Status()
		{ // {{{
			echo view("Common/Header.html");
			echo view('Seller/DeliveryStatus.html');
			echo view("Common/Footer.html");
		} // }}}
		

	}
  ?>