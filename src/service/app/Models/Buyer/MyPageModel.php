<?php

namespace App\Models\Buyer;
use App\Models\CommonModel;

class MyPageModel extends CommonModel
{

 public function getCartList(){
     $data = [];
     $query = "
  
        select
            *
        from 
            buyer_cart a 
                join seller_product b on a.idx = b.idx
        where
            a.product_no = b.product_no
            and a.del_yn ='N'
           
        ";
     $this->rodb->query($query);
     while($row = $this->rodb->next_row()){
         $data["data"]= $row;

     }
     return $data;
 }

 public function getMyInfo($uuid){
     $data = [];
     $query = "

        select
            *
        from 
            buyer_company 
        where
            uuid = '$uuid'
           
        ";
     $this->rodb->query($query);
     while($row = $this->rodb->next_row()){
         $data= $row;
     }
     return $data;
 }

 public function updateMyInfo(){
     $buyer_name =$_POST["buyer_name"];
     $product_no = $_POST["product_no"];


     $query = "
            update
                buyer_company
            set
                 buyer_name = '".$data["buyer_name"]."'
                ,manager_name = '".$data["manager_name"]."'
                ,did_tel = '".$data["did_tel"]."'
                ,gen_tel = '".$data["gen_tel"]."'
                ,phone = '".$data["phone"]."'
                ,fax = '".$data["fax"]."'
                ,post_code = '".$data["post_code"]."'
                ,address = '".$data["address"]."'
                ,address_detail = '".$data["address_detail"]."'
                ,coordinate = ".$coordinate."
                ".$profile_img_uuid."
                ,update_date = '".date("Y-m-d H:i:s")."'
            where
                uuid = '".$data["uuid"]."'
            limit 1
        ";
     $this->wrdb->update($query);
 }
}
header("Content-Type:text/html;charset=EUC-KR");?>