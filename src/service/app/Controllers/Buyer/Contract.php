<?php

namespace App\Controllers\Buyer;
use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use App\Models\Buyer\BuyerModel;
class Contract extends BaseController
{
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
        $result = $this->buyer_model->contract($_POST);
        if($result == "1") {
            echo "
                <script>
                    alert('�����ڰ����� �����û�� �߽��ϴ�.');
					window.location.replace('/Buyer');
                </script>
            ";
        }else{
            echo "
                <script>
                    alert('������ �߻��߽��ϴ�.�ٽ� �õ����ּ���');
					history.back(-1);
                </script>
            ";
        }
        die();
    } // }}}
}
