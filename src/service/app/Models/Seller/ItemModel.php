<?php

namespace App\Models\Seller;
use App\Models\CommonModel;

class ItemModel extends CommonModel
{
    public function Register($files, $data, $table_name = "seller_product"){
        $allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG','JPG','GIF');


        if($files["representative_image"]["name"] != ""){
            $representative_ori = str_replace('&','＆', $files["representative_image"]["name"]);
            $upload_representative_ori = "representative_image";
            $upload_representative = uniqid().".".pathinfo($files["representative_image"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_representative,$allowed_ext,$upload_representative_ori);
        }
        if($files["product_image1"]["name"] != ""){
            $product_image1_ori = str_replace('&','＆', $files["product_image1"]["name"]);
            $upload_image1_ori = "product_image1";
            $upload_image1 = uniqid().".".pathinfo($files["product_image1"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_image1,$allowed_ext,$upload_image1_ori);
        }
        if($files["product_image2"]["name"] != ""){
            $product_image2_ori = str_replace('&','＆', $files["product_image2"]["name"]);
            $upload_image2_ori = "product_image2";
            $upload_image2 = uniqid().".".pathinfo($files["product_image2"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_image2,$allowed_ext,$upload_image2_ori);
        }
        if($files["detail_img"]["name"] != ""){
            $detail_img_ori = str_replace('&','＆', $files["detail_img"]["name"]);
            $upload_detail_image_ori = "detail_img";
            $upload_detail_image = uniqid().".".pathinfo($files["detail_img"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_detail_image,$allowed_ext,$upload_detail_image_ori);
        }
        $uuid = $_SESSION["login_info"]["uuid"];
        $company_name = $_SESSION["login_info"]["company_name"];
        $product_ranking = 9999;
        $status = '1';
        $product_no = date("YmdHis");

        $mild_disabled_count = "
                    select
                        count(*) as mild_disabled_cnt
                    from
                        seller_company_worker
                    where
                         register_id ='".$uuid."'
                         and disability_degree ='2'
                    limit 1
                ";
        $this->rodb->query($mild_disabled_count);
        $mild_disabled_cnt = $this->rodb->next_row();
        $mild_disabled = $mild_disabled_cnt["mild_disabled_cnt"];

        $severely_disabled_count = "
                    select
                        count(*) as severely_disabled_cnt
                    from
                        seller_company_worker
                    where
                         register_id ='".$uuid."'
                         and disability_degree ='1'
                    limit 1
                ";
        $this->rodb->query($severely_disabled_count);
        $severely_disabled_cnt = $this->rodb->next_row();
        $severely_disabled = $severely_disabled_cnt["severely_disabled_cnt"];


        $contribution = sprintf("%f",$data["product_price"]/$data["seller_sales"]);
        $contribution = explode('.',$contribution);
        $contribution = substr($contribution[1],0,4);
        $supply = $contribution[0].'.'.$contribution; // 감면비율 소수점4째자리
        $workers = $mild_disabled+($severely_disabled*2);  // 장애인근로자 수

        $base = 1149000;   //기본금액
        $reduction_money = $supply*($workers*12)*$base; // (수급비율*근로자)*기본금*12개월
        if($reduction_money > $data["product_price"]*0.5) {
            $reduction_money = $data["product_price"] * 0.5;  // 감면액이 상품가격의 50%가 넘으면 50%로 표시
        }
        $reduction_money = (int)$reduction_money;
        $slice = substr($reduction_money,0,-1);
        $reduction_money = $slice.'0';
        $reduction = $reduction_money/$data['product_price'];
        $product_name = addslashes($data['product_name']);
        $product_detail = addslashes($data['product_detail']);
        $product_detail = str_replace("\r\n", "<br>", $product_detail);
        $query = "
          insert into
              ".$table_name."
          set
               product_no = '".$product_no."'
              ,status = '".$status."'
              ,product_category = '".$data["product_category"]."'
              ,product_category2 = '".$data["product_category2"]."'
              ,product_name = '".$product_name."'
              ,product_price = '".$data["product_price"]."'
              ,product_quantity = '".$data["product_quantity"]."'          
              ,product_start = '".$data["product_start"]."'
              ,product_end = '".$data["product_end"]."'
              ,product_surtax = '".$data["product_surtax"]."'
              ,delivery_cycle = '".$data["delivery_cycle"]."'
              ,product_detail = '".$product_detail."'
              ,representative_image = '".$upload_representative."'
              ,product_image1 = '".$upload_image1."'
              ,product_image2 = '".$upload_image2."'
              ,detail_img = '".$upload_detail_image."'
              ,representative_image_ori = '".$representative_ori."'
              ,product_image1_ori = '".$product_image1_ori."'
              ,product_image2_ori = '".$product_image2_ori."'
              ,detail_img_ori = '".$detail_img_ori."'
              ,reduction = '".$reduction."'
              ,supply = '".$supply."'
              ,reduction_money = '".$reduction_money."'
              ,register_date = '".date("Y-m-d H:i:s")."'
              ,register_id = '".$uuid."'
              ,company_name = '".$company_name."'
              ,product_ranking = '".$product_ranking."'
              ,del_yn = 'N'
      ";
        $idx = $this->wrdb->insert($query);
        if($idx){
            return 1;
        }
        else {
            return null;
        }
    }

    public function ItemDelete($data){
        $idx = $data['idx'];
        $status = '8';
        $query = "
            update
                seller_product
            set
                status = '".$status."'
            where
                idx = '".$idx."'
        ";
        $this->wrdb->update($query);
        return "1";
    }

    public function Category1($product_no){
        $category_type =[];
        $query = "
            select
                distinct category_type1 as category_type1
            from
              product_category  
             where del_yn ='N'
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $category_type['category_type1'][]= $row;
        }
        $product_query = "
            select
                 product_category
            from
              seller_product 
            where product_no = '".$product_no."'
        ";
        $this->rodb->query($product_query);
        $product_info = $this->rodb->next_row();

        $query = "
            select
                distinct category_type2 as category_type2
            from
              product_category  
            where category_type1 = '".$product_info['product_category']."'
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $category_type['category_type2'][] = $row;
        }
        return $category_type;
    }


    public function ItemUpdateSubmit($files, $data){

    $uuid = $_SESSION["login_info"]["uuid"];
    $mild_disabled_count = "
                    select
                        count(*) as mild_disabled_cnt
                    from
                        seller_company_worker
                    where
                         register_id ='".$uuid."'
                         and disability_degree ='2'
                    limit 1
                ";
    $this->rodb->query($mild_disabled_count);
    $mild_disabled_cnt = $this->rodb->next_row();
    $mild_disabled = $mild_disabled_cnt["mild_disabled_cnt"];

    $severely_disabled_count = "
                    select
                        count(*) as severely_disabled_cnt
                    from
                        seller_company_worker
                    where
                         register_id ='".$uuid."'
                         and disability_degree ='1'
                    limit 1
                ";
    $this->rodb->query($severely_disabled_count);
    $severely_disabled_cnt = $this->rodb->next_row();
    $severely_disabled = $severely_disabled_cnt["severely_disabled_cnt"];

    $seller_info =[];
    $seller_query = "
			SELECT * FROM seller_company
			WHERE uuid='".$uuid."'
		";
    $this->rodb->query($seller_query);
    while($row = $this->rodb->next_row()) {
        $seller_info = $row;
    }

    $contribution = sprintf("%f",$data["product_price"]/$seller_info["seller_sales"]);
    $contribution = explode('.',$contribution);
    $contribution = substr($contribution[1],0,4);
    $supply = $contribution[0].'.'.$contribution; // 감면비율 소수점4째자리
    $workers = $mild_disabled+($severely_disabled*2);  // 장애인근로자 수

    $base = 1149000;   //기본금액
    $reduction_money = $supply*($workers*12)*$base; // (수급비율*근로자)*기본금*12개월
    if($reduction_money > $data["product_price"]*0.5) {
        $reduction_money = $data["product_price"] * 0.5;  // 감면액이 상품가격의 50%가 넘으면 50%로 표시
    }
    $reduction_money = (int)$reduction_money;
    $slice = substr($reduction_money,0,-1);
    $reduction_money = $slice.'0';
    $reduction = $reduction_money/$data['product_price'];


    $allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG','JPG','GIF');

    if($files["representative_image"]["name"] != ""){
        $representative_ori = str_replace('&','＆', $files["representative_image"]["name"]);
        $upload_representative_ori = "representative_image";
        $upload_representative = uniqid().".".pathinfo($files["representative_image"]["name"], PATHINFO_EXTENSION);
        $this->uploadFileNew($files,$upload_representative,$allowed_ext,$upload_representative_ori);
    }else{
        $representative_ori = $data["representative_image"];
        $upload_representative = $data["representative_image"];
    }
    if($files["product_image1"]["name"] != ""){
        $product_image1_ori = str_replace('&','＆', $files["product_image1"]["name"]);
        $upload_image1_ori = "product_image1";
        $upload_image1 = uniqid().".".pathinfo($files["product_image1"]["name"], PATHINFO_EXTENSION);
        $this->uploadFileNew($files,$upload_image1,$allowed_ext,$upload_image1_ori);
    }else{
        $product_image1_ori = $data["product_image1"];
        $upload_image1 = $data["product_image1"];
    }
    if($files["product_image2"]["name"] != ""){
        $product_image2_ori = str_replace('&','＆', $files["product_image2"]["name"]);
        $upload_image2_ori = "product_image2";
        $upload_image2 = uniqid().".".pathinfo($files["product_image2"]["name"], PATHINFO_EXTENSION);
        $this->uploadFileNew($files,$upload_image2,$allowed_ext,$upload_image2_ori);
    }else{
        $product_image2_ori = $data["product_image2"];
        $upload_image2 = $data["product_image2"];
    }
    if($files["detail_img"]["name"] != ""){
        $detail_img_ori =  str_replace('&','＆', $files["detail_img"]["name"]);
        $upload_detail_image_ori = "detail_img";
        $upload_detail_image = uniqid().".".pathinfo($files["detail_img"]["name"], PATHINFO_EXTENSION);
        $this->uploadFileNew($files,$upload_detail_image,$allowed_ext,$upload_detail_image_ori);
    }else{
        $detail_img_ori = $data["detail_img"];
        $upload_detail_image = $data["detail_img"];
    }
    $product_name = addslashes($data['product_name']);
    $product_detail = addslashes($data['product_detail']);
    $product_detail = str_replace("\r\n", "<br>", $product_detail);
    $product_no = $data["product_no"];
    $status = 3;
    $query = "
            update
                seller_product
            set
                 product_category = '".$data["product_category"]."'
                ,product_category2 = '".$data["product_category2"]."'
                ,product_name = '".$product_name."'
                 ,product_price = '".$data["product_price"]."'
                ,product_quantity= '".$data["product_quantity"]."'
                 ,product_start= '".$data["product_start"]."'
                ,product_end = '".$data["product_end"]."'
                ,product_surtax = '".$data["product_surtax"]."'
                ,delivery_cycle = '".$data["delivery_cycle"]."'
                ,product_detail = '".$product_detail."'
                ,status = '".$status."'
                ,representative_image = '".$upload_representative."'
                ,product_image1 = '".$upload_image1."'
                ,product_image2 = '".$upload_image2."'
                ,detail_img = '".$upload_detail_image."'
                ,representative_image_ori = '".$representative_ori."'
                ,product_image1_ori = '".$product_image1_ori."'
                ,product_image2_ori = '".$product_image2_ori."'
                ,detail_img_ori = '".$detail_img_ori."'
                ,update_id  = '".$uuid."'      
                ,reduction  = '".$reduction."'  
                ,supply  = '".$supply."'  
                ,reduction_money = '".$reduction_money."'
                ,update_date = '".date("Y-m-d H:i:s")."'
            where
                register_id = '".$uuid."'
                and product_no = $product_no
        ";
    $this->wrdb->update($query);
    return "1";
}

public function SellerInfo(){
    $uuid = $_SESSION["login_info"]["uuid"];
    $sellerInfo = [];
    $query = "
            select
               *
            from
              seller_company
            where uuid ='".$uuid."'
 
        ";
    $this->rodb->query($query);
    while($row = $this->rodb->next_row()){
        $sellerInfo = $row;
    }
    return $sellerInfo;
}

public function CategorySearch($data){
    $category_type = [];
    $query = "
            select
              DISTINCT category_type2
            from
              product_category        
            where
                category_type1 = '".$data["category_type1"]."'
        ";
    $this->rodb->query($query);
    while($row = $this->rodb->next_row()){
        $category_type[] = $row;
    }
    return $category_type;
}

    public function RegCategory(){
        $category = [];
        $query = "
            select
              DISTINCT category_type1 as category_type1
            from
              product_category     
             where del_yn ='N'
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $category[] = $row;
        }
        return $category;
    }

    public function SessionCategory(){
        $category= [];
        $query = "
            select
             *
            from
              product_category
            where del_yn = 'N'
            order by sort1 asc, sort2 asc
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $category[] = $row;
        }
        return $category;
    }

    public function dupCheck($data){
        $query = "
			SELECT
				count(*) 
			FROM
				 company_consulting
			WHERE
				user_phone = '" . $data['u_tel'] . "'
				and del_yn ='N'
				limit 1
			";
        $consulting_dup = $this->rodb->simple_query($query);
        if ($consulting_dup != 0) {
            return 1;
        }
    }

    public function ConsultingReg($data){
        $dup_query = "
			SELECT
				count(*) 
			FROM
				 company_consulting
			WHERE
				user_phone = '" . $data['user_phone'] . "'
				and status in ('1','2') 
			";
        $consulting_dup = $this->rodb->simple_query($dup_query);
        if ($consulting_dup != 0) {
            return "2";
        }

        $query = "
          insert into
              company_consulting
          set
               company_name_department = '".$data['company_name_department']."',
               user_name = '".$data['user_name']."',
               user_phone = '".$data['user_phone']."',
               user_email = '".$data['user_email']."',
               status = '1',
              register_date = '".date("Y-m-d H:i:s")."'
      ";
        $idx = $this->wrdb->insert($query);
        if($idx){
            return" 1";
        }
        else {
            return null;
        }
    }


    public function SessionCategory2($data){
        $category= [];
        $category_type = $data['category_type1'];
        $query = "
            select
              DISTINCT category_type2
            from
              product_category 
            where  category_type1 = '".$data["category_type1"]."'
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $category[] = $row;
        }
        return $category;
    }

public function Category(){
    $category = [];
    $query = "
            select
              DISTINCT category_type2
            from
              product_category        
        ";
    $this->rodb->query($query);
    while($row = $this->rodb->next_row()){
        $category[] = $row;
    }
    return $category;
}

}