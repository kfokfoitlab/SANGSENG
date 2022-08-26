<?php

namespace App\Controllers\Seller;
use App\Controllers\BaseController;
use App\Models\CompanyModel;
use App\Models\DatabaseModel;
use App\Models\Management\Company\ApplicationModel;
use App\Models\Seller\SellerModel;

class Item extends BaseController
{
    private $model;
    private $database_model;
    private $company_model;
    private $seller_model;
//
    public function __construct()
    { //{{{
        $this->seller_model = new SellerModel;
        $this->application_model = new ApplicationModel;
        $this->database_model = new DatabaseModel;
        $this->company_model = new CompanyModel;
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
        $result = $this->seller_model->Register($_FILES, $_POST);

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
                    alert('오류가 발생했습니다.다시 시도해주세요');
					history.back(-1);
                </script>
            ";
        }
        die();
    } // }}}

}