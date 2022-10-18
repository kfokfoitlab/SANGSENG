<?php

namespace App\Controllers;
use App\Models\Management\Company\ApplicationModel;
use App\Models\CompanyModel;
use App\Models\DatabaseModel;
use App\Models\Buyer\BuyerModel;
class Buyer extends BaseController
{
    private $model;
    private $database_model;
    private $buyer_model;
    public function __construct()
    { //{{{
        $this->buyer_model = new BuyerModel;
        $this->database_model = new DatabaseModel;
    } //}}}

    public function index()
    {
        $uuid = $_SESSION['login_info']['uuid'];
        $value = $_GET["value"];
        $buyer_info = $this->buyer_model->Buyer_info($uuid);
        $data = $this->buyer_model->RecommendationList($value);
        $reduction =  $this->buyer_model->ReductionMoney();
        $buyer_reduction =  $this->buyer_model->BuyerReduction();
        $notification_del = $this->buyer_model->NotificationDel();
        $data = array(
            "data" => $data["data"],
            "reduction" => $reduction,
            "buyer_reduction" => $buyer_reduction,
            "buyer_info" => $buyer_info
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
