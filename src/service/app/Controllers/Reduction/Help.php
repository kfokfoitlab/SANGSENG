<?php

namespace App\Controllers\Reduction;
use App\Controllers\BaseController;

use App\Models\DatabaseModel;
use App\Models\Buyer\MyPageModel;
use App\Models\Reduction\ReductionModel;
class Help extends BaseController
{

    private $model;
    private $reduction_model;
    private $database_model;
    private $mypage_model;
    public function __construct()
    { //{{{
        $this->reduction_model = new ReductionModel;
        $this->mypage_model = new MyPageModel;
        $this->database_model = new DatabaseModel;
    } //}}}

    public function index()
    {
        if($_SESSION['login_info']['type'] != "buyer" ){
            echo "
        <script>
        alert('구매기업만 이용 가능합니다.');
        history.back();
        </script>
        ";
        }


        if($_GET['cn'] != ""){
        $seller_info = $this->reduction_model->getdownloadList($_GET);
        $buyer_info = $this->reduction_model->getBuyerdownloadList($_GET);
        $workflow = $this->reduction_model->getWorkflowId($_GET);
    }
        $data = $this->reduction_model->getdocumentList();
        $data = array(
            "data" => $data
            ,"seller_info" =>$seller_info
            ,"buyer_info" =>$buyer_info
            ,"workflow" =>$workflow
        );

        echo view("Common/Header.html");
        echo view('Reduction/Help.html',$data);
        echo view("Common/Footer.html");
    }

    public function downloadFileNew(){
        $this->reduction_model->downloadFileNew();
    }
    public function ProvisionUpload(){
       $provision = $this->reduction_model->provisionUpload($_FILES,$_POST);
       if($provision == 1){
           echo "
                <script>
                    alert('업로드 완료.');
					window.location.replace('/Reduction/Help/?cn=".$_POST["cn"]."');
                </script>
            ";
       }else{
           echo "
                <script>
                    alert('업로드 실패.');
					window.location.replace('/Reduction/Help');
                </script>
            ";
       }

    }

}
