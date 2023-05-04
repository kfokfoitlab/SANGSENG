<?php

namespace App\Controllers\Product;
use App\Controllers\BaseController as Base;
use App\Models\Product\CategoryModel as Model;
use App\Models\Database\DatabaseModel;

class Category extends Base
{
    private $page_name = "상품관리 > 카테고리";
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
    public function CategoryRegister()
    { //{{{
        echo view('Common/Header.html');
        echo view(_CONTROLLER.'/CategoryRegister.html');
        echo view('Common/Footer.html');
    } //}}}

    public function CategoryRegisterSubmit()
    { //{{{

        $result = $this->model->Register($_POST);

        if($result == "1"){
            echo "
					<script>
						alert('정상 등록되었습니다');
						location.href = '/Product/Category';
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

    public function Delete()
    { //{{{
        $idx = $_GET['idx'];
        $result = $this->model->Delete($idx);
        if ($result == "1") {
            echo "
                <script>
                    alert('삭제되었습니다.');
					window.location.replace('/Product/Category');
                </script>
            ";
        } else {
            echo "
                <script>
                    alert('오류가 발생했습니다. 관리자에게 문의해주세요');
					history.back(-1);
                </script>
            ";
        }
    } //}}}

    public function Update()
    { //{{{
        $idx = $_GET['idx'];
        $data = $this->model->Detail($idx);

        $data = array(
            "page_name" => $this->page_name
        ,"data" => $data
        );
        echo view('Common/HeaderSub.html');
        echo view(_CONTROLLER.'/Update.html', $data);
        echo script_tag("assets/js/"._CONTROLLER."/Update.js");
        echo view('Common/Footer.html');

    } //}}}

    public function UpdateSubmit()
    { //{{{
        $this->model->Update($_POST);
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

}
