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
     $password = $_POST['password'];
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
        $complete_reduction = $data["complete_reduction"];
        $uuid = $data['uuid'];
        $whereQuery = "";
        if($workflow_id != ""){
            $query = "
                update
                    contract_condition
                set
                    contract_status =5
                where 1=1
                  $whereQuery
            ";
            //     echo $query;
            $this->wrdb->update($query);

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
                    contract_status =5,
                    reduction_money = $reduction_money
                where 
                    workflow_id = $workflow_id
            ";
                $this->wrdb->update($reduction_query);
            return 1;
        }else{
            return null;
        }
    }

    public function test($data){
        $workflow_id = $data["workflow_id"];
        $complete_reduction = $data["complete_reduction"];
        $uuid = $data['uuid'];
        $whereQuery = "";
        if($workflow_id != ""){
            $whereQuery = " AND workflow_id in (".$workflow_id.")";
            $query = "
                update
                    contract_condition
                set
                    contract_status =5
                where 1=1
                  $whereQuery
            ";
            //     echo $query;
            $this->wrdb->update($query);
            if($this){
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
                $row = $this->rodb->next_row();

                $mild_disabled = $row["mild_disabled"];
                $severely_disabled = $row["severely_disabled"];
                $seller_sales = $row["seller_sales"];
                $contribution =  $complete_reduction/$seller_sales;
                $workers = $mild_disabled+($severely_disabled*2);
                $reduction =$contribution*$workers;

                $reduction_query = "
                update
                    buyer_company
                set
                   reduction_money = $reduction
                where 1=1
                    and  uuid = '".$uuid."'
            ";
                $this->wrdb->update($reduction_query);
            }
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
            where a.del_yn != 'Y' AND buyer_uuid ='".$uuid."'".$where_query."
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