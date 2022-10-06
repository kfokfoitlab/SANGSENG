<?php
	
	namespace App\Controllers\Seller;
	use App\Controllers\BaseController;
	use App\Models\Management\Company\ApplicationModel;
	use App\Models\DatabaseModel;
    use App\Models\Seller\StatisticsModel;


    class Statistics extends BaseController
	{
		private $statistics_model;
		private $database_model;
		private $seller_model;
		
		public function __construct()
		{ //{{{
			$this->statistics_model = new StatisticsModel;
			$this->application_model = new ApplicationModel;
			$this->database_model = new DatabaseModel;
		} //}}}
		
		public function SalesAnalysis()
		{ // {{{
            $uuid = $_SESSION["login_info"]["uuid"];
            $total = $this->statistics_model->TotalPrice($uuid);
            $static_list = $this->statistics_model->getStatistics($uuid);

            $data = array(
                "static_list" => $static_list,
                "year_total" => $total
            );

            echo view("Common/Header.html");
			echo view('Seller/SalesAnalysis.html',$data);
			echo view("Common/Footer.html");
		} // }}}

	}
  ?>