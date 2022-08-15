<?php

namespace App\Controllers\Terms;
use App\Controllers\BaseController as Base;
use App\Models\Terms\TermsModel as Model;

class Subscribe extends Base
{
    private $page_name = "약관 관리 > 광고성 정보 수신";
    private $model;

    public function __construct()
    { //{{{
        $this->model = new Model;
    } //}}}

    public function Index()
    { //{{{
        $category = _CONTROLLER;
        $data = array(
             "page_name" => $this->page_name
            ,"category" => $category
            ,"data" => $this->model->getData($category)
        );

        echo view('Common/Header.html');
        echo view('Terms/Index.html', $data);
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
