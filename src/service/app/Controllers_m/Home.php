<?php

namespace App\Controllers_m;
use App\Models\Management\Company\ApplicationModel;
use App\Models\DatabaseModel;
use App\Models_m\Buyer\BuyerModel;
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
        $reduction =  $this->buyer_model->ReductionMoney();
        $notice_list = $this->seller_model->getNoticeList();
        $promotion_video = $this->seller_model->getPromotionVideo();
        $data = array(
            "data" => $ranking["data"],
            "reduction" => $reduction,
            "promotion_video" => $promotion_video,
            "notice_list" => $notice_list
        );

      //  echo view("Common/Header.html");
        echo view('Mobile/Home/Index.html');
     //   echo view("Common/Footer.html");

    }

    public function List()
    {
        $new_product = $this->buyer_model->NewProductList();

        $data = array(
            "new_product" => $new_product,
        );

        //  echo view("Common/Header.html");
        echo view('Mobile/Auth/List.html',$data);
        //   echo view("Common/Footer.html");

    }
}
