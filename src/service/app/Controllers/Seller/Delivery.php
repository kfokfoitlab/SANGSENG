<?php
	
	namespace App\Controllers\Seller;
	use App\Controllers\BaseController;
	use App\Models\Management\Company\ApplicationModel;
	use App\Models\Delivery\SellerDeliveryModel;
	use App\Models\DatabaseModel;
	use App\Models\Seller\IMJOBModel;
	
	class Delivery extends BaseController
	{
		private $model;
		private $database_model;
		private $seller_delivery_model;
		private $seller_model;
		
		public function __construct()
		{ //{{{
			$this->imjob_model = new IMJOBModel;
			$this->application_model = new ApplicationModel;
			$this->database_model = new DatabaseModel;
			$this->seller_delivery_model = new SellerDeliveryModel;
		} //}}}

		public function Status()
		{ // {{{
            if($_GET['cn'] != ""){
                $delivery = $this->seller_delivery_model->getDeliveryList($_GET);
                $contents = $this->seller_delivery_model->getContents($_GET);
                $data_page_total_cnt = count($delivery);
            }
            $uuid = $_SESSION['login_info']['uuid'];
            $contractList = $this->seller_delivery_model->getContractList($uuid);

            $data = array(
                "contractList" => $contractList
                ,"delivery" => $delivery
                ,"contents" => $contents
                ,"data_page_total_cnt" => $delivery["count"]

            );
			echo view("Common/Header.html");
			echo view('Seller/DeliveryStatus.html',$data);
			echo view("Common/Footer.html");
		} // }}}

        public function DeliverySubmit(){
                $result = $this->seller_delivery_model->Register($_FILES,$_POST);
                if($result != "") {
                    echo "
                <script>
                    alert('".$result."');
					window.location.replace('/Seller/DeliveryStatus/?cn=".$_POST["cn"]."');
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
        public function invoice(){
            $result = $this->seller_delivery_model->invoice($_POST);
            if($result == 1) {
                echo "
                <script>
                    alert('예약일이 등록되었습니다.');
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

        public function downloadFileNew(){
            $this->seller_delivery_model->downloadFileNew();
        }
	}
  ?>