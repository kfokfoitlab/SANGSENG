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
    private $item_per_page = 10;
//
    public function __construct()
    { //{{{
        $this->item_model = new ItemModel;
        $this->seller_model = new SellerModel;
        $this->application_model = new ApplicationModel;
        $this->database_model = new DatabaseModel;
    } //}}}

    public function Search(){
     $uuid = $_SESSION['login_info']['uuid'];
        $data = $this->seller_model->searchProductList($uuid);
        $data = array(
            "search" => $data["data"]
        );
        echo view("Common/Header.html");
        echo view('Seller/ItemList.html',$data);
        echo view("Common/Footer.html");
    }
    public function ItemList()
    { // {{{

        $page = (@$_GET["page"])?$_GET["page"] : 1;
        /*$this->item_per_page = (@$_GET["length"])?$_GET["length"]:$this->item_per_page;

        $page_query = array(
        "page" => $page
        ,"length" => $this->item_per_page
        );*/

        $uuid = $_SESSION['login_info']['uuid'];
        $data = $this->seller_model->getProductList($uuid);
        $excel = $this->seller_model->getExcelData($uuid);
        $data_cnt = $this->seller_model->getProductCount($uuid);
        $data_page_total_cnt = count($data);
        $data = array(
            "data" => $data["data"],
            "excel" => $excel,
            "data_cnt" => $data_cnt,
            "data_page_total_cnt" => $data["count"]
        );
        echo view("Common/Header.html");
        echo view('Seller/ItemList.html',$data);
        echo view("Common/Footer.html");
    } // }}}
    public function ItemRegist()
    { // {{{

        $data = $this->item_model->SellerInfo();
        $data = array(
            "data" => $data
        );
        echo view("Common/Header.html");
        echo view('Seller/ItemRegist.html',$data);
        echo view("Common/Footer.html");
    } // }}}

    public function ItemSubmit()
    { // {{{
        $result = $this->item_model->Register($_FILES, $_POST);

        if($result == "1") {
            echo "
                <script>
                    alert('상품이 등록되었습니다.');
					window.location.replace('/Seller/Item/ItemList');
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
                    alert('수정사항을 관리자가 검토중입니다.');
					window.location.replace('/Seller/Item/ItemList');
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
    public function ItemDelete()
    {
        $result = $this->item_model->ItemDelete($_GET);
        if ($result == "1") {
            echo "
                <script>
                    alert('관리자에게 삭제를 요청했습니다..');
					window.location.replace('/Seller/Item/ItemList');
                </script>
            ";
        } else {
            echo "
                <script>
                    alert('오류가 발생했습니다. 관리자에게 문의해주세요');
					history.back(-1);
                </script>
            ";
        }
    }

	public function StatusComment(){
		echo view('/Seller/StatusComment.html');
	}
}