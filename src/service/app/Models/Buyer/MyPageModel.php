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

     $delivery_query = "
            select
               count(*) delivery
            from
              delivery 
            where del_yn != 'Y' AND buyer_uuid ='".$uuid."'
 
        ";
     $this->rodb->query($delivery_query);
     $data['delivery']= $this->rodb->next_row();

     $contract_query = "
            select
               count(*) contract
            from
              contract_condition 
            where del_yn != 'Y' AND buyer_uuid ='".$uuid."' and contract_status ='2'
 
        ";
     $this->rodb->query($contract_query);
     $data['contract']= $this->rodb->next_row();

     $cart_query = "
            select
               count(*) cart
            from
              buyer_cart 
            where del_yn != 'Y' AND buyer_id ='".$uuid."' 
 
        ";
     $this->rodb->query($cart_query);
     $data['cart']= $this->rodb->next_row();

     $point_query = "
            select
               sum(buyer_point) as point
            from
              contract_condition 
            where del_yn != 'Y' AND buyer_uuid ='".$uuid."' and contract_status ='5'
 
        ";
     $this->rodb->query($point_query);
     $data['point']= $this->rodb->next_row();

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
  $buyer_documents_ori = str_replace('&','＆', $files["buyer_documents"]["name"]);
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

 public function buyerContractStatus($data){
     $workflow_id = $data["workflow_id"];
     $complete_reduction = $data["complete_reduction"];
     $product_quantity = $data["product_quantity"];
     $pworkflow_id = $data["pworkflow_id"];
     if($pworkflow_id != ""){
         $playing = explode(',',$pworkflow_id);
         $where = "workflow_id in (";
         $where = $where . @join(",", $playing);
         $where = $where . ")";

         $playing_query = "
                update
                    contract_condition
                set
                    contract_status =2
                where 
                   $where
            ";
         $this->wrdb->update($playing_query);
     }
     if($workflow_id != ""){
         $query = "
                select
                    *
                from                 
                contract_condition 
                where
                        workflow_id ='".$workflow_id."'
                limit 1
            ";
         $this->rodb->query($query);
         $seller_uuid = $this->rodb->next_row();

         $mild_disabled_query = "
                select
                    count(*) as mild_disabled
                from                 
                seller_company_worker 
                where
                        register_id = '".$seller_uuid['seller_uuid']."'
                       and disability_degree ='2'
                       and status ='5'
                       and del_yn !='Y'
                limit 1
            ";
         $this->rodb->query($mild_disabled_query);
         $seller_mild_disabled = $this->rodb->next_row();

         $severely_disabled_query = "
                select
                    count(*) as severely_disabled
                from                 
                seller_company_worker 
                where
                    register_id = '".$seller_uuid['seller_uuid']."'
                     and disability_degree ='1'
                       and status ='5'
                       and del_yn !='Y'
                limit 1
            ";
         $this->rodb->query($severely_disabled_query);
         $seller_severely_disabled = $this->rodb->next_row();

         $seller_info_query = "
                select
                    *
                from                 
                seller_company
                where
                uuid = '".$seller_uuid['seller_uuid']."'         
                limit 1
            ";
         $this->rodb->query($seller_info_query);
         $seller_info = $this->rodb->next_row();

         $mild_disabled = $seller_mild_disabled["mild_disabled"];
         $severely_disabled = $seller_severely_disabled["severely_disabled"];
         $seller_sales = $seller_info["seller_sales"];
         $contribution =  $complete_reduction/$seller_sales;
         $contribution = explode('.',$contribution);
         $contribution = substr($contribution[1],0,4);
         $supply = $contribution[0].'.'.$contribution; // 감면비율 소수점4째자리
         $workers = $mild_disabled+($severely_disabled*2);  // 장애인근로자 수

         $base = 1149000;   //기본금액
         $reduction_money = $supply*($workers*12)*$base; // (수급비율*근로자)*기본금*12개월
         if($reduction_money > $complete_reduction*0.5) {
             $reduction_money = $complete_reduction * 0.5;  // 감면액이 상품가격의 50%가 넘으면 50%로 표시
         }
         $reduction_money = (int)$reduction_money;
         $slice = substr($reduction_money,0,-1);
         $reduction_money = $slice.'0';
         $point = $complete_reduction*0.01;

         $reduction_query = "
                update
                    contract_condition
                set
                    product_price = '".$complete_reduction."',
                    contract_status =5,
                    product_quantity = '".$product_quantity."',
                    buyer_point = '".$point."',
                    reduction_money =  '".$reduction_money."'
                where 
                    workflow_id = '".$workflow_id."'
            ";
         $this->wrdb->update($reduction_query);

         return 1;
     }else{
         return null;
     }
 }


    public function sellerContractStatus($data){
        $workflow_id = $data["seller_workflow_id"];
        $complete_reduction = $data["complete_reduction"];
        $product_quantity = $data["product_quantity"];
        $pworkflow_id = $data["seller_pworkflow_id"];
        $uuid = $_SESSION['login_info']['uuid'];
        if($pworkflow_id != ""){
            $playing = explode(',',$pworkflow_id);
            $where = "workflow_id in (";
            $where = $where . @join(",", $playing);
            $where = $where . ")";
            $playing_query = "
                update
                    contract_condition
                set
                    contract_status =2
                where 
                    $where
            ";
            $this->wrdb->update($playing_query);
        }
        if($workflow_id != ""){
            $mild_disabled_query = "
                select
                    count(*) as mild_disabled
                from
                    seller_company_worker
                        
                where
                        register_id ='".$uuid."'
                        and disability_degree ='2'
                        and status ='5'
                        and del_yn !='Y'
                limit 1
            ";
            $this->rodb->query($mild_disabled_query);
            $seller_mild_disabled = $this->rodb->next_row();


            $severely_disabled_query = "
                select
                    count(*) as severely_disabled
                from
                    seller_company_worker 
                        
                where
                        register_id = '".$uuid."'
                         and disability_degree ='1'
                         and status ='5'
                         and del_yn !='Y'
                limit 1
            ";
            $this->rodb->query($severely_disabled_query);
            $seller_severely_disabled = $this->rodb->next_row();

            $seller_info_query = "
                select
                   *
                from
                    seller_company
                        
                where
                        uuid = '".$uuid."'
                limit 1
            ";
            $this->rodb->query($seller_info_query);
            $seller_info = $this->rodb->next_row();


            $mild_disabled = $seller_mild_disabled["mild_disabled"];
            $severely_disabled = $seller_severely_disabled["severely_disabled"];
            $seller_sales = $seller_info["seller_sales"];
            $contribution =  $complete_reduction/$seller_sales;
            $contribution = explode('.',$contribution);
            $contribution = substr($contribution[1],0,4);
            $supply = $contribution[0].'.'.$contribution; // 감면비율 소수점4째자리
            $workers = $mild_disabled+($severely_disabled*2);  // 장애인근로자 수

            $base = 1149000;   //기본금액
            $reduction_money = $supply*($workers*12)*$base; // (수급비율*근로자)*기본금*12개월
            if($reduction_money > $complete_reduction*0.5) {
                $reduction_money = $complete_reduction * 0.5;  // 감면액이 상품가격의 50%가 넘으면 50%로 표시
            }
            $reduction_money = (int)$reduction_money;
            $slice = substr($reduction_money,0,-1);
            $reduction_money = $slice.'0';
            $point = $complete_reduction*0.01;

                $reduction_query = "
                  update
                    contract_condition
                set
                    product_price = '".$complete_reduction."',
                    contract_status =5,
                    product_quantity = '".$product_quantity."',
                    buyer_point = '".$point."',
                    reduction_money =  '".$reduction_money."'
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
            where del_yn != 'Y' AND buyer_uuid ='".$uuid."'
        ";
        $data["count"] = $this->rodb->simple_query($query);
        $data["data"] = [];
        $where_query = "";

        if($_GET["search_A"] != ""){
            $where_query = $where_query." and product_name like '%".$_GET["search_A"]."%'";
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
        if($_GET["search_C"] != "all" && $_GET["search_C"] != ""){
            $where_query = $where_query." and product_category = '".$_GET["search_C"]."'";
        }else{
            $where_query = $where_query." ";
        }

        $query = "
            select
               *,idx as 'cidx',product_price as contract_price
            from
              contract_condition 
            where del_yn != 'Y' AND buyer_uuid ='".$uuid."'".$where_query."
        ";
        $page_start = 0;
        if($_GET["p_n"] != ""){
            $page_start = ($_GET["p_n"] - 1)*10;
        }
        $query = $query." order by idx desc";
        $query = $query." limit ".$page_start.", 10";

        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data["data"][] = $row;

        }
        $query = "
            select
               count(*) as playing
            from
              contract_condition 
            where del_yn != 'Y' AND buyer_uuid ='".$uuid."' and contract_status = 2 ".$where_query."
 
        ";
        $this->rodb->query($query);
        $data['playing']= $this->rodb->next_row();
        $query = "
            select
               count(*) as complete
            from
              contract_condition 
            where del_yn != 'Y' AND buyer_uuid ='".$uuid."' and contract_status =5 ".$where_query."
 
        ";
        $this->rodb->query($query);
        $data['complete'] = $this->rodb->next_row();


        $query = "
            select
               sum(product_price) as price
            from
              contract_condition 
            where del_yn != 'Y' AND buyer_uuid ='".$uuid."' and contract_status =5 ".$where_query."
 
        ";
        $this->rodb->query($query);
        $data['price'] = $this->rodb->next_row();

        $query = "
            select
               sum(reduction_money) as reduction
            from
              contract_condition 
            where del_yn != 'Y' AND buyer_uuid ='".$uuid."' and contract_status =5 ".$where_query."
 
        ";
        $this->rodb->query($query);
        $data['reduction'] = $this->rodb->next_row();

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