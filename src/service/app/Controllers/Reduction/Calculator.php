<?php

namespace App\Controllers\Reduction;
use App\Controllers\BaseController;

use App\Models\DatabaseModel;
use App\Models\Buyer\MyPageModel;
class Calculator extends BaseController
{

    private $model;
    private $database_model;
    private $mypage_model;
    public function __construct()
    { //{{{
        $this->mypage_model = new MyPageModel;
        $this->database_model = new DatabaseModel;
    } //}}}

    public function index()
    {

        echo view("Common/Header.html");
        echo view('Reduction/Calculator.html');
        echo view("Common/Footer.html");
    }
}
