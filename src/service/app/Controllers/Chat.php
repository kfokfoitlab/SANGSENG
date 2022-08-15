<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\ChatModel as Model;

class Chat extends BaseController
{
    private $model;

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
        
    } //}}}

    public function Room($channel_uuid)
    { //{{{

        $channel_info = $this->model->getChannelInfo($channel_uuid);
        if($_SESSION["login_info"]["type"] == "user"){
            if($channel_info["user_uuid"] == $_SESSION["login_info"]["uuid"]){
                $chat_type = "user";
            }
        }
        else if($_SESSION["login_info"]["type"] == "company"){
            if($channel_info["company_uuid"] == $_SESSION["login_info"]["uuid"]){
                $chat_type = "company";
            }
        }
        else {
            echo "
                <script>
                    alert('권한이 없습니다.');
                    window.close();
                </script>
            ";

            die();
        }

        echo "
            <script>
                const channel_uuid = '".$channel_uuid."';
            </script>
        ";

        $chat_list = $this->model->getChat($channel_uuid);
        $data = array(
            "chat_list" => $chat_list
        );

        echo view("Common/HeaderSub.html");
        echo view('Chat/Room.html', $data);
        echo script_tag('https://js.pusher.com/7.2/pusher.min.js');
        echo script_tag('assets/js/'._CONTROLLER.'/Room_'.$chat_type.'.js');
        echo view("Common/FooterSub.html");
        
    } //}}}

    public function Send()
    { //{{{

        $channel = $_POST["channel"];
        $sender_uuid = $_SESSION["login_info"]["uuid"];
        $message = $_POST["message"];
        $chat_type = $_POST["chat_type"];
        $chat = $this->model->Send($channel, $sender_uuid, $message, $chat_type);

        $result = array(
             "channel" => $channel
            ,"message" => $message
        );

        echo json_encode($result, JSON_UNESCAPED_UNICODE);

        die();

    } //}}}

    public function CreateChannel($user_uuid, $company_uuid)
    { //{{{
        $user_uuid = $user_uuid;
        $company_uuid = $company_uuid;

        $channel_uuid = $this->model->CreateChannel($user_uuid, $company_uuid);

        header("Location: /Chat/Room/".$channel_uuid);

        die();

    } //}}}
}
