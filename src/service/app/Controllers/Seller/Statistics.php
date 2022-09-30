<?php
	
	namespace App\Controllers\Seller;
	use App\Controllers\BaseController;
	use App\Models\Management\Company\ApplicationModel;
	use App\Models\Delivery\DeliveryModel;
	use App\Models\DatabaseModel;
	use App\Models\Seller\IMJOBModel;
	
	class Statistics extends BaseController
	{
		private $model;
		private $database_model;
		private $delivery_model;
		private $seller_model;
		
		public function __construct()
		{ //{{{
			$this->imjob_model = new IMJOBModel;
			$this->application_model = new ApplicationModel;
			$this->database_model = new DatabaseModel;
			$this->delivery_model = new DeliveryModel;
		} //}}}
		
		public function SalesAnalysis()
		{ // {{{
			echo view("Common/Header.html");
			echo view('Seller/SalesAnalysis.html');
			echo view("Common/Footer.html");
		} // }}}
		
		

  
  

  
	}
  ?>