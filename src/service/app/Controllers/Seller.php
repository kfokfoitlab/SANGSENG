<?php

namespace App\Controllers;
use App\Models\Seller\SellerModel;
use App\Models\Auth\SignInModel;
use App\Models\Buyer\MyPageModel;
class Seller extends BaseController
{

    private $seller_model;
	private $sigin_model;
    private $mypage_model;
    public function __construct()
    { //{{{
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
        $disabledCount = $this->seller_model->getDisabledCount($uuid);
       $data = array(
            "totalSales" => $totalSales
        ,"expectationSales" => $expectationSales
       ,"completionContract" =>  $completionContract
       ,"contractList" =>  $contractList
       ,"disabledCount" =>  $disabledCount
        );
	    $_SESSION["disabledCount"] = $this->sigin_model->getWorkerCount();
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

        $uuid = $_SESSION['login_info']['uuid'];
        $result = $this->mypage_model->ContractStatus($_POST);
        $data = $this->seller_model->getContractList($uuid);
        $data_cnt = $this->seller_model->getContractCount($uuid);
        $data = array(
            "data" => $data["data"],
            "data_cnt" => $data_cnt,
    );
        echo view("Common/Header.html");
        echo view('Seller/Contract.html',$data);
        echo view("Common/Footer.html");
    } // }}}

}
