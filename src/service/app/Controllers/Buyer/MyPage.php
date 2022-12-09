<?php

namespace App\Controllers\Buyer;
use App\Controllers\BaseController;
use App\Models\Auth\SignInModel;
use App\Models\Buyer\MyPageModel;
class MyPage extends BaseController
{
    private $sigin_model;
    private $mypage_model;
    public function __construct()
    { //{{{
        $this->mypage_model = new MyPageModel;
        $this->sigin_model = new SignInModel;

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
    public function Contract()
    { // {{{
         $uuid = $_SESSION['login_info']['uuid'];
         $data = $this->mypage_model->getContractList($uuid);
        $data = array(
                "data" => $data["data"]
                ,"playing" => $data["playing"]
                ,"complete" => $data["complete"]
                ,"price" => $data["price"]
                ,"reduction" => $data["reduction"]
                ,"data_page_total_cnt" => $data["count"]
            );
            echo view("Common/Header.html");
            echo view('MyPage/BuyerContract.html',$data);
            echo view("Common/Footer.html");
        }
    public function ContractUpdate()
    { // {{{

        $result = $this->mypage_model->buyerContractStatus($_POST);
        if($result == 1 ){
            $_SESSION["Contract"]= $this->sigin_model->getContractList();
            $_SESSION["ReductionMoney"]= $this->sigin_model->BuyerReduction();
            }
        } // }}}

    public function Info()
    { // {{{
        $uuid = $_SESSION["login_info"]["uuid"];
        $data = $this->mypage_model->getMyInfo($uuid);
        $data = array(
            "data" => $data,
        );
        echo view("Common/Header.html");
        echo view('MyPage/BuyerInfo.html',$data);
        echo view("Common/Footer.html");
    } // }}}

    public function Cart()
    { // {{{
        $uuid = $_SESSION["login_info"]["uuid"];
        $data = $this->mypage_model->getCartList($uuid);

        $data = array(
            "data" => $data
            ,"cnt" => $data["count"]
        );
        echo view("Common/Header.html");
        echo view('MyPage/BuyerCart.html',$data);
        echo view("Common/Footer.html");
    } // }}}
    public function BuyerUpdateSubmit(){
        $password = $_POST["password"];
        $pwdCheck =  $this->mypage_model->pwdCheck($password);
        if($pwdCheck == 1){
            $result = $this->mypage_model->updateMyInfo($_FILES,$_POST);

            if($result == "1") {
                $_SESSION["buyer_info"] = $this->sigin_model->getBuyerinfo();
                $_SESSION["Contract"]=  $this->sigin_model->getContractList();
                $_SESSION["ReductionMoney"]= $this->sigin_model->BuyerReduction();
                echo "
                <script>
                    alert('회원님의 정보가 변경 되었습니다.');
					window.location.replace('/Buyer/MyPage/Info');
                </script>
            ";
            }else{
                echo "
                <script>
                    alert('정보수정에 실패했습니다.');
					history.back(-1);
                </script>
            ";
            }
        }else{
            echo "
                <script>
                    alert('비밀번호가 일치하지 않습니다.');
					window.location.replace('/Buyer/MyPage/Info');
                </script>
            ";
        }

    }

    public function CartDel(){
        $idx = $_GET["idx"];
        $result =  $this->mypage_model->CartDel($idx);
        if($result == "1") {
            echo "
                <script>
                    alert('삭제되었습니다.');
					window.location.replace('/Buyer/MyPage/Cart');
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
    }
    public function ConfirmPassword()
    { // {{{
        echo view("Common/Header.html");
        echo view('MyPage/BuyerPasswordConfirm.html');
        echo view("Common/Footer.html");
    } // }}}

    public function ChangePassword()
    { // {{{
        $password = $_POST["password"];
        $result = $this->mypage_model->pwdCheck($password);

        if($result == 1){
            echo "
                <script>
                    alert('새로운 비밀번호를 입력해주세요.');
                </script>
            ";
            echo view("Common/Header.html");
            echo view('MyPage/BuyerPasswordChange.html');
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
    public function BuyerPwdSubmit(){
        $uuid = $_SESSION['login_info']['uuid'];
        $result =  $this->mypage_model->PwdUpdate($uuid);
        if($result == "1") {
            echo "
                <script>
                    alert('비밀번호가 변경되었습니다.');
					window.location.replace('/Buyer');
                </script>
            ";
        }else{
            echo "
                <script>
                    alert('오류가 발생했습니다.다시 시도해주세요');
					window.location.replace('/Buyer/MyPage/ConfirmPassword');
                </script>
            ";
        }
    }
    public function downloadFileNew(){
        $this->mypage_model->downloadFileNew();
    }
}
