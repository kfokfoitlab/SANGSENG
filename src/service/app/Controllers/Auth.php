<?php

namespace App\Controllers;
use App\Models\Auth\SignInModel as Model;
use App\Models\Auth\SignUpUserModel as UserModel;
use App\Models\Auth\SignUpCompanyModel as CompanyModel;
use App\Models\DatabaseModel;

class Auth extends BaseController
{

    private $model;
    private $user_model;
    private $company_model;
    private $database_model;

    public function __construct()
    { //{{{
        $this->model = new Model;
        $this->user_model = new UserModel;
        $this->company_model = new CompanyModel;
        $this->database_model = new DatabaseModel;
    } //}}}

    public function index()
    {
        header("Location:/Auth/SignIn");
    }

    public function SignIn()
    { // {{{

        session_destroy();

        $remember_id = (@$_COOKIE["remember_id"])? $_COOKIE["remember_id"] : "";
        $remember_me = (@$_COOKIE["remember_me"])? $_COOKIE["remember_me"] : "";
        $data = array(
             "remember_me" => $remember_me
            ,"remember_id" => $remember_id
        );

        echo view("Common/HeaderAuth.html");
        echo view('Auth/SignIn.html', $data);
        echo view("Common/FooterAuth.html");

    } // }}}

    public function SignInSubmit()
    { //{{{
        $user_id = $_POST["user_id"];
        $password = $_POST["password"];

        $result = $this->model->SignIn($user_id, $password);

        if($result["result"] == "success"){
            // 아이디 저장 쿠키 처리 - 1개월간
            if(@$_POST["remember_me"] == 1){
                setcookie("remember_me", "1", strtotime("+1 month"), "/");
                setcookie("remember_id", $user_id, strtotime("+1 month"), "/");
            }
            else{
                setcookie("remember_me", null, -1, "/");
                setcookie("remember_id", null, -1, "/");
            }

            // 기업회원이고 승인 대기중일때.
            if($_SESSION["login_info"]["type"] == "company"){
                switch($_SESSION["login_info"]["status"]){
                    case "0" :
                    case "1" :
                        header("Location:/Auth/SignInCompanyReport/review");
                        break;
                    case "5" : // OK
                        header("Location:/");
                        break;
                    case "7" :
                        header("Location:/Auth/SignInCompanyReport/reject");
                        break;

                }
            }
            // 인재 회원
            else {
                header("Location:/");
            }
        }
        else {
            echo "
                <script>
                    alert('아이디, 비번이 맞지 않습니다.');
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

        echo view("Common/HeaderAuth.html");
        echo view('Auth/SignUp.html');
        echo view("Common/FooterAuth.html");

    } // }}}

    /**
     * 인재 회원가입
     */
    public function SignUpUserSLA()
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

        echo view("Common/HeaderAuth.html");
        echo view('Auth/SignUpUserSLA.html', $data);
        echo view("Common/FooterAuth.html");

    } // }}}

    public function SignUpUser()
    { // {{{

        $data = array(
             "sbs" => $_POST["sbs"]
            ,"ads" => $_POST["ads"]
        );

        echo view("Common/HeaderAuth.html");
        echo view('Auth/SignUpUser.html', $data);
        echo script_tag("/assets/js/"._CONTROLLER."/SignUpUser.js");
        echo view("Common/FooterAuth.html");
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

    public function SignUpUserSubmit()
    { //{{{

        $uuid = $this->user_model->Register($_FILES, $_POST);

        if($uuid){
            header("Location: /"._CONTROLLER."/SignUpUserComplete/".$uuid);
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

    public function SignUpUserComplete($uuid = null)
    { // {{{
        
        // 가입한 회원번호 : uuid
        // 지금은 아무 처리 안함. 나중에 기능이 추가될 수도...

        echo view("Common/HeaderAuth.html");
        echo view('Auth/SignUpUserComplete.html');
        echo view("Common/FooterAuth.html");

    } // }}}


    /**
     * 기업 회원가입
     */
    public function SignUpCompanySLA()
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

        echo view("Common/HeaderAuth.html");
        echo view('Auth/SignUpCompanySLA.html', $data);
        echo view("Common/FooterAuth.html");

    } // }}}

    public function SignUpCompany()
    { // {{{

        $data = array(
             "sbs" => $_POST["sbs"]
            ,"ads" => $_POST["ads"]
        );

        echo view("Common/HeaderAuth.html");
        echo view('Auth/SignUpCompany.html', $data);
        echo script_tag("/assets/js/"._CONTROLLER."/SignUpCompany.js");
        echo view("Common/FooterAuth.html");
        echo view("Modal/SearchPost.html"); 

    } // }}}

    public function SignUpCompanySubmit()
    { //{{{

        $uuid = $this->company_model->Register($_POST);

        if($uuid){
            header("Location: /"._CONTROLLER."/SignUpCompanyComplete/".$uuid);
        }
        else {
            echo "
                <script>
                    alert('이미 가입된 기업입니다.');
                    window.location.replace('/"._CONTROLLER."/SignUp');
                </script>
            ";
        }

        die();

    } //}}}

    public function SignUpCompanyComplete($uuid = null)
    { // {{{
        
        // 가입한 회원번호 : uuid
        // 지금은 아무 처리 안함. 나중에 기능이 추가될 수도...

        echo view("Common/HeaderAuth.html");
        echo view('Auth/SignUpCompanyComplete.html');
        echo view("Common/FooterAuth.html");

    } // }}}

    public function SignOut()
    { //{{{

        session_destroy();
        header("Location: /");

        die();
    } //}}}
 
}
