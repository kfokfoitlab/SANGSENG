<?php

namespace App\Controllers\Management\User;
use App\Controllers\BaseController;
use App\Models\Management\User\ProfileModel as Model;
use App\Models\DatabaseModel;

class Profile extends BaseController
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

        $profile = $this->model->getProfile($_SESSION["login_info"]["uuid"]);
        $impairments = $this->database_model->getImpairmentAll();

        $data = array(
             "profile" => $profile
            ,"profile_img_url" => "/Image/".$profile["profile_img_uuid"]
            ,"impairment_data" => json_decode($profile["impairment"], true)
            ,"impairments" => $impairments
        );

        echo view("Common/HeaderManagement.html");
        echo view(_CONTROLLER.'.html', $data);
        echo script_tag('assets/js/'._CONTROLLER.'.js');
        echo view("Common/FooterManagement.html");
        echo view("Modal/SearchPost.html"); 

    } // }}}

    public function UpdateSubmit($section = null)
    { //{{{
        $uuid = $_SESSION["login_info"]["uuid"];
        $_POST["uuid"] = $uuid;

        $this->model->Update($section, @$_FILES, $_POST);

        echo "
            <script>
                alert('수정하였습니다.');
                window.location.replace('/"._CONTROLLER."');
            </script>
        ";

        die();

    } //}}}
}
