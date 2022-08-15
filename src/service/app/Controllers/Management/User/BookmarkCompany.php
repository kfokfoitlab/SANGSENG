<?php

namespace App\Controllers\Management\User;
use App\Controllers\BaseController;
use App\Models\CompanyModel as model;
use App\Models\DatabaseModel;

class BookmarkCompany extends BaseController
{
    private $model;
    private $database_model;
    private $user_uuid;
    private $item_per_page = 10; // 페이지네이션, 페이지당 아이템 갯수

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
        $this->database_model = new DatabaseModel;

        $this->user_uuid = $_SESSION["login_info"]["uuid"];
    } //}}}

    public function Index()
    { // {{{

        // 현재 페이지
        $page = (@$_GET["page"])?$_GET["page"] : 1;
        // 페이지당 노출 수
        $this->item_per_page = (@$_GET["length"])?$_GET["length"]:$this->item_per_page;
        // 검색 설정
        $search_query = array(
             "sort" => @$_GET["sort"]
            ,"page" => $page
            ,"length" => $this->item_per_page
        );

        $data = $this->model->getBookmarkAllList(0, $search_query, $this->user_uuid);

        // 페이지네이션 계산
        $total_count = $data["count"];         // 전체 아이템 수
        $item_per_page = $this->item_per_page; 
        $page_count = ceil($total_count / $item_per_page);    // 노출될 페이지 수
        $now_page = $page;

        $data = array(
             "data" => $data["data"]
            ,"item_count" => $data["count"]
            ,"pagination" => array(
                 "page_count" => $page_count
                ,"now_page" => $now_page
            )
        );


        echo view("Common/HeaderManagement.html");
        echo view(_CONTROLLER.'/Index.html', $data);
        //echo script_tag('assets/js/'._CONTROLLER.'.js');
        echo view("Common/FooterManagement.html");
        echo view("Modal/SearchPost.html"); 

    } // }}}

}
