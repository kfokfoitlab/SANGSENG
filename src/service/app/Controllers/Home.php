<?php

namespace App\Controllers;
use App\Models\Management\Company\ApplicationModel;
use App\Models\CompanyModel;
use App\Models\DatabaseModel;
use App\Models\Buyer\BuyerModel;

class Home extends BaseController
{
    private $model;
    private $database_model;
    private $buyer_model;
    public function __construct()
    { //{{{
        $this->buyer_model = new BuyerModel;
        $this->application_model = new ApplicationModel;
        $this->database_model = new DatabaseModel;
    } //}}}

    public function index()
    {
     /*   $job_category = $this->database_model->getJobAll();
        $recommended_data = $this->application_model->getRecommendedList(5);

        // 현재 페이지
        $page = (@$_GET["page"])?$_GET["page"] : 1;
        // 페이지당 노출 수
        $this->item_per_page = (@$_GET["length"])?$_GET["length"]:12;
        // 검색 설정
        $data = $this->database_model->SearchList($_POST);
       $search_query = array(
             "title" => @$_GET["st"]
            ,"address" => @$_GET["ad"]
            ,"profession" => @$_GET["pf"]
            ,"employment_type" => @$_GET["et"]
            ,"career" => @$_GET["cr"]
            ,"work_type" => @$_GET["wt"]
            ,"business_type" => @$_GET["bt"]
            ,"sort" => @$_GET["sort"]
            ,"page" => $page
            ,"length" => $this->item_per_page
        );
        $recent_company = $this->company_model->getAllList(0, $search_query);*/
        $ranking = $this->buyer_model->RecommendationList();
        $reduction =  $this->buyer_model->ReductionMoney();

        $data = array(
            "data" => $ranking["data"],
            "reduction" => $reduction
        );

/*        $data = array(
             "job_category" => $job_category
            ,"recommended_data" => $recommended_data
            ,"data" => $data["data"]
        );*/
        
        echo view("Common/Header.html");
        echo view('Home/Index.html', $data);
        echo view("Common/Footer.html");
    }
}
