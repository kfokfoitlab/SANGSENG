<?php

namespace App\Models\Seller;
use App\Models\CommonModel;

class SellerInfoModel extends CommonModel
{

    public function getMyInfo($uuid){
        $data = [];
        $query = "

        select
            *
        from 
            seller_company 
        where
            uuid = '".$uuid."'
           
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data= $row;
        }
        return $data;
    }

}
