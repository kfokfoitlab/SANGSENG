<?php

namespace App\Controllers\Management\Company;
use App\Controllers\BaseController;
use App\Models\Management\Company\PublicSettingsModel as Model;
use App\Models\DatabaseModel;

class PublicSettings extends BaseController
{
    private $model;
    private $database_model;

    public function __construct()
    { //{{{
        if(@$_SESSION["login"] != "success"){
            echo "
                alert('로그인이 필요합니다.');
                window.location.replace('/Auth/SignIn');
            ";

            die();
        }

        $this->model = new Model;
        $this->database_model = new DatabaseModel;
    } //}}}

    public function Index()
    { // {{{

        $profile = $this->model->getData($_SESSION["login_info"]["uuid"]);
        $impairments = $this->database_model->getImpairmentAll();

        $data = array(
             "item" => $profile
            ,"profile_img_url" => "/Image/".@$profile["profile_img_uuid"]
        );

        echo view("Common/HeaderManagement.html");
        echo view(_CONTROLLER.'.html', $data);
        echo script_tag('assets/js/'._CONTROLLER.'/'._METHOD.'.js');
        echo view("Common/FooterManagement.html");
        echo view("Modal/SearchPost.html"); 

    } // }}}

    public function UpdateSubmit()
    { //{{{
        $company_uuid = $_SESSION["login_info"]["uuid"];

        $this->model->Update($company_uuid, @$_FILES, $_POST);

        echo "
            <script>
                alert('저장하였습니다.');
                window.location.replace('/"._CONTROLLER."');
            </script>
        ";

        die();

    } //}}}
}
