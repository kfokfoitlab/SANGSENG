<?php

namespace App\Controllers\TermsHistory;
use App\Controllers\BaseController as Base;
use App\Models\TermsHistory\TermsHistoryModel as Model;
use App\Models\Database\DatabaseModel;

class Lists extends Base
{
    private $page_name = "약관동의이력 > 전체 목록";
    private $model;
    private $database_model;

    public function __construct()
    { //{{{
        $this->model = new Model;
        $this->database_model = new DatabaseModel;
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
        $data = $this->model->Detail($_GET);

        $data = array(
            "page_name" => $this->page_name
        ,"data" => $data
        );


        echo view('Common/HeaderSub.html');
        echo view(_CONTROLLER.'/Detail.html',$data);
        echo script_tag("assets/js/"._CONTROLLER."/Detail.js");
        echo view('Common/Footer.html');


    } //}}}

    public function statusUpdate()
    {
        $data = array(
            "idx" => $_GET["idx"]
        ,"status" => $_GET["status"]
        );
        $this->model->statusUpdate($data);
        echo "
            <script>
                history.back();
            </script>
        ";
    }

    public function Update()
    { //{{{

        $data =$this->model->ContentsUpdate($_GET);

        $data = array(
            "page_name" => $this->page_name
        ,"data" => $data
        );
        echo view('Common/HeaderSub.html');
        echo view('TermsHistory/Lists/Update.html',$data);
        echo view('Common/Footer.html');
    } //}}}

    public function UpdateSubmit(){
        $result = $this->model->Update($_POST);
        if($result == 1) {
            echo "
            <script>
                alert('수정되었습니다.');
                window.location.replace('/" . _CONTROLLER . "');
            </script>
        ";
        }else{
            echo "
            <script>
                alert('실패했습니다.');
                window.location.replace('/" . _CONTROLLER . "');
            </script>
        ";
        }
        die();
    }

}
