<?php

namespace App\Controllers\Configuration;
use App\Controllers\BaseController as Base;
use App\Models\Configuration\ApproveModel as Model;

class Approve extends Base
{ 
    private $page_name;
    private $model;

    public function __construct()
    { //{{{
        $this->page_name = "환경설정 > 심사 설정";
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
        echo view('Common/Footer.html');

    } //}}}

    public function UpdateSubmit()
    { //{{{

        $result = $this->model->Update($_POST);

        echo "
            <script>
                alert('저장하였습니다.');
                window.location.replace('/"._CONTROLLER."');
            </script>
        ";

        die();
        
    } //}}}
}

