<?php

namespace App\Models\Buyer;
use App\Models\CommonModel;
use function Sodium\add;

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
        $seller_uuid = $data['seller_uuid'];
        $buyer_uuid = $_SESSION['login_info']['uuid'];
        $reduction_money = $data['reduction_money'];
        $product_quantity = $data['product_quantity'];
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
              ,product_quantity = '".$data["product_quantity"]."'
              ,reduction_money = '".$reduction_money."'
              ,product_no = '".$data["product_no"]."'       
              ,register_date ='".date("Y-m-d")."'
              ,del_yn = 'N'          
      ";
        $idx = $this->wrdb->insert($query);
        if($idx){
            $query = "
            update
                seller_company
            set
                seller_notification = '1'
            where uuid ='".$seller_uuid."'
        ";
            $this->wrdb->update($query);
            return 1;
        }else{
            return null;
        }
    }

    public function RecommendationList($value){
        $where = "";

        if($value != ""){
            $where = "  and product_category = $value";
        }
        $ranking = [];
        $query = "
            select
                *
            from
              seller_product
            where status ='5'
           $where
           order by 
               register_date desc
            limit 5;
           
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $ranking["data"][]= $row;

        }
        foreach($ranking["data"] as $item){
            $ranking["replyCount"][] = $this->SellerReplyCount($item["product_no"]);
        }
        return $ranking;

    }
    public function NotificationDel(){
        $uuid = $_SESSION['login_info']['uuid'];
        $query = "
            update
                buyer_company
            set
                 buyer_notification = '0'
            where
            uuid = '".$uuid."'
            limit 1
        ";
        $this->wrdb->update($query);
        return "1";

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

    public function BuyerReduction(){
        $uuid = $_SESSION['login_info']['uuid'];
        $buyer_reduction =[];
        $query = "
            select
                sum(reduction_money) as buyer_reduction
            from
                contract_condition
            where 1=1
            and   buyer_uuid = '".$uuid."'          
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $buyer_reduction= $row;
        }
        return $buyer_reduction;
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
            $query = $query." and (product_name like '%".$_GET["search_v"]."%'
             or company_name like '%".$_GET["search_v"]."%')";
        }
        $list_cnt = [];
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $list_cnt[] = $row;
        }
        $list["count"] = count($list_cnt);
        $page_start = 0;
        if($_GET["p_n"] != ""){
            $page_start = ($_GET["p_n"] - 1)*10;
        }
        $query = $query." order by product_price desc,reduction desc";
        $query = $query." limit ".$page_start.", 10";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $list["data"][]= $row;

        }
        foreach($list["data"] as $item){
            $list["replyCount"][] = $this->SellerReplyCount($item["product_no"]);
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
        $seller_company = $data["seller_company"];
        $query = "
          insert into
               buyer_cart
          set
               product_no = '".$product_no."'
               ,buyer_id = '".$buyer_id."' 
               ,seller_id = '".$seller_id."' 
               ,seller_company = '".$seller_company."' 
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

    public function SellerReplyReg($data){
        $product_no = $data["product_no"];
        $user_uuid = $_SESSION['login_info']['uuid'];
        $user_company_name = $_SESSION['login_info']['company_name'];
        $reply_content = $data['reply_content'];
        $reply_step = $data['reply_step'];
        $reply_no = date("YmdHis");
        if($reply_step != 1){
            $reply_no = $data["reply_no"];
            $reply_step = "(select max(a.reply_step) from seller_product_reply as a where a.reply_no = '".$reply_no."') + 1";
        }
        $query = "
          insert into
               seller_product_reply
          set
               product_no = '".$product_no."'
              ,user_uuid = '".$user_uuid."'
              ,user_company_name = '".$user_company_name."'
              ,user_type = '".$_SESSION['login_info']['type']."'
              ,reply_content = '".$reply_content."'
              ,reply_no = '".$reply_no."'
              ,reply_step = ".$reply_step."
              ,register_date = '".date("Y-m-d H:i:s")."'
              ,register_id = '".$user_uuid."'
      ";
        $idx = $this->wrdb->insert($query);
        if($idx){
            return 1;
        }else{
            return null;
        }
    }

    public function SellerReplyCheck($data){
        $user_uuid = $_SESSION['login_info']['uuid'];
        $product_no = $data['product_no'];

        $query = "
            select
                count(*)
            from
                contract_condition
            where
                product_no = $product_no
                and buyer_uuid = '".$user_uuid."'
                and contract_status = '5'
				and del_yn = 'n'
            limit 1
        ";

        return $this->rodb->simple_query($query);
    }

    public function SellerReplyCountCheck($data){
        $user_uuid = $_SESSION['login_info']['uuid'];
        $product_no = $data['product_no'];

        $query = "
            select
                count(*)
            from
                seller_product_reply
            where
                product_no = $product_no
                and user_uuid = '".$user_uuid."'
				and del_yn = 'n'
            limit 1
        ";

        return $this->rodb->simple_query($query);
    }

    public function SellerReReplyCheck($data){
        $user_uuid = $_SESSION['login_info']['uuid'];
        $product_no = $data['product_no'];

        if($_SESSION['login_info']['type'] == 'buyer') {
            $query = "
            select
                count(*)
            from
                seller_product_reply
            where
                product_no = $product_no
                and user_uuid = '" . $user_uuid . "'
                and reply_no = '" . $_POST["reply_no"] . "'
				and del_yn = 'n'
            limit 1
            ";
        }elseif ($_SESSION['login_info']['type'] == 'seller'){
            $query = "
            select
                count(*)
            from
                seller_product
            where
                product_no = $product_no
                and register_id = '" . $user_uuid . "'
            limit 1
            ";
        }

        return $this->rodb->simple_query($query);
    }

    public function SellerReplyList($product_no){
        $data = [];
        $query = "
              select
                  *
            from
                seller_product_reply
            where
                product_no = '".$product_no."'
				and del_yn = 'n'
            order by reply_no desc,reply_step asc
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data[] = $row;
        }
        return $data;
    }

    public function SellerReplyCount($product_no){
        $query = "
              select
                  count(idx)
            from
                seller_product_reply
            where
                product_no = '".$product_no."'
                and reply_step=1
                and del_yn = 'n'
        ";

        $replyCount = $this->rodb->simple_query($query);

        return $replyCount;
    }

    public function SellerReplyDelete($data){
        $user_uuid = $_SESSION['login_info']['uuid'];

        if($data["reply_step"] == 1){
            $WhereQuery = " reply_no = ".$data["reply_no"];
        }else{
            $WhereQuery = " idx = ".$data["idx"];
        }
        $query = "
			UPDATE
			seller_product_reply SET
			del_yn = 'y'
			,delete_date = '".date("Y-m-d H:i:s")."'
			,delete_id = '".$user_uuid."'
			WHERE
			    ".$WhereQuery."
		";

        $result = $this->wrdb->update($query);
        return $result;
    }

    public function SellerReplyUpdate($data){
        $user_uuid = $_SESSION['login_info']['uuid'];

        $query = "
			UPDATE
			seller_product_reply SET
			reply_content = '".$data["reply_content"]."'
			,update_date = '".date("Y-m-d H:i:s")."'
			,update_id = '".$user_uuid."'
			WHERE
			    idx = ".$data["idx"]."
		";

        $result = $this->wrdb->update($query);
        return $result;
    }
}