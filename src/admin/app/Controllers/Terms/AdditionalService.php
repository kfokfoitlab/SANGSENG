<?php

namespace App\Controllers\Terms;
use App\Controllers\BaseController as Base;
use App\Models\Terms\TermsModel as Model;

class AdditionalService extends Base
{
    private $page_name = "약관 관리 > 부가서비스 및 혜택 안내";
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
