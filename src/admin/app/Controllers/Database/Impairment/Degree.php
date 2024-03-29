<?php

namespace App\Controllers\Database\Impairment;
use App\Controllers\BaseController as Base;
use App\Models\Database\Impairment\DegreeModel as Model;

class Degree extends Base
{
    private $page_name = "데이터베이스 > 장애 정도";
    private $model;

    public function __construct()
    { //{{{
        $this->model = new Model;
    } //}}}

    public function Index()
    { //{{{
        $data = array(
             "page_name" => $this->page_name
            ,"data" => $this->model->getList()
        );

        echo view('Common/Header.html');
        echo view(_CONTROLLER.'/Index.html', $data);
        echo script_tag("assets/js/"._CONTROLLER."/Index.js");
        echo view('Common/Footer.html');

    } //}}}

    public function UpdateSubmit()
    { //{{{

        $data = json_decode($_POST["json_data"], true);
        $result = $this->model->Update($data);

        echo "
            <script>
                alert('저장하였습니다.');
                window.location.replace('/"._CONTROLLER."');
            </script>
        ";

        die();
        
    } //}}}

}
