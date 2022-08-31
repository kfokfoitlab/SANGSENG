<?php

namespace App\Models\Buyer;
use App\Models\Auth;
use App\Models\CommonModel;

class MyPageModel extends CommonModel
{

 public function getCartList($uuid){
     $data = [];
     $query = "
  
        select
            *
        from 
            buyer_cart a 
                join seller_product b on a.idx = b.idx
        where
            a.buyer_id = '$uuid'
            and a.del_yn != 'Y'
           
        ";
     //echo $query;
     $this->rodb->query($query);
     while($row = $this->rodb->next_row()){
         $data["data"][]= $row;

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
     $query = "

        select
            *
        from 
            buyer_company 
        where
            uuid = '$uuid'
       and  password = SHA2('".$password."', 256)
           
        ";
     $this->rodb->query($query);
     $row = $this->rodb->next_row();
    if(isset($row["idx"])){
        return "1";
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
     return "1";
 }

    public function getContractList($uuid){

        $data = [];
        $query = "
            select
                count(*)
            from
                contract_condition
            where seller_uuid ='".$uuid."'
        ";
        $data["count"] = $this->rodb->simple_query($query);
        $data["data"] = [];
        $query = "
            select
               *
            from
              contract_condition a
            join seller_product b 
            on a.seller_uuid = b.register_id
            where buyer_uuid ='".$uuid."'
           order by 
               a.idx desc
           
           
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data["data"][] = $row;

        }
        return $data;
    }
    public function CartDel($idx){
        $uuid = $_SESSION["login_info"]["uuid"];
        $query = "
            update
                buyer_cart
            set
                 del_yn = 'Y'
            where
            register_id = '".$uuid."'
            and idx = $idx
            limit 1
        ";
        $this->wrdb->update($query);
        return "1";
    }
}