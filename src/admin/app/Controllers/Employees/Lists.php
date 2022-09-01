<?php

namespace App\Controllers\Employees;
use App\Controllers\BaseController as Base;
use App\Models\Employees\EmployeesModel as Model;
use App\Models\Database\DatabaseModel;

class Lists extends Base
{
    private $page_name = "근로자관리 > 전체 목록";
    private $model;
    private $database_model;

    public function __construct()
    { //{{{
        $this->model = new Model;
        $this->database_model = new DatabaseModel;
    } //}}}

    public function Index()
    { //{{{

        $job_category = $this->database_model->getJobAll();
        $impairments = $this->database_model->getImpairmentAll();

        $data = array(
             "page_name" => $this->page_name
            ,"job_category" => $job_category
            ,"impairments" => $impairments

            //,"data" => $this->model->getList()
        );

        echo view('Common/Header.html');
        echo view(_CONTROLLER.'/Index.html', $data);
        echo script_tag("assets/js/"._CONTROLLER."/Index.js");
        echo view('Common/Footer.html');

    } //}}}

    public function getList()
    { //{{{

        $start = $_POST["start"];
        $length = $_POST["length"];
        $limit = array(
             "start" => $start
            ,"length" => $length
        );

        $result = $this->model->getListData($_POST);

        $data = array(
             "draw" => @$_POST["draw"]
            ,"recordsTotal" => $result["records_total"]
            ,"recordsFiltered" => $result["filtered_total"]
            ,"data" => $result["data"]
        );

        echo json_encode($data, JSON_UNESCAPED_UNICODE);

        die();
        
    } //}}}

    public function Detail()
    { //{{{

        echo view('Common/HeaderSub.html');
        echo view(_CONTROLLER.'/Detail.html');
        echo script_tag("assets/js/"._CONTROLLER."/Detail.js");
        echo view('Common/Footer.html');


    } //}}}

    public function RecommendSubmit($type, $uuid)
    { //{{{

        $this->model->Recommend($type, $uuid);

        echo 1;

        die();

    } //}}}

    public function Update()
    { //{{{
        echo view('Common/HeaderSub.html');
        echo view('Employees/Lists/Update.html');
        echo view('Common/Footer.html');
    } //}}}

}
