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
        $data["data"] = [];
        $query = "
            select
                *
            from
              seller_product
           order by 
               idx desc
            
           
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data["data"][] = $row;

        }
        return $data;
    }


    public function productDetail($product_no){
        $data["data"] = [];
        $query = "
              select
                  *
            from 
                seller_product a 
                join
                    seller_company b 
                        on a.register_id = b.uuid
            where 
                product_no = $product_no
              and 
                a.register_id = b.uuid

        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data["data"] = $row;
        }
        return $data;
    }

    public function contract($data){
        helper(["uuid_v4", "specialchars"]);
        $uuid = gen_uuid_v4();
        $contract_no = date("YmdHis");
        //1:���δ��,2:������,3:���ű�� ����,5:���Ϸ�,7:�ݷ�,9:������
        $contract_status = "1";

        $query = "
          insert into
              contract_condition
          set
               contract_no = '".$contract_no."'
               ,uuid = '".$uuid."'
              ,contract_status = '".$contract_status."'
              ,seller_uuid = '".$data["seller_uuid"]."'
              ,buyer_uuid = '".$data["buyer_uuid"]."'
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

    public function RecommendationList(){
        $ranking = [];
        $query = "
            select
                *
            from
              seller_product
           order by 
               product_ranking desc
            limit 4;
           
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $ranking["data"][]= $row;

        }
        return $ranking;

    }
    public function CategoryList($value){
        $list = [];
        $query = "
            select
                *
            from
              seller_product
            where 1=1
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

    public function CartInsert(){
        $product_no = $_POST["product_no"];
        $buyer_id = $_POST["buyer_uuid"];
        $seller_uuid = $_POST["seller_uuid"];

        $query = "
          insert into
               buyer_cart
          set
               product_no = '".$product_no."'
               ,buyer_id = '".$buyer_id."' 
               ,seller_id = '".$seller_uuid."' 
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
header("Content-Type:text/html;charset=EUC-KR");?>