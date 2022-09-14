<?php

namespace App\Models\Seller;
use App\Models\CommonModel;

class SellerModel extends CommonModel
{
    public function Register($files, $data, $table_name = "seller_product"){
        $allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG');
        $upload_representative_ori = "representative_image";
        $upload_representative = uniqid().".".pathinfo($files["representative_image"]["name"], PATHINFO_EXTENSION);
        $this->uploadFileNew($files,$upload_representative,$allowed_ext,$upload_representative_ori);

        $upload_image1_ori = "product_image1";
        $upload_image1 = uniqid().".".pathinfo($files["product_image1"]["name"], PATHINFO_EXTENSION);
        $this->uploadFileNew($files,$upload_image1,$allowed_ext,$upload_image1_ori);

        $upload_image2_ori = "product_image2";
        $upload_image2 = uniqid().".".pathinfo($files["product_image2"]["name"], PATHINFO_EXTENSION);
        $this->uploadFileNew($files,$upload_image2,$allowed_ext,$upload_image2_ori);

        $uuid = $_SESSION["login_info"]["uuid"];
        $company_name = $_SESSION["login_info"]["company_name"];
        $product_ranking = 9999;
        $status = '5';
        $product_no = date("YmdHis");

        $query = "
          insert into
              ".$table_name."
          set
               product_no = '".$product_no."'
              ,status = '".$status."'
              ,product_category = '".$data["product_category"]."'
              ,product_name = '".$data["product_name"]."'
              ,product_price = '".$data["product_price"]."'
              ,product_quantity = '".$data["product_quantity"]."'          
              ,product_start = '".$data["product_start"]."'
              ,product_end = '".$data["product_end"]."'
              ,product_surtax = '".$data["product_surtax"]."'
              ,delivery_cycle = '".$data["delivery_cycle"]."'
              ,product_detail = '".$data["product_detail"]."'
              ,representative_image = '".$upload_representative."'
              ,product_image1 = '".$upload_image1."'
              ,product_image2 = '".$upload_image2."'
              ,register_date = '".date("Y-m-d H:i:s")."'
              ,register_id = '".$uuid."'
              ,company_name = '".$company_name."'
              ,product_ranking = '".$product_ranking."'
      ";
            $idx = $this->wrdb->insert($query);
        if($idx){
            return 1;
        }
        else {
            return null;
        }
    }
    public function getProductList($uuid,$page_query,$limit){
        $limit = "";
        if($limit){
            $limit = "limit ".$limit;
        }
        $l_start = $page_query["length"] * ($page_query["page"] - 1);
        $l_end = $page_query["length"];
        $limit_query = "limit ".$l_start.", ".$l_end;

        $data = [];
        // total
        $query = "
            select
                count(*)
            from
                seller_product
            where register_id ='".$uuid."'
        ";
        $data["count"] = $this->rodb->simple_query($query);
        $where_query = "";

        if($_GET["search_A"] != ""){
            $where_query = $where_query." and product_name like '%".$_GET["search_A"]."%'";
        }
        if($_GET["search_B"] != "all" && $_GET["search_B"] != ""){
            if($_GET["search_B"] == "1"){
                $where_query = $where_query." and status=1";
            }elseif ($_GET["search_B"] == "5"){
                $where_query = $where_query." and status=5";
            }elseif ($_GET["search_B"] == "9"){
                $where_query = $where_query." and status=9";
            }
        }
        if($_GET["search_C"] != ""){
            $where_query = $where_query." and product_category = '".$_GET["search_C"]."'";
        }

        $data["data"] = [];
        $query = "
            select
                *
            from
              seller_product  
            where register_id ='".$uuid."'".$where_query." ".$limit_query."       
        ";
       // echo $query;
      //  $query = $query." order by register_date  desc";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data["data"][] = $row;
        }
        return $data;
    }

    public function getProductCount($uuid){

        // total
        $query = "
            select
                count(*) as product_cnt,
                count(case when status =1 then 1 end) as status1,
                count(case when status=5 then 1 end) as status5,
                count(case when status=9 then 1 end) as status9
            from seller_product where 1=1
                                  and register_id ='".$uuid."'
                                   and (del_yn != 'y' or del_yn is null)
    
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data_cnt = $row;
        }
        return $data_cnt;
}


    public function getContractCount($uuid){
        $query = "
            select
                count(*) as contract_cnt,
                count(case when contract_status =1 then 1 end) as status1,
                count(case when contract_status=2 then 1 end) as status2,
                count(case when contract_status=5 then 1 end) as status5
            from contract_condition where 1=1
                                  and seller_uuid ='".$uuid."'
                                   and (del_yn != 'y' or del_yn is null)
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data_cnt = $row;
        }
        return $data_cnt;
    }


    public function getContractList($uuid){

        $data = [];
        // total
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
            where seller_uuid ='".$uuid."'".$where_query."
           order by 
               a.idx desc
           
           
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data["data"][] = $row;

        }
        return $data;
    }

    public function itemDetail($uuid,$product_no){
        $data =[];
        $query = "
            select
                *
            from
              seller_product  
            where product_no = $product_no
            and register_id = '$uuid'
                     
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data["data"][] = $row;

        }
        return $data;
    }

    public function getTotalSales($uuid){
        $sales =[];
        $query = "
            select
              sum(b.product_price) as 'price'
            from
              contract_condition a
            join seller_product b on a.product_no = b.product_no
            where a.seller_uuid = '$uuid'
            and contract_status = 5
                     
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $sales = $row;
        }
        return $sales;
    }
    public function getexpectationSales($uuid){
        $expectationSales =[];
        $query = "
            select
              sum(b.product_price) as 'price'
            from
              contract_condition a
            join seller_product b on a.product_no = b.product_no
            where a.seller_uuid = '$uuid'
              and (contract_status = 2 or contract_status = 5)

                     
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
           $expectationSales = $row;
        }
        return $expectationSales;
    }
    public function getCompletionContract($uuid){
        $completionContract =[];
        $query = "
            select
             count(*) as'count'
            from
              contract_condition          
            where seller_uuid = '$uuid'
              and  contract_status = 5             
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $completionContract = $row;
        }
        return $completionContract;
    }
    public function getContract($uuid){
        $contract =[];
        $query = "
            select
             count(*) as 'count'
            from
              contract_condition          
            where seller_uuid = '$uuid'
              and  contract_status = 2             
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $contract = $row;
        }
        return $contract;
    }

    public function getDisabledCount($uuid){
        $disabledCount =[];
        $query = "
            select
             severely_disabled,
             mild_disabled
            from
              seller_company          
            where uuid = '$uuid'            
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $disabledCount = $row;
        }
        return $disabledCount;
    }

}
