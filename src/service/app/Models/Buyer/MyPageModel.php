<?php

namespace App\Models\Buyer;
use App\Models\CommonModel;

class MyPageModel extends CommonModel
{

 public function getCartList($uuid){
/*     $data = [];
     // total
     $query = "
            select
                count(*) as 'count'
            from
                buyer_cart
            where buyer_id = '".$uuid."'
            and del_yn != 'Y'
        ";
     $data["count"] = $this->rodb->simple_query($query);*/
     $data = [];
     $query = "
  
        select
           *,a.idx as 'productidx'
        from 
            buyer_cart a 
                join seller_product b on a.product_no  = b.product_no
        where
            a.buyer_id = '".$uuid."'
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
            uuid = '".$uuid."'
           
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
            uuid = '".$uuid."'
       and  password = SHA2('".$password."', 256)          
        ";
     $this->rodb->query($query);
     $row = $this->rodb->next_row();
    if(isset($row["idx"])){
        return 1;
    }else{
        return null;
    }
 }


 public function updateMyInfo($files,$data){
    $uuid = $_SESSION["login_info"]["uuid"];
     $allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG','JPG','PDF');

     if($files["buyer_documents"]["name"] != ""){
         $buyer_documents_ori = $files["buyer_documents"]["name"];
         $upload_buyer_documents_ori = "buyer_documents";
         $upload_buyer_documents_image = uniqid().".".pathinfo($files["buyer_documents"]["name"], PATHINFO_EXTENSION);
         $this->uploadFileNew($files,$upload_buyer_documents_image,$allowed_ext,$upload_buyer_documents_ori);
     }else{
         $buyer_documents_ori = $data["buyer_documents_ori"];
         $upload_buyer_documents_image = $data["buyer_documents"];
     }

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
                ,interest_office = '".$data["interest_office"]."'
                ,interest_daily = '".$data["interest_daily"]."'
                ,interest_computerized = '".$data["interest_computerized"]."'
                ,interest_food = '".$data["interest_food"]."'       
                ,interest_cleaning = '".$data["interest_cleaning"]."'     
                ,buyer_documents = '".$upload_buyer_documents_image."'
                ,buyer_documents_ori = '".$buyer_documents_ori."'
            where
                uuid = '".$uuid."'
            limit 1
        ";
     $this->wrdb->update($query);
     return "1";
 }

    public function ContractStatus($data){
        $workflow_id = $data["workflow_id"];
        $complete_reduction = $data["complete_reduction"];
        $product_quantity = $data["product_quantity"];
        $pworkflow_id = $data["pworkflow_id"];

        if($pworkflow_id != ""){
            $playing_query = "
                update
                    contract_condition
                set
                    contract_status =2
                where 
                    workflow_id = '".$pworkflow_id."'
            ";
            $this->wrdb->update($playing_query);
        }
        $uuid = $data['uuid'];
        $whereQuery = "";
        if($workflow_id != ""){
                $query = "
                    select
                        *
                    from
                        seller_company a
                            left join contract_condition b on (a.uuid = b.seller_uuid)
                    where
                            b.workflow_id = ".$workflow_id."
                    limit 1
                ";
                $this->rodb->query($query);
                $seller = $this->rodb->next_row();

                $mild_disabled = $seller["mild_disabled"];
                $severely_disabled = $seller["severely_disabled"];
                $seller_sales = $seller["seller_sales"];
                $contribution =  $complete_reduction/$seller_sales;
                $seller_workers = $mild_disabled+($severely_disabled*2);
                $reduction =$contribution*$seller_workers;
            $query = "
                    select
                        *
                    from
                        buyer_company a
                            left join contract_condition b on (a.uuid = b.buyer_uuid)
                    where
                            b.workflow_id = ".$workflow_id."
                    limit 1
                ";
            $this->rodb->query($query);
            $buyer = $this->rodb->next_row();


            $buyer_workers = $buyer["workers"]; //상시근로자
            $classification = 0;
            if($buyer['classification'] == 1){ //기업구분에 따른 의무고용율
                $classification = 0.031;
            }else{
                $classification = 0.034;
            }

            $employ = 0; //의무고용인원
            if($buyer_workers<50){
                $employ = 0;
            }else{
                $employ = (int)($buyer_workers*$classification);
            }
            if($employ != 0){
                $ratio = ($buyer['mild_disabled']+($buyer['severely_disabled']*2))/$employ; //의무고용인원충족비율
            }else{
                $ratio = 0;
            }
            $base = 0;     //부담금기초
            if($buyer_workers<100){
                $base = 0;
            }
            if($ratio >= 0.75){
                $base = 1149000;
            }
            else if($ratio >= 0.5){
                $base = 1217940;
            }
            else if($ratio >= 0.25){
                $base = 1378800;
            }
            else if($ratio > 0){
                $base = 1608600;
            }else{
                $base = 1914400;
            }

            $levy = $base * $employ *12; //부담금
            $result_price = $reduction *$base;
            $reduction_money = 0;
            if($result_price > $levy ){
                $reduction_money = $levy*0.6;
            }else if($result_price >$complete_reduction*0.5){
                $reduction_money = $complete_reduction*0.5 * 12;
            }else if($complete_reduction < $reduction_money){
                $reduction_money = $complete_reduction /2;
            }else{
                $reduction_money = $result_price * 12;
            }
            $reduction_money = (int)$reduction_money;
                $reduction_query = "
                update
                    contract_condition
                set
                    product_price = $complete_reduction,
                    contract_status =5,
                    product_quantity = '".$product_quantity."',
                    reduction_money = $reduction_money
                where 
                    workflow_id = '".$workflow_id."'
            ";
                $this->wrdb->update($reduction_query);
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
            where del_yn != 'Y' AND seller_uuid ='".$uuid."'
        ";
        $data["count"] = $this->rodb->simple_query($query);
        $data["data"] = [];
        $where_query = "";

        if($_GET["search_A"] != ""){
            $where_query = $where_query." and a.product_name like '%".$_GET["search_A"]."%'";
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
               *,idx as 'cidx',product_price as contract_price
            from
              contract_condition 
            where del_yn != 'Y' AND buyer_uuid ='".$uuid."'".$where_query."
           order by 
               idx desc    
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

    public function PwdUpdate($uuid){
        $password = $_POST['new_password'];
        $query = "
            update
                buyer_company
            set
              password = SHA2('".$password."', 256)
            where
            uuid = '".$uuid."'
            limit 1
        ";
        $this->wrdb->update($query);
        return "1";
    }
}