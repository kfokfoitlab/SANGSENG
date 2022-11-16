<?php

namespace App\Controllers\Product;
use App\Controllers\BaseController as Base;
use App\Models\Product\ProductModel as Model;
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

    public function Update($idx)
    { //{{{

        $data = $this->model->Detail($idx);

        $data = array(
            "page_name" => $this->page_name
        ,"data" => $data
        );

        echo view('Common/HeaderSub.html');
        echo view(_CONTROLLER.'/Update.html', $data);
        echo script_tag("assets/js/"._CONTROLLER."/Update.js");
        echo view("Modal/SearchPost.html");
        echo view('Common/Footer.html');

    } //}}}

    public function UpdateSubmit()
    { //{{{
        $this->model->Update(@$_FILES, $_POST);

        if($this == 1 ){
            echo "
            <script>
                alert('수정하였습니다.');
                window.location.replace('/"._CONTROLLER."/Detail/".$_POST["idx"]."');
            </script>
        ";

        }else{
            echo "
            <script>
                alert('실패.');
               window.location.replace('/"._CONTROLLER."/Detail/".$_POST["idx"]."');
            </script>
        ";
        }
     
        die();

    }

    public function statusUpdate()
    {
        $data = array(
            "idx" => $_GET["idx"]
        ,"status" => $_GET["status"]
        );
        $this->model->statusUpdate($data);
		if($_GET["status"] == 7){
			echo "
            <script>
            	alert('반려되었습니다');
                opener.location.reload();
    			window.close();
            </script>
        ";
		}else {
			echo "
            <script>
                history.back();
            </script>
       	 ";
		}
    }
	
	public function StatusComment(){
		echo view(_CONTROLLER.'/StatusComment.html');
	}
    public function downloadFileNew(){
        $this->model->downloadFileNew();
    }

}
