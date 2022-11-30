<?php

namespace App\Controllers\Video;
use App\Controllers\BaseController as Base;
use App\Models\Video\VideoModel as Model;
use App\Models\Database\DatabaseModel;

class Lists extends Base
{
    private $page_name = "홍보영상관리 > 전체 목록";
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

    public function Register()
    { //{{{
        $data = array(
            "page_name" => $this->page_name
            //,"data" => $this->model->getList()
        );

        echo view('Common/Header.html');
        echo view(_CONTROLLER.'/VideoRegister.html', $data);
        echo script_tag("assets/js/"._CONTROLLER."/Index.js");
        echo view('Common/Footer.html');
    } //}}}



    public function videoRegisterSubmit()
    { //{{{

        $result = $this->model->Register($_POST,$_FILES);

        if($result == 1){
            echo "
					<script>
						alert('정상 등록되었습니다');
						location.href = '/Video/Lists';
					</script>
				";
        }else{
            echo "
					<script>
						alert('오류가 발생했습니다.다시 시도해주세요');
					</script>
				";
        }
    } //}}}
    public function statusUpdate()
    {
        $data = array(
            "idx" => $_GET["idx"]
        , "status" => $_GET["status"]
        );
        $this->model->statusUpdate($data);
        if ($this == 1) {

            echo "
            <script>
            alert('상태가 변경되었습니다');
                history.back();
            </script>
       	 ";
        }else{
            echo "
            <script>
            alert('실패');
                history.back();
            </script>
       	 ";
        }
    }
}
