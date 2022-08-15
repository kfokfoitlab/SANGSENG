<?php

namespace App\Controllers;
use App\Models\Management\Company\ApplicationModel as Model;
use App\Models\Management\User\ResumeModel;
use App\Models\Management\User\ManageJobModel;
use App\Models\DatabaseModel;

class Recruit extends BaseController
{
    private $model;
    private $resume_model;
    private $database_model;
    private $item_per_page = 10; // 페이지네이션, 페이지당 아이템 갯수

    public function __construct()
    { //{{{
        $this->model = new Model;
        $this->resume_model = new ResumeModel;
        $this->database_model = new DatabaseModel;
    } //}}}

    public function Index()
    { // {{{
        $job_category = $this->database_model->getJobAll();

        // 현재 페이지
        $page = (@$_GET["page"])?$_GET["page"] : 1;
        // 페이지당 노출 수
        $this->item_per_page = (@$_GET["length"])?$_GET["length"]:$this->item_per_page;
        // 검색 설정
        $search_query = array(
             "title" => @$_GET["st"]
            ,"address" => @$_GET["ad"]
            ,"profession" => @$_GET["pf"]
            ,"employment_type" => @$_GET["et"]
            ,"career" => @$_GET["cr"]
            ,"work_type" => @$_GET["wt"]
            ,"sort" => @$_GET["sort"]
            ,"page" => $page
            ,"length" => $this->item_per_page
        );

        $data = $this->model->getAllList(0, $search_query);

        // 페이지네이션 계산
        $total_count = $data["count"];         // 전체 아이템 수
        $item_per_page = $this->item_per_page; 
        $page_count = ceil($total_count / $item_per_page);    // 노출될 페이지 수
        $now_page = $page;


        $data = array(
             "job_category" => $job_category
            ,"data" => $data["data"]
            ,"item_count" => $data["count"]
            ,"pagination" => array(
                 "page_count" => $page_count
                ,"now_page" => $now_page
            )
        );

        echo view("Common/HeaderSub.html");
        echo view('Recruit/Index.html', $data);
        echo script_tag('assets/js/'._CONTROLLER.'/Index.js');
        echo view("Common/FooterSub.html");
    } // }}}

    public function Detail($uuid)
    { // {{{

        $data = $this->model->DetailRecruit($uuid);
        $job_category = $this->database_model->getJobAll();
        $impairments = $this->database_model->getImpairmentAll();

        $data = array(
             "data" => $data
            ,"job_category" => $job_category
            ,"impairments" => $impairments
            ,"impairment_data" => json_decode($data["impairment"], true)
        );

        echo view("Common/HeaderSub.html");
        echo view('Recruit/Detail.html', $data);
        echo script_tag('assets/js/'._CONTROLLER.'/Index.js');
        echo view("Common/FooterSub.html");
    } // }}}

    public function Application($application_uuid)
    { // {{{
        if(@$_SESSION["login"] != "success" || $_SESSION["login_info"]["type"] != "user"){
            echo "
                <script>
                    alert('인재회원 로그인이 필요합니다.');
                    window.history.back(-1)
                </script>
            ";

            die();
        }
        $user_uuid = $_SESSION["login_info"]["uuid"];

        // 이미 지원여부 확인
        $check = $this->model->ApplicationList($user_uuid, $application_uuid);
        if(is_array($check) && count($check) > 0){
            echo "
                <script>
                    alert('이미 지원하였습니다.');
                    window.history.back(-1);
                </script>
            ";

            die();

        }

        $data = $this->model->DetailRecruit($application_uuid);
        $resume_list = $this->resume_model->getList($user_uuid);

        $data = array(
             "resume_list" => $resume_list
            ,"data" => $data
        );

        echo view("Common/HeaderSub.html");
        echo view('Recruit/Application.html', $data);
        echo script_tag('assets/js/Recruit/Application.js');
        echo view("Common/FooterSub.html");
    } // }}}

    public function ApplicationSubmit()
    { //{{{

        $user_uuid = $_SESSION["login_info"]["uuid"];
        $idx = $this->model->Applicated($user_uuid, $_POST);

        if($idx){
            echo "
                <script>
                    alert('지원하였습니다.');
                    window.location.replace('/Management/User/Application');
                </script>
            ";
        }

        die();

    } //}}}

    public function ApplicationComplete()
    { // {{{
        echo view("Common/HeaderSub.html");
        echo view('Recruit/ApplicationComplete.html');
        echo view("Common/FooterSub.html");
    } // }}}

    public function Bookmark()
    { //{{{

        $type = $_POST["type"];
        $application_uuid = $_POST["application_uuid"];
        $user_uuid = $_POST["user_uuid"];

        $result = $this->model->Bookmark($type, $application_uuid, $user_uuid);
        echo 1;

        die();

    } //}}}
}
