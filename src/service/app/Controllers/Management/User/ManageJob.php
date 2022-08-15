<?php

namespace App\Controllers\Management\User;
use App\Controllers\BaseController;
use App\Models\Management\User\ManageJobModel as Model;
use App\Models\Management\User\ResumeModel;
use App\Models\DatabaseModel;

class ManageJob extends BaseController
{
    private $model;
    private $resume_model;
    private $database_model;
    private $user_uuid;

    public function __construct()
    { //{{{
        if(@$_SESSION["login"] != "success"){
            echo "
                <script>
                    alert('로그인이 필요합니다.');
                    window.location.replace('/Auth/SignIn');
                <script>
            ";

            die();
        }

        $this->model = new Model;
        $this->resume_model = new ResumeModel;
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

    public function Detail()
    { //{{{

        $data = $this->model->Detail($this->user_uuid);
        $resume_list = $this->resume_model->getList($this->user_uuid);
        $job_category = $this->database_model->getJobAll();

        $data = array(
             "resume_list" => $resume_list
            ,"data" => $data
        );

        echo view("Common/HeaderManagement.html");
        echo view(_CONTROLLER.'/Detail.html', $data);
        echo script_tag('assets/js/'._CONTROLLER.'/Detail.js');
        echo view("Common/FooterManagement.html");

    } //}}}

    public function Create()
    { //{{{

        $resume_list = $this->resume_model->getList($this->user_uuid);

        $data = array(
            "resume_list" => $resume_list
        );

        echo view("Common/HeaderManagement.html");
        echo view(_CONTROLLER.'/Create.html', $data);
        echo script_tag('assets/js/'._CONTROLLER.'/Create.js');
        echo view("Common/FooterManagement.html");

    } //}}}

    public function CreateSubmit()
    { //{{{

        $result = $this->model->Create($this->user_uuid, $_POST);

        if($result){
            echo "
                <script>
                    alert('구직 활동이 시작되었습니다.');
                    window.location.replace('/"._CONTROLLER."');
                </script>
            ";
        }
        else {
            echo "
                <script>
                    alert('에러가 발생했습니다. [code: 3002]');
                    window.history.back(-1);
                </script>
            ";
        }

        die();

    } //}}}

    public function UpdateSubmit()
    { //{{{

        $result = $this->model->Update($this->user_uuid, $_POST);

        if($result){
            echo "
                <script>
                    alert('수정하였습니다.');
                    window.location.replace('/"._CONTROLLER."');
                </script>
            ";
        }

        die();

    } //}}}

    public function DeleteSubmit($idx)
    { //{{{

        $this->model->Delete($this->user_uuid, $idx);

        echo "
            <script>
                alert('구직 활동을 중단하였습니다.');
                window.location.replace('/"._CONTROLLER."');
            </script>
        ";

    } //}}}
}
