<?php

namespace App\Controllers\CS;
use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use App\Models\CS\NoticeModel as Model;

class Notice extends BaseController
{

    private $model;
    private $database_model;
	
    public function __construct()
    { //{{{
        $this->model = new Model;
        $this->database_model = new DatabaseModel;
    } //}}}

    public function index()
    {
		$data = $this->model->getListData();
		$data = array(
		    "data" => $data["data"],
		    "data_page_total_cnt" => $data["count"]
		);
		
		echo view("Common/Header.html");
		echo view('CS/Notice/Index.html',$data);
		echo view("Common/Footer.html");
    }
    public function Detail()
    {
	    $data = $this->model->getNoticeBoard();
		$this->model->hitUpdate();
        echo view("Common/Header.html");
        echo view('CS/Notice/Detail.html',$data);
        echo view("Common/Footer.html");
    }
	
	public function downloadFileNew(){
		$this->model->downloadFileNew();
	}
	
}
