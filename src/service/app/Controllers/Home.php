<?php

namespace App\Controllers;
use App\Models\Management\Company\ApplicationModel;
use App\Models\CompanyModel;
use App\Models\DatabaseModel;
use App\Models\Buyer\BuyerModel;

class Home extends BaseController
{
    private $model;
    private $database_model;
    private $buyer_model;
    public function __construct()
    { //{{{
        $this->buyer_model = new BuyerModel;
        $this->application_model = new ApplicationModel;
        $this->database_model = new DatabaseModel;
    } //}}}

    public function index()
    {

        $value = $_GET["value"];
        $ranking = $this->buyer_model->RecommendationList($value);
        $reduction =  $this->buyer_model->ReductionMoney();
        $data = array(
            "data" => $ranking["data"],
            "reduction" => $reduction
        );

        echo view("Common/Header.html");
        echo view('Home/Index.html', $data);
        echo view("Common/Footer.html");
    }
}
