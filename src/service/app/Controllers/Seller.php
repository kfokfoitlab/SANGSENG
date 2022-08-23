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

        $data = array(
            "job_category" => $job_category
        ,"recommended_data" => $recommended_data
        ,"recent_company" => $recent_company["data"]
        );

        echo view("Common/Header.html");
        echo view('Seller/Index.html', $data);
        echo view("Common/Footer.html");
    }

    public function List()
    { // {{{
        echo view("Common/Header.html");
        echo view('Seller/IMJOB.html');
        echo view("Common/Footer.html");
    } // }}}

    public function Manage()
    { // {{{
        echo view("Common/Header.html");
        echo view('Seller/Manage.html');
        echo view("Common/Footer.html");
    } // }}}

    public function Item()
    { // {{{
        echo view("Common/Header.html");
        echo view('Seller/ItemRegist.html');
        echo view("Common/Footer.html");
    } // }}}

    public function ItemSubmit()
    { // {{{
        header("Content-Type:text/html;charset=UTF-8");
        $result = $this->seller_model->Register($_FILES, $_POST);

        if($result == "1") {
            echo "
                <script>
                    alert('상품이 등록되었습니다.');
					window.location.replace('/Seller');
                </script>
            ";
        }else{
            echo "
                <script>
                    alert('오류가 발생했습니다.다시 시도해주세요');
					history.back(-1);
                </script>
            ";
        }
        die();
    } // }}}


}
