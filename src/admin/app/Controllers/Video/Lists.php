<?php

namespace App\Controllers\Video;
use App\Controllers\BaseController as Base;
use App\Models\Contract\ContractModel as Model;
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

    
}
