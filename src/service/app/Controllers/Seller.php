<?php

namespace App\Controllers;
use App\Models\Seller\SellerModel;
use App\Models\Auth\SignInModel;
use App\Models\Buyer\MyPageModel;
use App\Models\CS\QuestionsModel;

class Seller extends BaseController
{
    private $questions_model;
    private $seller_model;
	private $sigin_model;
    private $mypage_model;
    public function __construct()
    { //{{{
        $this->questions_model = new QuestionsModel;
        $this->mypage_model = new MyPageModel;
        $this->seller_model = new SellerModel;
	    $this->sigin_model = new SignInModel;
    } //}}}
    public function index()
    {
        $uuid = $_SESSION['login_info']["uuid"];
        $totalSales = $this->seller_model->getTotalSales($uuid);
        $expectationSales = $this->seller_model->getexpectationSales($uuid);
        $completionContract = $this->seller_model->getCompletionContract($uuid);
        $contractList = $this->seller_model->getContract($uuid);
       // $disabledCount = $this->seller_model->getDisabledCount($uuid);
        $questions = $this->seller_model->getQuestionsList();
	    $product_reply = $this->seller_model->getProductreplyList();
	    $notice_list = $this->seller_model->getNoticeList();
        $_SESSION["totalSales"] = $this->sigin_model->getTotalSales($uuid);
        $_SESSION["expectationSales"] = $this->sigin_model->getexpectationSales($uuid);
        $_SESSION["completionContract"] = $this->sigin_model->getCompletionContract($uuid);
        $_SESSION["disabledCount"] = $this->sigin_model->getWorkerCount();
        $_SESSION["sellerinfo"] = $this->sigin_model->Sellerinfo();
        $data = array(
            "totalSales" => $totalSales
        ,"expectationSales" => $expectationSales
       ,"completionContract" =>  $completionContract
       ,"contractList" =>  $contractList
     //  ,"disabledCount" =>  $disabledCount
        ,"questions" =>  $questions
	        ,"product_reply" => $product_reply
	        ,"notice_list" => $notice_list
        );

        echo view("Common/Header.html");
        echo view('Seller/Index.html', $data);
        echo view("Common/Footer.html");
    }
    public function ItemUpdate()
    { // {{{
        echo view("Common/Header.html");
        echo view('Seller/ItemUpdate.html');
        echo view("Common/Footer.html");
    } // }}}

    public function Contract()
    {
        $notification_del = $this->seller_model->NotificationDel();
        $uuid = $_SESSION['login_info']['uuid'];
        $data = $this->seller_model->getContractList($uuid);
        $data_cnt = $this->seller_model->getContractCount($uuid);
        $data = array(
            "data" => $data["data"],
            "data_cnt" => $data_cnt
    );
        echo view("Common/Header.html");
        echo view('Seller/Contract.html',$data);
        echo view("Common/Footer.html");
    } // }}}

    public function ContractUpdate(){
        $uuid = $_SESSION['login_info']['uuid'];
        $result = $this->mypage_model->ContractStatus($_POST);
        $_SESSION["totalSales"] = $this->sigin_model->getTotalSales($uuid);
        echo "
        <script>
           alert('최신화되었습니다');
          location.href = '/Seller/Contract';
        </script>
        ";
    }


}
