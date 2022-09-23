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
	
	public function InfoUpdate(){
		
		if(@$_SESSION["login"] != "success"){
			echo "
				<script>
                	alert('로그인 후 이용가능합니다');
                	location.href = '/Auth/SignIn';
				</script>
            ";
			
			die();
		}
		$pwdCheck = $this->sellerinfo_model->pwdCheck();
		if($pwdCheck == 1) {
			$result = $this->sellerinfo_model->infoUpdate($_FILES, $_POST);
			if ($result == 1) {
				echo "
				<script>
                	alert('수정되었습니다');
                	location.href = '/Seller';
				</script>
            ";
			} else {
				echo "
				<script>
                	alert('오류가 발생했습니다');
                	history.back();
				</script>
            ";
			}
		}else{
			echo "
				<script>
                	alert('현재 비밀번호를 다시 확인해주세요');
                	history.back();
				</script>
            ";
		}
	}

}