<?php

namespace App\Controllers;
use App\Models\CompanyModel as model;
use App\Models\Management\Company\ApplicationModel;
use App\Models\DatabaseModel;

class Company extends BaseController
{

    private $model;
    private $database_model;
    private $item_per_page = 10; // 페이지네이션, 페이지당 아이템 갯수

    public function __construct()
    { //{{{
        $this->model = new model;
        $this->database_model = new DatabaseModel;
    } //}}}

    public function Index()
    { // {{{

        $job_category = $this->database_model->getJobAll();
        $business_type = $this->model->getBusinessType();

        // 현재 페이지
        $page = (@$_GET["page"])?$_GET["page"] : 1;
        // 페이지당 노출 수
        $this->item_per_page = (@$_GET["length"])?$_GET["length"]:$this->item_per_page;
        // 검색 설정
        $search_query = array(
             "title" => @$_GET["st"]
            ,"address" => @$_GET["ad"]
            ,"business_type" => @$_GET["bt"]
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
            ,"business_type" => $business_type
            ,"data" => $data["data"]
            ,"item_count" => $data["count"]
            ,"pagination" => array(
                 "page_count" => $page_count
                ,"now_page" => $now_page
            )
        );

        echo view("Common/HeaderSub.html");
        echo view('Company/Index.html', $data);
        echo script_tag('assets/js/'._CONTROLLER.'/Index.js');
        echo view("Common/FooterSub.html");
    } // }}}

    public function Detail($company_uuid)
    { // {{{

        $data = $this->model->Detail($company_uuid);

        $application_model = new ApplicationModel;
        $application = $application_model->getList($company_uuid, 3);

        $data = array(
             "data" => $data
            ,"application" => $application
        );

        echo view("Common/HeaderSub.html");
        echo view('Company/Detail.html', $data);
        echo script_tag('assets/js/'._CONTROLLER.'/Index.js');
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
