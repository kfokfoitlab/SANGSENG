<?php

namespace App\Controllers\Seller;
use App\Controllers\BaseController;
use App\Models\Seller\ItemModel;
use App\Models\DatabaseModel;
use App\Models\Seller\SellerInfoModel;

class Mypage extends BaseController
{
    private $item_model;
    private $database_model;
    private $seller_model;
    private $item_per_page = 10;
//
    public function __construct()
    { //{{{
        $this->item_model = new ItemModel;
        $this->sellerinfo_model = new SellerInfoModel;
        $this->database_model = new DatabaseModel;
    } //}}}
	
	public function Info()
	{
        $uuid = $_SESSION["login_info"]["uuid"];
        $data = $this->sellerinfo_model->getMyInfo($uuid);

        $data = array(
            "data" => $data,
        );

		echo view("Common/Header.html");
		echo view('MyPage/SellerInfo.html',$data);
		echo view("Common/Footer.html");
	}
	
	public function ConfirmPassword()
	{ // {{{
		echo view("Common/Header.html");
		echo view('MyPage/SellerPasswordConfirm.html');
		echo view("Common/Footer.html");
	} // }}}
	
	public function ChangePassword()
	{ // {{{
		echo view("Common/Header.html");
		echo view('MyPage/SellerPasswordChange.html');
		echo view("Common/Footer.html");
	} // }}}

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

}