<?php

namespace App\Controllers\Buyer;
use App\Controllers\BaseController;
use App\Models\Management\Company\ApplicationModel;
use App\Models\DatabaseModel;
use App\Models\Delivery\buyerDeliveryModel;
use App\Models\Buyer\BuyerModel;
class Delivery extends BaseController
{

    private $model;
    private $database_model;
    private $buyer_model;
    private $buyer_delivery_model;

    public function __construct()
    { //{{{
        $this->buyer_delivery_model = new buyerDeliveryModel;
        $this->buyer_model = new BuyerModel;
        $this->database_model = new DatabaseModel;
    } //}}}

    public function index()
    {
        $data = $this->buyer_model->getProductList();


        $data = array(
            "data" => $data["data"]
        );
        echo view("Common/Header.html");
        echo view('Buyer/Index.html', $data);
        echo view("Common/Footer.html");
    }

    public function Status()
    { // {{{
        $notification_del = $this->buyer_model->NotificationDel();
        if($_GET['cn'] != ""){
            $delivery = $this->buyer_delivery_model->getDeliveryList($_GET);
            $contents = $this->buyer_delivery_model->getContents($_GET);
        }
        $uuid = $_SESSION['login_info']['uuid'];
        $contractList = $this->buyer_delivery_model->getContractList($uuid);
        $data = array(
            "contractList" => $contractList
        ,"delivery" => $delivery
        ,"contents" => $contents
        );

        echo view("Common/Header.html");
        echo view('MyPage/BuyerDeliveryStatus.html',$data);
        echo view("Common/Footer.html");
    } // }}}

    public function DeliveryStatusUpdate(){
        $status = $this->buyer_delivery_model->DeliveryStatus($_GET);

        if($status == 1){
            echo "
                <script>
                    alert('배송상태가 변경되었습니다.');
					window.location.replace('/Buyer/DeliveryStatus/?cn=".$_GET["cn"]."');
                </script>
            ";
        }else{
            echo "
                <script>
                    alert('오류가 발생했습니다 관리자에게 문의해주세요');
					window.location.replace('/Buyer/DeliveryStatus/?cn=".$_GET["cn"]."');
                </script>
            ";
        }
    }
    public function downloadFileNew(){
        $this->buyer_delivery_model->downloadFileNew();
    }
    
}
