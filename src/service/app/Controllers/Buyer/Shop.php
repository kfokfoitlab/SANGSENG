<?php

namespace App\Controllers\Buyer;
use App\Controllers\BaseController;
use App\Models\Management\Company\ApplicationModel;
use App\Models\CompanyModel;
use App\Models\DatabaseModel;
use App\Models\Buyer\BuyerModel;
class Shop extends BaseController
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
        $value = $_GET["value"];
        $uuid = $_SESSION["login_info"]["uuid"];
        $ranking = $this->buyer_model->RecommendationList();
        $list = $this->buyer_model->CategoryList($value);
        $buyer_info = $this->buyer_model->Buyer_info($uuid);
        $data = array(
            "ranking" => $ranking["data"],
            "list" => $list["data"],
            "buyer_info" => $buyer_info
        );


        echo view("Common/Header.html");
        echo view('Shop/List.html',$data);
        echo view("Common/Footer.html");
    } // }}}

    public function Detail($product_no)
    { // {{{
        $data = $this->buyer_model->productDetail($product_no);
        $data = array(
            "data" => $data
        ,"reduction_money" => $_GET["rm"]
        );
        echo view("Common/Header.html");
        echo view('Shop/Detail.html',$data);
        echo view("Common/Footer.html");
    } // }}}

    public function Contract()
    { // {{{

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
        $result = $this->buyer_model->CartInsert($_POST);
        if($result == "1") {
            echo "
                <script>
                    alert('장바구니에 담았습니다.');
					window.location.replace('/Buyer');
                </script>
            ";
        }else{
            echo "
                <script>
                    alert('실패했습니다');
					history.back(-1);
                </script>
            ";
        }
        die();
    } // }}}
}
