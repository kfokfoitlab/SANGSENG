<?php

namespace App\Controllers\Contract;
use App\Controllers\BaseController as Base;
use App\Models\Contract\ContractModel as Model;
use App\Models\Database\DatabaseModel;

class Lists extends Base
{
    private $page_name = "계약관리 > 전체 목록";
    private $model;
    private $database_model;

    public function __construct()
    { //{{{
        $this->model = new Model;
        $this->database_model = new DatabaseModel;
    } //}}}

    public function Index()
    { //{{{

      /*  $job_category = $this->database_model->getJobAll();
        $impairments = $this->database_model->getImpairmentAll();

        $data = array(
            "page_name" => $this->page_name
        ,"job_category" => $job_category
        ,"impairments" => $impairments

            //,"data" => $this->model->getList()
        );
        */
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

    public function Detail($uuid)
    { //{{{

        $item = $this->model->Detail($uuid);
        $job_category = $this->database_model->getJobAll();
        $impairments = $this->database_model->getImpairmentAll();

        $data = array(
            "page_name" => $this->page_name
        ,"job_category" => $job_category
        ,"impairments" => $impairments
        ,"impairment_data" => json_decode($item["application"]["impairment"], true)
        ,"data" => $item["application"]
        ,"receipt" => $item["receipt"]
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

    public function Update()
    { //{{{
        echo view('Common/HeaderSub.html');
        echo view('Application/Lists/Update.html');
        echo view('Common/Footer.html');
    } //}}}

    public function contractSubmit(){


        $result = $this->model->contractSubmit($_GET);
        if($result == 1){
            echo "
            <script>
             alert('계약서를 전송하였습니다.');
                history.back();
            </script>
        ";
        }else{
            echo "
            <script>
           alert('이미 진행중인 계약입니다');
                history.back();
            </script>
        ";
        }
    }


    public function statusUpdate()
    {
        $data = array(
            "idx" => $_GET["idx"]
        ,"status" => $_GET["status"]
        ,"workflow_id" => $_GET["workflow_id"]
        );
        $this->model->statusUpdate($data);

        if($this == 1){
            echo "
            <script>
             alert('상태가 변경되었습니다.');
                history.back();
            </script>
        ";
        }else{
            echo "
            <script>
           alert('이미 진행중인 계약입니다');
                history.back();
            </script>
        ";
        }

    }

 /*   public function ContractUpdate()
    { // {{{
        $this->model->ContractStatus($_POST);
        if($this == 1) {
            echo "
        <script>
        alert('최신화되었습니다.');
        location.href = '/Contract/Lists';
		</script>
        ";
        }else{
            echo "
        <script>
        alert('오류가 발생했습니다.');
        location.href = '/Contract/Lists';
		</script>
        ";
        }
    } // }}}*/


	public function ContractUpdate()
	{ // {{{
        $result = $this->model->ContractStatus($_POST);
        echo $result;
        die();

	} // }}}
	
	public function ContractDelete()
	{ // {{{
		$this->model->ContractDelete($_GET);
		echo "
        <script>
        alert('삭제되었습니다.');
        location.href = '/Contract/Lists';
		</script>
        ";
	} // }}}
}
