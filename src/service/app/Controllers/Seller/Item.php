<?php

namespace App\Controllers\Seller;
use App\Controllers\BaseController;
use App\Models\Seller\ItemModel;
use App\Models\DatabaseModel;
use App\Models\Management\Company\ApplicationModel;
use App\Models\Seller\SellerModel;

class Item extends BaseController
{
    private $item_model;
    private $database_model;
    private $seller_model;
//
    public function __construct()
    { //{{{
        $this->item_model = new ItemModel;
        $this->seller_model = new SellerModel;
        $this->application_model = new ApplicationModel;
        $this->database_model = new DatabaseModel;
    } //}}}

    public function ItemList()
    { // {{{
        $uuid = $_SESSION['login_info']['uuid'];
        $data = $this->seller_model->getProductList($uuid);

        $data = array(
            "data" => $data["data"]
        );
        echo view("Common/Header.html");
        echo view('Seller/ItemList.html',$data);
        echo view("Common/Footer.html");
    } // }}}
    public function ItemRegist()
    { // {{{
        echo view("Common/Header.html");
        echo view('Seller/ItemRegist.html');
        echo view("Common/Footer.html");
    } // }}}

    public function ItemSubmit()
    { // {{{
        header("Content-Type:text/html;charset=UTF-8");
        $result = $this->item_model->Register($_FILES, $_POST);

        if($result == "1") {
            echo "
                <script>
                    alert('상품이 등록되었습니다.');
					window.location.replace('/Seller');
                </script>
            ";
        }else{
            echo "
                <script>
                    alert('상품등록에 실패했습니다.');
					history.back(-1);
                </script>
            ";
        }
        die();
    } // }}}

    public function ItemUpdate($product_no)
    { // {{{
        $uuid = $_SESSION['login_info']['uuid'];
        $data = $this->seller_model->itemDetail($uuid,$product_no);

        $data = array(
            "data" => $data["data"]
        );
        echo view("Common/Header.html");
        echo view('Seller/ItemUpdate.html',$data);
        echo view("Common/Footer.html");
    } // }}}
    public function ItemUpdateSubmit()
    { // {{{

        $result = $this->item_model->ItemUpdateSubmit($_FILES, $_POST);
        if($result == "1") {
            echo "
                <script>
                    alert('상품이 수정되었습니다.');
					window.location.replace('/Seller');
                </script>
            ";
        }else{
            echo "
                <script>
                    alert('상품이 수정에 실패했습니다');
					history.back(-1);
                </script>
            ";
        }

    }
}