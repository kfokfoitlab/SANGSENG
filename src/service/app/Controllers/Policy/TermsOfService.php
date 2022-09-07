<?php

namespace App\Controllers\Policy;
use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use App\Models\Buyer\MyPageModel;
use App\Models\Auth\SignInModel as Model;
use App\Models\Auth\SignUpUserModel as UserModel;

class TermsOfService extends BaseController
{

    private $model;
    private $user_model;
    private $database_model;
    private $mypage_model;

    public function __construct()
    { //{{{
        $this->model = new Model;
        $this->user_model = new UserModel;
        $this->mypage_model = new MyPageModel;
        $this->database_model = new DatabaseModel;
    } //}}}

    public function index()
    {
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
        echo view('Policy/TermsOfService.html', $data);
        echo view("Common/Footer.html");
    }

}
