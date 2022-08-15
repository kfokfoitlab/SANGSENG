<?php

namespace App\Controllers\Management\User;
use App\Controllers\BaseController;
use App\Models\Management\User\ChatModel as Model;

class Chat extends BaseController
{
    private $model;
    private $user_uuid;

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
        $this->user_uuid = $_SESSION["login_info"]["uuid"];
        
    } //}}}

    public function Index()
    { // {{{

        $chat = $this->model->getList($this->user_uuid);

        $data = array(
            "data" => $chat
        );

        echo view("Common/HeaderManagement.html");
        echo view(_CONTROLLER.'/Index.html', $data);
        echo view("Common/FooterManagement.html");

    } // }}}
}
