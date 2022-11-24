<?php

namespace App\Controllers;
use App\Models\Auth\SignInModel;
use App\Models\Seller\SellerModel;
use App\Models\DatabaseModel;
use App\Models\Buyer\BuyerModel;
class Buyer extends BaseController
{
    private $sigin_model;
    private $seller_model;
    private $database_model;
    private $buyer_model;
    public function __construct()
    { //{{{
        $this->buyer_model = new BuyerModel;
        $this->database_model = new DatabaseModel;
        $this->sigin_model = new SignInModel;
        $this->seller_model = new SellerModel;
    } //}}}

    public function index()
    {
        $uuid = $_SESSION['login_info']['uuid'];
        $value = $_GET["value"];
        $notice_list = $this->seller_model->getNoticeList();
        $buyer_info = $this->buyer_model->Buyer_info($uuid);
        $data = $this->buyer_model->RecommendationList($value);
        $reduction =  $this->buyer_model->ReductionMoney();
        $buyer_reduction =  $this->buyer_model->BuyerReduction();
        $new_product = $this->buyer_model->NewProductList();
        $notification_del = $this->buyer_model->NotificationDel();
        $_SESSION["Contract"]= $this->sigin_model->getContractList();
        $_SESSION["ReductionMoney"]= $this->sigin_model->BuyerReduction();

        $data = array(
            "data" => $data["data"],
            "reduction" => $reduction,
            "buyer_reduction" => $buyer_reduction,
            "buyer_info" => $buyer_info,
            "new_product" => $new_product,
            "notice_list" => $notice_list
        );
        echo view("Common/Header.html");
        echo view('Buyer/Index.html', $data);
        echo view("Common/Footer.html");
    }


    public function Detail($product_no)
    { // {{{
        $data = $this->buyer_model->productDetail($product_no);
        $data = array(
            "data" => $data
        );
        echo view("Common/Header.html");
        echo view('Shop/Detail.html',$data);
        echo view("Common/Footer.html");
    } // }}}

/*    public function Contract()
    { // {{{

        echo view("Common/Header.html");
        echo view('MyPage/BuyerContract.html');
        echo view("Common/Footer.html");
    } // }}}*/

   /* public function Info()
    { // {{{

        echo view("Common/Header.html");
        echo view('MyPage/BuyerInfo.html');
        echo view("Common/Footer.html");
    } // }}}*/

    public function Cart()
    { // {{{

        echo view("Common/Header.html");
        echo view('MyPage/BuyerCart.html');
        echo view("Common/Footer.html");
    } // }}}
}
