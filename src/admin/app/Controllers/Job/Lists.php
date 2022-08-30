<?php

namespace App\Controllers\Job;
use App\Controllers\BaseController as Base;
use App\Models\Job\JobModel as Model;
use App\Models\Database\DatabaseModel;

class Lists extends Base
{
    private $page_name = "상품관리 > 전체 목록";
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

    public function Detail($idx)
    { //{{{

        $item = $this->model->Detail($idx);

        $data = array(
             "page_name" => $this->page_name
            ,"data" => $item
        );

        echo view('Common/HeaderSub.html');
        echo view(_CONTROLLER.'/Detail.html', $data);
        echo script_tag("assets/js/"._CONTROLLER."/Detail.js");
        echo view('Common/Footer.html');


    } //}}}

    public function RecommendSubmit($type, $uuid)
    { //{{{

        $this->model->Recommend($type, $uuid);

        echo 1;

        die();

    } //}}}

}
