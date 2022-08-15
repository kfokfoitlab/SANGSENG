<?php

namespace App\Controllers\Database\Job;
use App\Controllers\BaseController as Base;
use App\Models\Database\Job\CareerModel as Model;

class Career extends Base
{ 
    private $page_name;
    private $model;

    public function __construct()
    { //{{{
        $this->page_name = "경력 사항";
        $this->model = new Model;
    } //}}}

    public function Index()
    { //{{{
        $data = array(
            "page_name" => $this->page_name
            ,"data" => $this->model->getList()
        );

        echo view('Common/Header.html');
        echo view(_CONTROLLER.'.html', $data);
        echo script_tag("assets/js/Database/Job/Script.js");
        echo view('Common/Footer.html');

    } //}}}

    public function UpdateSubmit()
    { //{{{
        helper("specialchars");

        // data migration
        $data = [];
        if(isset($_POST) && is_array($_POST) && count($_POST) > 0){
            foreach($_POST["idx"] as $key => $val){
                $data[] = array(
                     "idx" => $_POST["idx"][$key]
                    ,"title" => specialchars($_POST["title"][$key])
                    ,"description" => specialchars($_POST["description"][$key])
                ); 
            }

        }

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

