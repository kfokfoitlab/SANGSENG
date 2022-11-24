<?php

namespace App\Controllers;
use App\Models\Management\Company\ApplicationModel;
use App\Models\DatabaseModel;
use App\Models\Buyer\BuyerModel;
use App\Models\Seller\SellerModel;
class Home extends BaseController
{
    private $database_model;
    private $buyer_model;
    private $seller_model;

    public function __construct()
    { //{{{
        $this->buyer_model = new BuyerModel;
        $this->application_model = new ApplicationModel;
        $this->database_model = new DatabaseModel;
        $this->seller_model = new SellerModel;

    } //}}}

    public function index()
    {
        $value = $_GET["value"];
        $ranking = $this->buyer_model->RecommendationList($value);
        $new_product = $this->buyer_model->NewProductList();
        $reduction =  $this->buyer_model->ReductionMoney();
        $notice_list = $this->seller_model->getNoticeList();
        $data = array(
            "data" => $ranking["data"],
            "reduction" => $reduction,
            "new_product" => $new_product,
            "notice_list" => $notice_list

        );

        echo view("Common/Header.html");
        echo view('Home/Index.html', $data);
        echo view("Common/Footer.html");
    }
}
