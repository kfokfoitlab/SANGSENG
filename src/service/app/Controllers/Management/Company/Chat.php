<?php

namespace App\Controllers\Management\Company;
use App\Controllers\BaseController;
use App\Models\Management\Company\ChatModel as Model;

class Chat extends BaseController
{
    private $model;
    private $company_uuid;

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
        $this->company_uuid = $_SESSION["login_info"]["uuid"];
        
    } //}}}

    public function Index()
    { // {{{

        $chat = $this->model->getList($this->company_uuid);

        $data = array(
            "data" => $chat
        );

        echo view("Common/HeaderManagement.html");
        echo view(_CONTROLLER.'/Index.html', $data);
        echo view("Common/FooterManagement.html");

    } // }}}
}
