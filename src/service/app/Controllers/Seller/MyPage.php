<?php

namespace App\Controllers\Seller;
use App\Controllers\BaseController;
use App\Models\CommonModel;
use App\Models\Seller\ItemModel;
use App\Models\DatabaseModel;
use App\Models\Seller\SellerInfoModel;
use App\Models\Seller\SellerModel;
use App\Models\Auth\SignInModel;
class Mypage extends BaseController
{
    private $item_model;
    private $database_model;
    private $seller_model;
    private $common_model;
    private $sigin_model;
//
    public function __construct()
    { //{{{
        $this->common_model = new CommonModel;
        $this->item_model = new ItemModel;
        $this->seller_model = new SellerModel;
        $this->sellerinfo_model = new SellerInfoModel;
        $this->database_model = new DatabaseModel;
        $this->sigin_model = new SignInModel;

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

    public function PasswordCheck()
    { // {{{
        $uuid = $_SESSION["login_info"]["uuid"];
        $result = $this->sellerinfo_model->pwdCheck($uuid);
        if($result == 1){
            echo "
                <script>
                    alert('새로운 비밀번호를 입력해주세요.');
                </script>
            ";
            echo view("Common/Header.html");
            echo view('MyPage/SellerPasswordChange.html');
            echo view("Common/Footer.html");
        }else{
            echo "
                <script>
                    alert('비밀번호가 일치하지 않습니다.');
					history.back(-1);
                </script>
            ";
        }
    } // }}}

    public function ChangePassword()
	{ // {{{
        $uuid = $_SESSION['login_info']['uuid'];
        $result =  $this->sellerinfo_model->PwdUpdate($uuid);
        if($result == "1") {
            echo "
                <script>
                    alert('비밀번호가 변경되었습니다.');
					window.location.replace('/Seller');
                </script>
            ";
        }else{
            echo "
                <script>
                    alert('오류가 발생했습니다.다시 시도해주세요');
					window.location.replace('/Seller');
                </script>
            ";
        }
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
		$uuid = $_SESSION['login_info']['uuid'];
		if(@$_SESSION["login"] != "success"){
			echo "
				<script>
                	alert('로그인 후 이용가능합니다');
                	location.href = '/Auth/SignIn';
				</script>
            ";
			
			die();
		}
		$pwdCheck = $this->sellerinfo_model->pwdCheck($uuid);
		if($pwdCheck == 1) {
			$result = $this->sellerinfo_model->infoUpdate($_FILES, $_POST);
			if ($result == 1) {
                $_SESSION["totalSales"] = $this->sigin_model->getTotalSales($uuid);
                $_SESSION["sellerinfo"] = $this->sigin_model->Sellerinfo();
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
    public function downloadFileNew(){
        $this->seller_model->downloadFileNew();
    }
}