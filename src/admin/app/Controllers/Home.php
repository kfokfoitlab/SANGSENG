<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {

        echo view('Common/Header.html');
//        echo view('Home.html');
        echo view('Common/Footer.html');
    }
}
