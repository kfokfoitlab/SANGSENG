<?php

namespace App\Controllers;
use App\Models\Management\Company\ApplicationModel;
use App\Models\CompanyModel;
use App\Models\DatabaseModel;
use App\Models\Seller\SellerModel;
class Seller extends BaseController
{
    private $model;
    private $database_model;
    private $company_model;
    private $seller_model;

    public function __construct()
    { //{{{
        $this->seller_model = new SellerModel;
        $this->application_model = new ApplicationModel;
        $this->database_model = new DatabaseModel;
        $this->company_model = new CompanyModel;
    } //}}}

    public function index()
    {

        $recommended_data = $this->application_model->getRecommendedList(5);

        // 현재 페이지
        $page = (@$_GET["page"])?$_GET["page"] : 1;
        // 페이지당 노출 수
        $this->item_per_page = (@$_GET["length"])?$_GET["length"]:12;
        // 검색 설정
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
        $recent_company = $this->company_model->getAllList(0, $search_query);

      /*  $data = array(
            "job_category" => $job_category
        ,"recommended_data" => $recommended_data
        ,"recent_company" => $recent_company["data"]
        );*/

        echo view("Common/Header.html");
        echo view('Seller/Index.html', /*$data*/);
        echo view("Common/Footer.html");
    }
    public function ItemUpdate()
    { // {{{
        echo view("Common/Header.html");
        echo view('Seller/ItemUpdate.html');
        echo view("Common/Footer.html");
    } // }}}

    public function Contract()
    { // {{{
        echo view("Common/Header.html");
        echo view('Seller/Contract.html');
        echo view("Common/Footer.html");
    } // }}}

}
