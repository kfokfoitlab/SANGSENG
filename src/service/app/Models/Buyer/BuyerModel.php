<?php

namespace App\Models\Buyer;
use App\Models\CommonModel;

class BuyerModel extends CommonModel
{

    public function getProductList(){
        $data = [];
        // total
        $query = "
            select
                count(*)
            from
                seller_product
        ";
        $data["count"] = $this->rodb->simple_query($query);
        $data= [];
        $query = "
            select
                *
            from
              seller_product
            where status = '5'
            and del_yn !='Y' 
           order by 
               idx desc
            limit 5  
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data[] = $row;

        }
        return $data;
    }


    public function productDetail($product_no){
        $data["data"] = [];
        $query = "
              select
                  *, a.register_id as 'seller_uuid'
            from
                seller_product a
                join
                    seller_company b
                        on a.register_id = b.uuid
            where
                a.product_no = $product_no

        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data = $row;
        }
        return $data;
    }

    public function contract_Check($data){
        $uuid = $_SESSION['login_info']['uuid'];
        $product_no = $_POST['product_no'];

        $query = "
            select
                count(*)
            from
                contract_condition
            where
                product_no = $product_no
                and buyer_uuid = '".$uuid."'
        ";
        return $this->rodb->simple_query($query);


    }

    public function cartDel($data){
        $idx = $data['idx'];
        $uuid = $_SESSION["login_info"]["uuid"];
        $query = " 
           select count(*)
           from buyer_cart
           WHERE idx = $idx
           and buyer_id ='".$uuid."'
            LIMIT 1 
        ";
        $cart_del = $this->rodb->simple_query($query);
        if($cart_del >0){
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
        }else{
            return null;
        }
    }

    public function contract($data){
        helper(["uuid_v4", "specialchars"]);
        $uuid = gen_uuid_v4();
        $contract_no = date("YmdHis");
        $contract_status = "1";
        $buyer_uuid = $_SESSION['login_info']['uuid'];
        $reduction_money = $data['reduction_money'];
        $query = "
          insert into
              contract_condition
          set
               contract_no = '".$contract_no."'
               ,uuid = '".$uuid."'
              ,contract_status = '".$contract_status."'
              ,seller_uuid = '".$data["seller_uuid"]."'
              ,buyer_uuid = '".$buyer_uuid."'
              ,seller_company = '".$data["seller_company"]."'
              ,product_name = '".$data["product_name"]."'
              ,product_price = '".$data["product_price"]."'
              ,buyer_company = '".$data["buyer_company"]."'
              ,reduction_money = '".$reduction_money."'
              ,product_no = '".$data["product_no"]."'       
              ,register_date = '".date("Y-m-d H:i:s")."'
              ,del_yn = 'N'          
      ";
        $idx = $this->wrdb->insert($query);
        if($idx){
            return 1;
        }else{
            return null;
        }
    }

    public function RecommendationList($value){
        $ranking = [];
        $query = "
            select
                *
            from
              seller_product
            where status ='5'
            and product_category = $value
           order by 
               product_ranking asc
            limit 5;
           
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $ranking["data"][]= $row;

        }
        return $ranking;

    }

    public function ReductionMoney(){
        $reduction = [];

        $query = "
            select
                sum(reduction_money) as reduction_money
            from
                contract_condition
            where 1=1
              and contract_status ='5'
              and del_yn !='Y'
           
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $reduction= $row;
        }
        return $reduction;
    }
    public function CategoryList($value){
        $list = [];
        $query = "
            select
                *
            from
              seller_product
            where 1=1
              and status ='5'
              and del_yn !='Y'
                and product_category = $value
           
        ";
        if($_GET["search_v"] != ""){
            $query = $query."and (product_name like '%".$_GET["search_v"]."%'
             or company_name like '%".$_GET["search_v"]."%')";
        }
        $query = $query." order by register_date  desc";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $list["data"][]= $row;

        }
        return $list;
    }

    public function Buyer_info($uuid){
        $buyer_info = [];
        $query = "
            select
                *
            from
              buyer_company
            where  uuid = '".$uuid."'
           
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $buyer_info = $row;

        }
        return $buyer_info;
    }

    public function cartCheck($data){
        $uuid = $_SESSION['login_info']['uuid'];
        $product_no = $data['product_no'];

        $query = "
            select
                count(*)
            from
                buyer_cart
            where
                product_no = $product_no
                and buyer_id = '".$uuid."'
                and del_yn ='N'
            limit 1
        ";
        return $this->rodb->simple_query($query);


    }

    public function CartInsert($data){
        $product_no = $data["product_no"];
        $buyer_id = $_SESSION['login_info']['uuid'];
        $seller_id = $data["seller_uuid"];
        $reduction_money =$data['reduction_money'];
        $query = "
          insert into
               buyer_cart
          set
               product_no = '".$product_no."'
               ,buyer_id = '".$buyer_id."' 
               ,seller_id = '".$seller_id."' 
               ,cart_reduction_money = '".$reduction_money."' 
              ,register_date = '".date("Y-m-d H:i:s")."'
              ,register_id = '".$buyer_id."' 
              ,del_yn = 'N'          
      ";
        $idx = $this->wrdb->insert($query);
        if($idx){
            return 1;
        }else{
            return null;
        }
    }
}