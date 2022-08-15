<?php

namespace App\Controllers\Database\Impairment;
use App\Controllers\BaseController as Base;
use App\Models\Database\Impairment\TypeModel as Model;

class Type extends Base
{
    private $page_name = "데이터베이스 > 장애 유형";
    private $model;

    public function __construct()
    { //{{{
        $this->model = new Model;
    } //}}}

    public function Index()
    { //{{{
        $data = array(
            "page_name" => $this->page_name
        );

        echo view('Common/Header.html');
        echo view(_CONTROLLER.'/Index.html', $data);
        echo script_tag("assets/js/"._CONTROLLER."/Index.js");
        echo view('Common/Footer.html');

    } //}}}

    public function Create()
    { //{{{

        $data = array(
            "page_name" => $this->page_name
        );
        
        echo view('Common/Header.html');
        echo view(_CONTROLLER.'/Create.html', $data);
        echo script_tag("assets/js/"._CONTROLLER."/Create.js");
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
        
    } //}}}

    public function Request($type = null)
    { // {{{
        $model = $this->model;

        switch(strtoupper($type)){
            case "GET":
                $this_level = $_POST["this-level"];
                $parent_idx = @(int)$_POST["parent_idx"];
                $data = $model->getImpairmentList($this_level, $parent_idx);
                echo json_encode($data, JSON_UNESCAPED_UNICODE); 
                break;

            case "SHOW":
                $data = $model->showImpairmentList();
                echo json_encode($data, JSON_UNESCAPED_UNICODE); 
                break;

            case "ADD":
                $data = array(
                     "this_level" => $_POST["this-level"]
                    ,"new_title" => htmlspecialchars($_POST["new-title"], ENT_QUOTES)
                    ,"level0_idx" => @$_POST["level0-idx"]
                    ,"level1_idx" => @$_POST["level1-idx"]
                    ,"level2_idx" => @$_POST["level2-idx"]
                );
                $result = $model->addImpairment($data);
                echo $result;
                break;

            case "UPDATE":
                $data = array(
                     "idx" => @(int)$_POST["idx"]
                    ,"title" => @htmlspecialchars($_POST["title"], ENT_QUOTES)
                );
                $result = $model->updateImpairment($data);
                echo $result;
                break;

            case "UPDATE_STORAGE":
                $data = array(
                     "idx" => @(int)$_POST["idx"]
                    ,"maximum_storage" => (int)@$_POST["maximum_storage"]
                );
                $result = $model->updateStorage($data);
                echo $result;
                break;

            case "DELETE":
                $data = array(
                     "idx" => @(int)$_POST["idx"]
                );
                $result = $model->deleteImpairment($data);
                echo $result;
                break;

            case "SORT":
                $idx_list = $_POST["idx"];
                $this_level = $_POST["this_level"];
                $result = $model->sortingImpairment($idx_list, $this_level);
                echo $result;
                break;

            default:

        }

        die();

    } // }}}

}
