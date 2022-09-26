<?php

namespace App\Controllers\Reduction;
use App\Controllers\BaseController;

use App\Models\DatabaseModel;
use App\Models\Buyer\MyPageModel;
use App\Models\Reduction\ReductionModel;
class Help extends BaseController
{

    private $model;
    private $reduction_model;
    private $database_model;
    private $mypage_model;
    public function __construct()
    { //{{{
        $this->reduction_model = new ReductionModel;
        $this->mypage_model = new MyPageModel;
        $this->database_model = new DatabaseModel;
    } //}}}

    public function index()
    {
        if($_GET['cn'] != ""){
        $seller_info = $this->reduction_model->getdownloadList($_GET);
    }
        $data = $this->reduction_model->getdocumentList($_GET);
        $data = array(
            "data" => $data
            ,"seller_info" =>$seller_info
        );

        echo view("Common/Header.html");
        echo view('Reduction/Help.html',$data);
        echo view("Common/Footer.html");
    }

    public function downloadFileNew(){
        $this->reduction_model->downloadFileNew();
    }
}
