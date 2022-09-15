<?php

namespace App\Models\Buyer;
use App\Models\CommonModel;

class MyPageModel extends CommonModel
{

 public function getCartList($uuid){
     $data = [];
     $query = "
  
        select
           *,a.idx as 'productidx'
        from 
            buyer_cart a 
                join seller_product b on a.product_no  = b.product_no
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

 public function ContractStatus($data){
     $workflow_id = $data["workflow_id"];
     $wArr = explode(",",$workflow_id);
     $whereQuery = "";
     if($workflow_id != ""){
         for($i =0; $i< count($wArr); $i++){
             $whereQuery = $whereQuery." or workflow_id =".$wArr[$i];
         }
         $query = "
                update
                    contract_condition
                set
                    contract_status = 5
                where 1=1
                  $whereQuery
            ";

    //     echo $query;
         $this->wrdb->update($query);
         return 1;
     }else{
         return null;
     }
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
        $where_query = "";

        if($_GET["search_A"] != ""){
            $where_query = $where_query." and contract_no like '%".$_GET["search_A"]."%'";
        }
        if($_GET["search_B"] != "all" && $_GET["search_B"] != ""){
            if($_GET["search_B"] == "1"){
                $where_query = $where_query." and contract_status=1";
            }elseif ($_GET["search_B"] == "2"){
                $where_query = $where_query." and contract_status=2";
            }elseif ($_GET["search_B"] == "5"){
                $where_query = $where_query." and contract_status=5";
            }
        }
        $query = "
            select
               *,a.idx as 'cidx'
            from
              contract_condition a
            join seller_product b 
            on a.product_no = b.product_no
            where buyer_uuid ='".$uuid."'".$where_query."
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