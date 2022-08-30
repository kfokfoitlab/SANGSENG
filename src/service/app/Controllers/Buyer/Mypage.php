<?php

namespace App\Controllers\Buyer;
use App\Controllers\BaseController;

use App\Models\DatabaseModel;
use App\Models\Buyer\MyPageModel;
class MyPage extends BaseController
{
    private $model;
    private $database_model;
    private $mypage_model;
    public function __construct()
    { //{{{
        $this->mypage_model = new MyPageModel;
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
    public function Contract()
    { // {{{

        echo view("Common/Header.html");
        echo view('MyPage/BuyerContract.html');
        echo view("Common/Footer.html");
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
        $data = $this->mypage_model->getCartList();
        $data = array(
            "data" => $data
        );
        echo view("Common/Header.html");
        echo view('MyPage/BuyerCart.html',$data);
        echo view("Common/Footer.html");
    } // }}}
    public function BuyerUpdateSubmit(){
        $password = $_POST["password"];
        $pwdCheck =  $this->mypage_model->pwdCheck($password);
        if($pwdCheck == 1){
            $result = $this->mypage_model->updateMyInfo($_POST);
            if($result == "1") {
                echo "
                <script>
                    alert('������ ����Ǿ����ϴ�.');
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
        }else{
            echo "
                <script>
                    alert('��й�ȣ�� ��ġ���� �ʽ��ϴ�.');
					window.location.replace('/Buyer');
                </script>
            ";
        }

    }
}
