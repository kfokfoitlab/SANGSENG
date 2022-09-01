<?php

namespace App\Controllers;
use App\Models\Seller\SellerModel;
class Seller extends BaseController
{

    private $seller_model;

    public function __construct()
    { //{{{
        $this->seller_model = new SellerModel;
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
    { // {{{
        $uuid = $_SESSION['login_info']['uuid'];
        $data = $this->seller_model->getContractList($uuid);

        $data = array(
            "data" => $data["data"]
    );
        echo view("Common/Header.html");
        echo view('Seller/Contract.html',$data);
        echo view("Common/Footer.html");
    } // }}}

}
