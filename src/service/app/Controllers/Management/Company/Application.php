<?php

namespace App\Controllers\Management\Company;
use App\Controllers\BaseController;
use App\Models\Management\Company\ApplicationModel as Model;
use App\Models\Management\User\ResumeModel;
use App\Models\DatabaseModel;

class Application extends BaseController
{
    private $model;
    private $resume_model;
    private $database_model;
    private $company_uuid;

    public function __construct()
    { //{{{
        if(@$_SESSION["login"] != "success"){
            echo "
                <script>
                    alert('로그인이 필요합니다.');
                    window.location.replace('/Auth/SignIn');
                </script>
            ";

            die();
        }

        $this->model = new Model;
        $this->resume_model = new ResumeModel;
        $this->database_model = new DatabaseModel;

        $this->company_uuid = $_SESSION["login_info"]["uuid"];
    } //}}}

    public function Index()
    { // {{{

        $data = $this->model->getList($this->company_uuid);

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

        $job_category = $this->database_model->getJobAll();
        $impairments = $this->database_model->getImpairmentAll();
        $item = $this->model->Detail($this->company_uuid, $uuid);
        $application_data = $this->model->getApplicationList($uuid);

        $data = array(
             "job_category" => $job_category
            ,"impairments" => $impairments
            ,"impairment_data" => json_decode($item["impairment"], true)
            ,"data" => $item
            ,"application_data" => $application_data
        );

        echo view("Common/HeaderManagement.html");
        echo view(_CONTROLLER.'/Detail.html', $data);
        echo view("Common/FooterManagement.html");

    } //}}}

    public function DetailReceipt($application_uuid, $resume_uuid, $user_uuid)
    { //{{{

        $receipt_data = $this->model->getApplicationReceiptData($application_uuid, $resume_uuid);

        //$data = $this->resume_model->Detail($user_uuid, $resume_uuid);
        $data = $receipt_data["resume"];
        $job_category = $this->database_model->getJobAll();
        $impairments = $this->database_model->getImpairmentAll();

        $data["school"] = json_decode($data["school"], true);
        $data["career"] = json_decode($data["career"], true);
        $data["award"] = json_decode($data["award"], true);
        $data["license"] = json_decode($data["license"], true);
        $data["skill"] = json_decode($data["skill"], true);
        $data["portfolio"] = json_decode($data["portfolio"], true);

        $data = array(
             "job_category" => $job_category
            ,"data" => $data
            ,"receipt_data" => $receipt_data
            ,"profile" => $receipt_data["user_profile"]
            ,"impairment_data" => json_decode($receipt_data["user_profile"]["impairment"], true)
            ,"impairments" => $impairments
        );

        echo view("Common/HeaderSub.html");
        //echo view('Management/User/Resume/DetailPreview.html', $data);

        echo view(_CONTROLLER.'/DetailReceipt.html', $data);
        echo view("Common/FooterManagement.html");

    } //}}}

    public function Create()
    { //{{{

        $job_category = $this->database_model->getJobAll();
        $impairments = $this->database_model->getImpairmentAll();

        $data = array(
             "job_category" => $job_category
            ,"impairments" => $impairments
        );

        echo view("Common/HeaderManagement.html");
        echo view(_CONTROLLER.'/Create.html', $data);
        //echo script_tag('assets/js/'._CONTROLLER.'/Create.js');
        echo view("Common/FooterManagement.html");
        echo view("Modal/SearchPost.html"); 

    } //}}}

    public function CreateSubmit()
    { //{{{

        $application_uuid = $this->model->Create($this->company_uuid, $_POST);

        if($application_uuid){
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

        $job_category = $this->database_model->getJobAll();
        $impairments = $this->database_model->getImpairmentAll();
        $item = $this->model->Detail($this->company_uuid, $uuid);

        $data = array(
             "job_category" => $job_category
            ,"impairments" => $impairments
            ,"impairment_data" => json_decode($item["impairment"], true)
            ,"data" => $item
        );

        echo view("Common/HeaderManagement.html");
        echo view(_CONTROLLER.'/Update.html', $data);
        //echo script_tag('assets/js/'._CONTROLLER.'/Create.js');
        echo view("Common/FooterManagement.html");
        echo view("Modal/SearchPost.html"); 

    } //}}}

    public function UpdateSubmit()
    { //{{{

        $application_uuid = $this->model->Update($this->company_uuid, $_POST);

        if($application_uuid){
            echo "
                <script>
                    alert('수정하였습니다.');
                    window.location.replace('/"._CONTROLLER."/Detail/".$_POST["uuid"]."');
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

    public function CloseSubmit($uuid)
    { //{{{
        $this->model->CloseApplication($uuid);

        echo "
            <script>
                alert('마감하였습니다.');
                window.close();
            </script>
        ";

        die();
    } //}}}

    public function DeleteSubmit($uuid)
    { //{{{
        $this->model->DeleteApplication($uuid);

        echo "
            <script>
                alert('삭제하였습니다.');
                window.close();
            </script>
        ";

    } //}}}

    // 채용, 탈락
    public function Result($application_uuid, $resume_uuid, $type)
    { //{{{
        $this->model->Result($application_uuid, $resume_uuid, $type);

        echo "
            <script>
                alert('처리하였습니다.');
                window.close();
            </script>
        ";

        die();

    } //}}}
}
