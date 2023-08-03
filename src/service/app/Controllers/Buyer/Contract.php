<?php

namespace App\Controllers\Buyer;
use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use App\Models\Buyer\BuyerModel;
use App\Models\Auth\SignInModel;

class Contract extends BaseController
{
    private $sigin_model;
    private $database_model;
    private $buyer_model;

    public function __construct()
    { //{{{
        $this->sigin_model = new SignInModel;
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
        $contract_Check = $this->buyer_model->contract_Check($_GET);
        if($contract_Check){
            if($_GET['ctype'] == 'detail') {
                echo "
                <script>
                    alert('이미 계약중인 상품입니다.');
                    window.location.replace('/Buyer/Shop/Detail/" . $_GET["product_no"] . "');
                </script>
                
            ";
            }
            if($_GET['ctype'] == 'cart'){
                echo "
                <script>
                    alert('이미 계약중인 상품입니다.');
					window.location.replace('/Buyer/MyPage/Cart');
                </script>
            ";
            }
            die();
        }
        $cart_del = $this->buyer_model->cartDel($_GET);
        $result = $this->buyer_model->contract($_GET);
        if($result == "1") {
            $_SESSION["Contract"]= $this->sigin_model->getContractList();
            if($_GET['ctype'] == 'detail'){
            echo "
                <script>
                    alert('관리자에게 검토요청을 보냈습니다.');
					window.location.replace('/Buyer/Shop/Detail/".$_GET["product_no"]."');
                </script>
            ";
            }
            if($_GET['ctype'] == 'cart'){
                echo "
                <script>
                    alert('관리자에게 검토요청을 보냈습니다.');
					window.location.replace('/Buyer/MyPage/Cart');
                </script>
            ";
            }
        }else{
            echo "
                <script>
                    alert('실패.');
					history.back(-1);
                </script>
            ";
        }
        die();
    }
}
