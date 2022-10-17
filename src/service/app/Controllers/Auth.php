<?php

namespace App\Controllers;
use App\Models\Auth\SignInModel as Model;
use App\Models\Auth\SignUpUserModel as UserModel;
use App\Models\Auth\SignUpCompanyModel as CompanyModel;
use App\Models\Auth\ForgotInfoModel as ForgotModel;
use App\Models\DatabaseModel;

class Auth extends BaseController
{

    private $model;
    private $user_model;
    private $company_model;
	private $forgotModel_model;
    private $database_model;
	
    public function __construct()
    { //{{{
        $this->model = new Model;
        $this->user_model = new UserModel;
        $this->company_model = new CompanyModel;
	    $this->forgotModel_model = new ForgotModel;
        $this->database_model = new DatabaseModel;
    } //}}}

    public function index()
    {
        header("Location:/Auth/SignIn");
    }

    public function SignIn()
    { // {{{

        session_destroy();
/*
        $remember_id = (@$_COOKIE["remember_id"])? $_COOKIE["remember_id"] : "";
        $remember_me = (@$_COOKIE["remember_me"])? $_COOKIE["remember_me"] : "";
        $data = array(
             "remember_me" => $remember_me
            ,"remember_id" => $remember_id
        );*/

        echo view("Common/Header.html");
        echo view('Auth/SignIn.html');
        echo view("Common/Footer.html");

    } // }}}

    public function SignInSubmit()
    { //{{{
        $email = $_POST["email"];
        $password = $_POST["password"];
        $company_type = $_POST["company_type"];
        $result = $this->model->SignIn($email, $password,$company_type);

        if($result["result"] == "success"){
            if($_SESSION["login_info"]["type"] == 'buyer') {
                switch ($_SESSION["login_info"]["status"]) {
                    case "0" :
                    case "1" :
                        header("Location:/Auth/SignInCompanyReport/review");
                        break;
                    case "5" : // OK
                        if($result['buyer_notification'] >0){
                            echo "
                         <script>
                            alert('배송현황이 변경되었습니다 확인해주세요.');
                            window.location.replace('/Buyer/DeliveryStatus');
                        </script>
                      ";
                            break;
                        }else{
                            header("Location:/Buyer");
                            break;
                        }
                    case "7" :
                        header("Location:/Auth/SignInCompanyReport/reject");
                        break;
                    case "9" :
                        header("Location:/Auth/SignInCompanyReport/delete");
                        break;
                }
            }else{
                switch ($_SESSION["login_info"]["status"]) {
                    case "0" :
                    case "1" :
                        header("Location:/Auth/SignInCompanyReport/review");
                        break;
                    case "5" : // OK
                        if($result['seller_notification'] >0){
                            echo "
                         <script>
                            alert('새로운 계약서가 등록되었습니다.');
                            window.location.replace('/Seller/Contract');
                        </script>
                      ";
                            break;
                        }else{
                            header("Location:/Seller");
                            break;
                        }
                    case "7" :
                        header("Location:/Auth/SignInCompanyReport/reject");
                        break;
                    case "9" :
                        header("Location:/Auth/SignInCompanyReport/delete");
                        break;
                }
            }
        }
        else {
            echo "
                <script>
                    alert('이메일 주소, 비밀번호가 맞지 않습니다.');
                    window.location.replace('/Auth/SignIn');
                </script>
            ";
        }

        die();
    } //}}}

    public function SignInCompanyReport($type = null)
    { //{{{
        session_destroy();

        $data = array(
            "type" => $type
        );

        echo view("Common/HeaderAuth.html");
        echo view('Auth/SignInCompanyReport.html', $data);
        echo view("Common/FooterAuth.html");

    } //}}}

    public function SignUp()
    { // {{{

        echo view("Common/Header.html");
        echo view('Auth/SignUp.html');
        echo view("Common/Footer.html");

    } // }}}

    /**
     * 구매기업 회원가입
     */
    public function SignUpBuyerSLA()
    { // {{{

        $sla = $this->user_model->getTermsData("Terms/ServiceLevelAgreement");
        $tos = $this->user_model->getTermsData("Terms/TermsOfService");
        $pps = $this->user_model->getTermsData("Terms/PrivacyPolicy");
        $sbs = $this->user_model->getTermsData("Terms/Subscribe");
        $ads = $this->user_model->getTermsData("Terms/AdditionalService");

        $data = array(
             "sla" => $sla["contents"]
            ,"tos" => $tos["contents"]
            ,"pps" => $pps["contents"]
            ,"sbs" => $sbs["contents"]
            ,"ads" => $ads["contents"]
        );

        echo view("Common/Header.html");
        echo view('Auth/SignUpBuyerSLA.html', $data);
        echo view("Common/Footer.html");

    } // }}}

    public function SignUpBuyer()
    { // {{{

        $data = array(
             "sbs" => $_POST["sbs"]
            ,"ads" => $_POST["ads"]
        );

        echo view("Common/Header.html");
        echo view('Auth/SignUpBuyer.html', $data);
        echo script_tag("/assets/js/"._CONTROLLER."/SignUpUser.js");
        echo view("Common/Footer.html");
        echo view("Modal/SearchPost.html"); 

    } // }}}

    public function SignUpUserStep2()
    { // {{{

        $dup_check = $this->user_model->dupCheck($_POST["email"]);
        if($dup_check){
            echo "
                <script>
                    alert('이미 가입된 회원입니다.');
                    window.location.replace('/Auth/SignUp');
                </script>
            ";

            die();
        }

        $impairments = $this->database_model->getImpairmentAll();

        $data = array(
             "hidden" => array(
                 "sbs" => $_POST["sbs"]
                ,"ads" => $_POST["ads"]
                ,"name" => $_POST["name"]
                ,"email" => $_POST["email"]
                ,"password" => $_POST["password"]
                ,"phone" => $_POST["phone"]
                ,"tel" => @$_POST["tel"]
                ,"fax" => @$_POST["fax"]
                ,"post_code" => $_POST["post_code"]
                ,"address" => $_POST["address"]
                ,"address_detail" => $_POST["address_detail"]
                ,"coordinate_x" => $_POST["coordinate_x"]
                ,"coordinate_y" => $_POST["coordinate_y"]
            )
            ,"impairments" => $impairments
        );

        echo view("Common/HeaderAuth.html");
        echo view('Auth/SignUpUserStep2.html', $data);
        echo script_tag("assets/js/"._CONTROLLER."/SignUpUser.js");
        echo view("Common/FooterAuth.html");

    } // }}}

    public function SignUpBuyerSubmit()
    { //{{{

        $dup_check = $this->user_model->dupCheck($_POST["email"]);
        if($dup_check){
            echo "
                <script>
                    alert('이미 가입된 기업입니다.');
                    window.location.replace('/Auth/SignUp');
                </script>
            ";

            die();
        }

        $uuid = $this->user_model->Register($_FILES, $_POST);

        if($uuid){
            header("Location: /"._CONTROLLER."/SignUpBuyerComplete/".$uuid);
        }
        else {
            echo "
                <script>
                    alert('오류가 발생했습니다. (Code: 1100)');
                    window.history.back(-1);
                </script>
            ";
        }

        die();

    } //}}}

    public function SignUpBuyerComplete($uuid = null)
    { // {{{
        
        // 가입한 회원번호 : uuid
        // 지금은 아무 처리 안함. 나중에 기능이 추가될 수도...

        echo view("Common/Header.html");
        echo view('Auth/SignUpBuyerComplete.html');
        echo view("Common/Footer.html");

    } // }}}


    /**
     * 판매기업 회원가입
     */
    public function SignUpSellerSLA()
    { // {{{

        $sla = $this->company_model->getTermsData("Terms/ServiceLevelAgreement");
        $tos = $this->company_model->getTermsData("Terms/TermsOfService");
        $pps = $this->company_model->getTermsData("Terms/PrivacyPolicy");
        $sbs = $this->company_model->getTermsData("Terms/Subscribe");
        $ads = $this->company_model->getTermsData("Terms/AdditionalService");

        $data = array(
             "sla" => $sla["contents"]
            ,"tos" => $tos["contents"]
            ,"pps" => $pps["contents"]
            ,"sbs" => $sbs["contents"]
            ,"ads" => $ads["contents"]
        );

        echo view("Common/Header.html");
        echo view('Auth/SignUpSellerSLA.html', $data);
        echo view("Common/Footer.html");

    } // }}}

    public function SignUpSeller()
    { // {{{

        $data = array(
             "sbs" => $_POST["sbs"]
            ,"ads" => $_POST["ads"]
        );

        echo view("Common/Header.html");
        echo view('Auth/SignUpSeller.html', $data);
        echo script_tag("/assets/js/"._CONTROLLER."/SignUpCompany.js");
        echo view("Common/Footer.html");
        echo view("Modal/SearchPost.html"); 

    } // }}}

    public function SignUpSellerSubmit()
    { //{{{
        $dup_check = $this->user_model->dupCheck($_POST["email"]);
        if($dup_check){
            echo "
                <script>
                    alert('이미 가입된 기업입니다.');
                    window.location.replace('/Auth/SignUp');
                </script>
            ";

            die();
        }
        $uuid = $this->company_model->Register($_FILES,$_POST);

        if($uuid){
            header("Location: /"._CONTROLLER."/SignUpSellerComplete/".$uuid);
        }
        else {
            echo "
                <script>
                    alert('오류가 발생했습니다. (Code: 1100)');
                    window.history.back(-1);
                </script>
            ";
        }

        die();

    } //}}}

    public function SignUpSellerComplete($uuid = null)
    { // {{{
        
        // 가입한 회원번호 : uuid
        // 지금은 아무 처리 안함. 나중에 기능이 추가될 수도...

        echo view("Common/Header.html");
        echo view('Auth/SignUpSellerComplete.html');
        echo view("Common/Footer.html");

    } // }}}

    public function SignOut()
    { //{{{

        session_destroy();
        header("Location: /");

        die();
    } //}}}

    /**
     * 아이디 찾기
     */
    public function ForgotMyId()
    { // {{{

        echo view("Common/Header.html");
        echo view('Auth/ForgotMyId.html');
        echo view("Common/Footer.html");

    } // }}}
	
	/**
	 * 비밀번호 찾기
	 */
	public function ForgotMyPass()
	{ // {{{
		
		echo view("Common/Header.html");
		echo view('Auth/ForgotMyPass.html');
		echo view("Common/Footer.html");
		
	} // }}}
	
	/**
	 * ID/비밀번호 찾기
	 */
	public function ForgotSubmit()
	{
		$data = array(
			"company_name" => $_POST["company_name"]
			,"manager_name" => $_POST["manager_name"]
			,"user_phone" => $_POST["user_phone"]
			,"user_id" => $_POST["user_id"]
			,"search_type" => $_POST["search_type"]
		);
		
		$result = $this->forgotModel_model->Register($data);
		
		if($result == "1") {
			echo "
                <script>
                    alert('담당자 확인 후 연락드리겠습니다.');
					window.location.replace('/"._CONTROLLER."/SignIn');
                </script>
            ";
		}else{
			echo "
                <script>
                    alert('가입된 정보가 없습니다. 다시 입력해주세요');
					history.back();
                </script>
            ";
		}
	}
	
}
