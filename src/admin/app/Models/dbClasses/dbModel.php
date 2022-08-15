<?php
namespace App\Models\dbClasses;
use App\Models\dbClasses\ro_conn_db;
use App\Models\dbClasses\wr_conn_db;

class dbModel
{
    protected $rodb;
    protected $wrdb;
    protected $router;

    public function __construct()
    {
        $this->rodb = new ro_conn_db;
        $this->wrdb = new wr_conn_db;
        $this->router = service("router");
    }
}
