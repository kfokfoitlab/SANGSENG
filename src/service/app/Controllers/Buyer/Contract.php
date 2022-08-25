<?php

namespace App\Controllers\Buyer;
use App\Controllers\BaseController;
use App\Models\Management\Company\ApplicationModel;
use App\Models\CompanyModel;
use App\Models\DatabaseModel;
use App\Models\Buyer\BuyerModel;
class Contract extends BaseController
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
        $data = $this->buyer_model->getProductList();


        $data = array(
            "data" => $data["data"]
        );
        echo view("Common/Header.html");
        echo view('Buyer/Index.html', $data);
        echo view("Common/Footer.html");
    }

    public function List()
    { // {{{

        echo view("Common/Header.html");
        echo view('Shop/List.html');
        echo view("Common/Footer.html");
    } // }}}

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

    public function Contract()
    { // {{{
        $result = $this->buyer_model->contract();


        echo view("Common/Header.html");
        echo view('MyPage/BuyerContract.html');
        echo view("Common/Footer.html");
    } // }}}

    public function Info()
    { // {{{

        echo view("Common/Header.html");
        echo view('MyPage/BuyerInfo.html');
        echo view("Common/Footer.html");
    } // }}}

    public function Cart()
    { // {{{
        echo view("Common/Header.html");
        echo view('MyPage/BuyerCart.html');
        echo view("Common/Footer.html");
    } // }}}
}
