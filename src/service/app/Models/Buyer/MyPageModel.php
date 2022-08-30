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

 public function pwdCheck($password){
     $uuid = $_SESSION["login_info"]["uuid"];
    $password_hash = hash("sha256",$password);

     $query = "

        select
            password
        from 
            buyer_company 
        where
            uuid = '$uuid'
           
        ";
     $this->rodb->query($query);
     $row = $this->rodb->next_row();
     if($row["password"] ===$password_hash){
         return 1;
     }else{
         return null;
     }
 }


 public function updateMyInfo($data){
    $uuid = $_SESSION["login_info"]["uuid"];
    $newPwd = $data["confirm_password"];

     $query = "
            update
                buyer_company
            set
                 buyer_name = '".$data["buyer_name"]."'
                ,phone = '".$data["phone"]."'
                 ,password = SHA2('".$newPwd."', 256)
                ,fax= '".$data["fax"]."'
                 ,classification= '".$data["classification"]."'
                ,address = '".$data["address"]."'
                ,workers = '".$data["workers"]."'
                ,severely_disabled = '".$data["severely_disabled"]."'
                ,mild_disabled = '".$data["mild_disabled"]."'
                ,tax_rate = '".$data["tax_rate"]."'
                ,update_id = '".$uuid."'               
                ,update_date = '".date("Y-m-d H:i:s")."'
            where
                uuid = '".$uuid."'
            limit 1
        ";
     $this->wrdb->update($query);
 }
}
header("Content-Type:text/html;charset=EUC-KR");?>