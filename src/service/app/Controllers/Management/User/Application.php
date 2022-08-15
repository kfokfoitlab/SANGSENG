<?php

namespace App\Controllers\Management\User;
use App\Controllers\BaseController;
use App\Models\Management\User\ApplicationModel as Model;
use App\Models\Management\Company\ApplicationModel as CompanyApplicationModel;
use App\Models\Management\User\ResumeModel;
use App\Models\DatabaseModel;

class Application extends BaseController
{
    private $model;
    private $company_application_model;
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
        $this->company_application_model = new CompanyApplicationModel;
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

    public function Detail($application_uuid)
    { //{{{

        $application_data = $this->company_application_model->DetailRecruit($application_uuid);
        $receipt_data = $this->model->DetailReceipt($this->user_uuid, $application_uuid);
        $resume_data = $this->resume_model->Detail($this->user_uuid, $receipt_data["resume_uuid"]);
        $resume_list = $this->resume_model->getList($this->user_uuid);

        $data = $this->company_application_model->DetailRecruit($application_uuid);
        $job_category = $this->database_model->getJobAll();
        $impairments = $this->database_model->getImpairmentAll();

        $data = array(
             "data" => $data
            ,"receipt_data" => $receipt_data
            ,"job_category" => $job_category
            ,"impairments" => $impairments
            ,"impairment_data" => json_decode($data["impairment"], true)
            ,"resume_list" => $resume_list
        );

        echo view("Common/HeaderSub.html");
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
