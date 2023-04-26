<?php

namespace App\Controllers;
use App\Models\Seller\ItemModel;
use App\Models\Buyer\BuyerModel;
use App\Models\Seller\SellerModel;
class Home extends BaseController
{
    private $item_model;
    private $buyer_model;
    private $seller_model;

    public function __construct()
    { //{{{
        $this->buyer_model = new BuyerModel;
        $this->item_model = new ItemModel;
        $this->seller_model = new SellerModel;

    } //}}}

    public function index()
    {
        $value = $_GET["value"];
        $ranking = $this->buyer_model->RecommendationList($value);
        $new_product = $this->buyer_model->NewProductList();
        $reduction =  $this->buyer_model->ReductionMoney();
        $notice_list = $this->seller_model->getNoticeList();
        $promotion_video = $this->seller_model->getPromotionVideo();
        $data = array(
            "data" => $ranking["data"],
            "reduction" => $reduction,
            "new_product" => $new_product,
            "promotion_video" => $promotion_video,
            "notice_list" => $notice_list,

        );

        echo view("Common/Header.html");
        echo view('Home/Index.html', $data);
        echo view("Common/Footer.html");
    }
    public function SessionCategory(){
        $category = $this->item_model->SessionCategory();
        $data = array(
            "data" => $category
        );
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
}
