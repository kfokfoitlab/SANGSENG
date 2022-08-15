<?php

namespace App\Controllers\Management\User;
use App\Controllers\BaseController;
use App\Models\Management\User\ResumeModel as Model;
use App\Models\DatabaseModel;

class Resume extends BaseController
{
    private $model;
    private $database_model;
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
        $this->database_model = new DatabaseModel;

        $this->user_uuid = $_SESSION["login_info"]["uuid"];
    } //}}}

    public function Index()
    { // {{{

        $data = $this->model->getList($this->user_uuid);

        $data = array(
            "data" => $data
        );

        echo view("Common/HeaderManagement.html");
        echo view(_CONTROLLER.'/Index.html', $data);
        //echo script_tag('assets/js/'._CONTROLLER.'.js');
        echo view("Common/FooterManagement.html");
        echo view("Modal/SearchPost.html"); 

    } // }}}

    public function Detail($uuid)
    { //{{{

        $data = $this->model->Detail($this->user_uuid, $uuid);
        $job_category = $this->database_model->getJobAll();

        $data["school"] = json_decode($data["school"], true);
        $data["career"] = json_decode($data["career"], true);
        $data["award"] = json_decode($data["award"], true);
        $data["license"] = json_decode($data["license"], true);
        $data["skill"] = json_decode($data["skill"], true);
        $data["portfolio"] = json_decode($data["portfolio"], true);

        $data = array(
             "job_category" => $job_category
            ,"data" => $data
        );

        echo view("Common/HeaderManagement.html");
        echo view(_CONTROLLER.'/Detail.html', $data);
        echo script_tag('assets/js/'._CONTROLLER.'/Create.js');
        echo view("Common/FooterManagement.html");

    } //}}}

    public function DetailPreview($uuid)
    { //{{{

        $data = $this->model->Detail($this->user_uuid, $uuid);
        $job_category = $this->database_model->getJobAll();

        $data["school"] = json_decode($data["school"], true);
        $data["career"] = json_decode($data["career"], true);
        $data["award"] = json_decode($data["award"], true);
        $data["license"] = json_decode($data["license"], true);
        $data["skill"] = json_decode($data["skill"], true);
        $data["portfolio"] = json_decode($data["portfolio"], true);

        $data = array(
             "job_category" => $job_category
            ,"data" => $data
        );

        echo view(_CONTROLLER.'/DetailPreview.html', $data);

        die();

    } //}}}

    public function Create()
    { //{{{

        $job_category = $this->database_model->getJobAll();

        $data = array(
            "job_category" => $job_category
        );

        echo view("Common/HeaderManagement.html");
        echo view(_CONTROLLER.'/Create.html', $data);
        echo script_tag('assets/js/'._CONTROLLER.'/Create.js');
        echo view("Common/FooterManagement.html");

    } //}}}

    public function CreateSubmit()
    { //{{{

        $resume_uuid = $this->model->Create($this->user_uuid, $_FILES, $_POST);

        if($resume_uuid){
            echo "
                <script>
                    alert('저장하였습니다.');
                    window.location.replace('/"._CONTROLLER."');
                </script>
            ";
        }
        else {
            echo "
                <script>
                    alert('에러가 발생했습니다. [code: 2002]');
                    window.history.back(-1);
                </script>
            ";
        }

        die();

    } //}}}

    public function Update($uuid)
    { //{{{

        $data = $this->model->Detail($this->user_uuid, $uuid);
        $job_category = $this->database_model->getJobAll();

        $data["school"] = json_decode($data["school"], true);
        $data["career"] = json_decode($data["career"], true);
        $data["award"] = json_decode($data["award"], true);
        $data["license"] = json_decode($data["license"], true);
        $data["skill"] = json_decode($data["skill"], true);
        $data["skill"] = (is_array($data["skill"]))?join(",", $data["skill"]): "";
        $data["portfolio"] = json_decode($data["portfolio"], true);

        $data = array(
             "job_category" => $job_category
            ,"data" => $data
        );


        echo view("Common/HeaderManagement.html");
        echo view(_CONTROLLER.'/Update.html', $data);
        echo script_tag('assets/js/'._CONTROLLER.'/Create.js');
        echo view("Common/FooterManagement.html");

    } //}}}

    public function UpdateSubmit()
    { //{{{

        $resume_uuid = $this->model->Update($this->user_uuid, $_FILES, $_POST);

        if($resume_uuid){
            echo "
                <script>
                    alert('수정하였습니다.');
                    window.location.replace('/"._CONTROLLER."');
                </script>
            ";
        }
        else {
            echo "
                <script>
                    alert('에러가 발생했습니다. [code: 2002]');
                    window.history.back(-1);
                </script>
            ";
        }

        die();

    } //}}}

    public function DeleteSubmit($resume_uuid)
    { //{{{
        $this->model->Delete($this->user_uuid, $resume_uuid);

        echo "
            <script>
                alert('삭제하였습니다.');
                window.location.replace('/"._CONTROLLER."');
            </script>
        ";

        die();
    } //}}}
}
