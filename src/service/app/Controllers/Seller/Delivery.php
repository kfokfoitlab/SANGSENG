<?php
	
	namespace App\Controllers\Seller;
	use App\Controllers\BaseController;
	use App\Models\Management\Company\ApplicationModel;
	use App\Models\Delivery\DeliveryModel;
	use App\Models\DatabaseModel;
	use App\Models\Seller\IMJOBModel;
	
	class Delivery extends BaseController
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
            if($_GET['cn'] != ""){
                $delivery = $this->delivery_model->getDeliveryList($_GET);
            }
            $uuid = $_SESSION['login_info']['uuid'];
            $contractList = $this->delivery_model->getContractList($uuid);
            $data = array(
                "contractList" => $contractList
                ,"delivery" => $delivery
            );
			echo view("Common/Header.html");
			echo view('Seller/DeliveryStatus.html',$data);
			echo view("Common/Footer.html");
		} // }}}

        public function DeliverySubmit(){
            $result = $this->delivery_model->Register($_FILES,$_POST);

            if($result == 1) {
                echo "
                <script>
                    alert('배송이 등록되었습니다.');
					window.location.replace('/Seller/DeliveryStatus/?cn=".$_POST["contract_no"]."');
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
  ?>