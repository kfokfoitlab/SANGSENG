<?php

namespace App\Controllers\Buyer;
use App\Controllers\BaseController;
use App\Models\Management\Company\ApplicationModel;
use App\Models\CompanyModel;
use App\Models\DatabaseModel;
use App\Models\Buyer\BuyerModel;
class Delivery extends BaseController
{
    private $model;
    private $database_model;
    private $buyer_model;
    public function __construct()
    { //{{{
        $this->buyer_model = new BuyerModel;
        $this->database_model = new DatabaseModel;
    } //}}}

    public function index()
    {
        $data = $this->buyer_model->getProductList();


        $data = array(
            "data" => $data["data"]
        );
        echo view("Common/Header.html");
        echo view('Buyer/Index.html', $data);
        echo view("Common/Footer.html");
    }

    public function Status()
    { // {{{

        echo view("Common/Header.html");
        echo view('MyPage/BuyerDeliveryStatus.html');
        echo view("Common/Footer.html");
    } // }}}

    
}
