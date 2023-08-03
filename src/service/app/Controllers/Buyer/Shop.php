<?php

namespace App\Controllers\Buyer;
use App\Controllers\BaseController;
use App\Models\Seller\ItemModel;
use App\Models\DatabaseModel;
use App\Models\Buyer\BuyerModel;
class Shop extends BaseController
{
    private $model;
    private $item_model;
    private $database_model;
    private $buyer_model;
    public function __construct()
    { //{{{
        $this->buyer_model = new BuyerModel;
        $this->item_model = new ItemModel;
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
        $ranking = $this->buyer_model->RecommendationList($value);
        $list = $this->buyer_model->CategoryList($value);
        $data_page_total_cnt = count($list);

        $data = array(
            "ranking" => $ranking["data"],
            "list" => $list["data"],
            "listReplyCount" => $list["replyCount"],
            "data_page_total_cnt" => $list["count"],
            "rankingReplyCount" => $ranking["replyCount"],

        );
        echo view("Common/Header.html");
        echo view('Shop/List.html',$data);
        echo view("Common/Footer.html");
    } // }}}

    public function Detail($product_no)
    { // {{{
        $data = $this->buyer_model->productDetail($product_no);
        $reply_data = $this->buyer_model->SellerReplyList($product_no);
        $replyCount = $this->buyer_model->SellerReplyCount($product_no);
        $data = array(
            "data" => $data
        ,"reply_data" => $reply_data
        ,"reduction_money" => $_GET["rm"]
        ,"replyCount" => $replyCount
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

        $cart_Check = $this->buyer_model->cartCheck($_POST);
        if($cart_Check){
            echo "
                <script>
                    alert('이미 장바구니에 있습니다.');
                    history.back();
                </script>
            ";

            die();
        }
        $result = $this->buyer_model->CartInsert($_POST);
        if($result == "1") {
            echo "
                <script>
                    alert('장바구니에 담았습니다.');
					history.back();
                </script>
            ";
        }else{
            echo "
                <script>
                    alert('오류가 발생했습니다. 관리자에게 문의해주세요');
					history.back(-1);
                </script>
            ";
        }
        die();
    } // }}}

    public function SellerReplySubmit(){
        if($_POST["reply_step"] == 1) {
            $replyCheck = $this->buyer_model->SellerReplyCheck($_POST);
            $replyCountCheck = $this->buyer_model->SellerReplyCountCheck($_POST);
            if($replyCheck == 0){
                echo "2";
                die();
            }elseif ($replyCountCheck > 0){
                echo "3";
                die();
            }
        }elseif ($_POST["reply_step"] == 2){
            $reReplyCheck = $this->buyer_model->SellerReReplyCheck($_POST);
            if($reReplyCheck == 0){
                echo "4";
                die();
            }
        }

        $result = $this->buyer_model->SellerReplyReg($_POST);
        if ($result == 1) {
            echo "1";
        } else {
            echo "0";
        }

    }
    public function SellerReplyDelete(){

        $result = $this->buyer_model->SellerReplyDelete($_POST);
        echo $result;
    }

    public function SellerReplyUpdate(){
        $result = $this->buyer_model->SellerReplyUpdate($_POST);
        echo $result;
    }
}

