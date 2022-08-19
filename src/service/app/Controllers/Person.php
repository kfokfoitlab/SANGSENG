<?php

namespace App\Controllers;
use App\Models\Management\User\ManageJobModel as Model;
use App\Models\Management\User\ProfileModel as ProfileModel;
use App\Models\DatabaseModel;

class Person extends BaseController
{
    private $model;
    private $manage_job_model;
    private $profile_model;
    private $database_model;
    private $item_per_page = 10; // 페이지네이션, 페이지당 아이템 갯수

    public function __construct()
    { //{{{
        $this->model = new Model;
        $this->manage_job_model = new Model;
        $this->profile_model = new ProfileModel;
        $this->database_model = new DatabaseModel;
    } //}}}

    public function Index()
    { // {{{
        session_destroy();
        $job_category = $this->database_model->getJobAll();

        // 현재 페이지
        $page = (@$_GET["page"])?$_GET["page"] : 1;
        // 페이지당 노출 수
        $this->item_per_page = (@$_GET["length"])?$_GET["length"]:$this->item_per_page;
        // 검색 설정
        $search_query = array(
             "title" => @$_GET["st"]
            ,"profession" => @$_GET["pf"]
            ,"employment_type" => @$_GET["et"]
            ,"career" => @$_GET["cr"]
            ,"sort" => @$_GET["sort"]
            ,"page" => $page
            ,"length" => $this->item_per_page
        );

        $data = $this->manage_job_model->getAllList(0, $search_query);

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
        echo view('Person/Index.html', $data);
        echo view("Common/FooterSub.html");
    } // }}}

    public function Detail($user_uuid)
    { // {{{

        $data = $this->model->Detail($user_uuid);
        $profile = $this->profile_model->getProfile($user_uuid);

        $resume = $data["resume"];

        $resume["school"] = json_decode($resume["school"], true);
        $resume["career"] = json_decode($resume["career"], true);
        $resume["award"] = json_decode($resume["award"], true);
        $resume["license"] = json_decode($resume["license"], true);
        $resume["skill"] = json_decode($resume["skill"], true);
        $resume["portfolio"] = json_decode($resume["portfolio"], true);

        $data = array(
              "data" => $data["data"]
             ,"resume" => $resume
             ,"profile" => $profile
        );

        echo view("Common/HeaderSub.html");
        echo view('Person/Detail.html', $data);
        echo script_tag('assets/js/'._CONTROLLER.'/Detail.js');
        echo view("Common/FooterSub.html");
    } // }}}

    public function Bookmark()
    { //{{{

        $type = $_POST["type"];
        $company_uuid = $_POST["company_uuid"];
        $user_uuid = $_POST["user_uuid"];

        $result = $this->model->Bookmark($type, $company_uuid, $user_uuid);
        echo 1;

        die();

    } //}}}
}
