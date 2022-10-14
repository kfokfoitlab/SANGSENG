<?php

namespace App\Models\Seller;
use App\Models\CommonModel;

class ItemModel extends CommonModel
{
    public function Register($files, $data, $table_name = "seller_product"){
        $allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG');

        if($files["representative_image"]["name"] != ""){
            $upload_representative_ori = "representative_image";
            $upload_representative = uniqid().".".pathinfo($files["representative_image"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_representative,$allowed_ext,$upload_representative_ori);
        }
        if($files["product_image1"]["name"] != ""){
            $upload_image1_ori = "product_image1";
            $upload_image1 = uniqid().".".pathinfo($files["product_image1"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_image1,$allowed_ext,$upload_image1_ori);
        }
        if($files["product_image2"]["name"] != ""){
            $upload_image2_ori = "product_image2";
            $upload_image2 = uniqid().".".pathinfo($files["product_image2"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_image2,$allowed_ext,$upload_image2_ori);
        }
        if($files["detail_img"]["name"] != ""){
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


        $contribution = $data["product_price"]/$data["seller_sales"];
        $workers = $mild_disabled+($severely_disabled*2);
        $reduction = $contribution * $workers;
        $reduction = round($reduction,4);
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
              ,detail_img = '".$upload_detail_image."'
              ,reduction = '".$reduction."'
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
                         uuid ='".$uuid."'
                         and disability_degree ='1'
                    limit 1
                ";
    $this->rodb->query($severely_disabled_count);
    $severely_disabled_cnt = $this->rodb->next_row();
    $severely_disabled = $severely_disabled_cnt["severely_disabled_cnt"];


    $contribution = $data["product_price"]/$data["seller_sales"];
    $workers = $mild_disabled+($severely_disabled*2);
    $reduction = $contribution * $workers;
    $reduction = round($reduction,4);

    $allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG');
	$upload_representative = $data["representative_image_ori_name"];
	$upload_image1 = $data["product_image1_ori_name"];
	$upload_image2 = $data["product_image2_ori_name"];
   
    $upload_representative_ori = "representative_image";
	if($files["representative_image"]["name"] != "") {
		$upload_representative = uniqid() . "." . pathinfo($files["representative_image"]["name"], PATHINFO_EXTENSION);
		$this->uploadFileNew($files, $upload_representative, $allowed_ext, $upload_representative_ori);
	}

    $upload_image1_ori = "product_image1";
	if($files["product_image1"]["name"] != "") {
		$upload_image1 = uniqid() . "." . pathinfo($files["product_image1"]["name"], PATHINFO_EXTENSION);
		$this->uploadFileNew($files, $upload_image1, $allowed_ext, $upload_image1_ori);
	}

    $upload_image2_ori = "product_image2";
	if($files["product_image2"]["name"] != "") {
		$upload_image2 = uniqid() . "." . pathinfo($files["product_image2"]["name"], PATHINFO_EXTENSION);
		$this->uploadFileNew($files, $upload_image2, $allowed_ext, $upload_image2_ori);
	}
    if($files["detail_img"]["name"] != ""){
        $upload_detail_image_ori = "detail_img";
        $upload_detail_image = uniqid().".".pathinfo($files["detail_img"]["name"], PATHINFO_EXTENSION);
        $this->uploadFileNew($files,$upload_detail_image,$allowed_ext,$upload_detail_image_ori);
    }

    $product_no = $data["product_no"];
    $status = 3;
    $query = "
            update
                seller_product
            set
                 product_category = '".$data["product_category"]."'
                ,product_name = '".$data["product_name"]."'
                 ,product_price = '".$data["product_price"]."'
                ,product_quantity= '".$data["product_quantity"]."'
                 ,product_start= '".$data["product_start"]."'
                ,product_end = '".$data["product_end"]."'
                ,product_surtax = '".$data["product_surtax"]."'
                ,delivery_cycle = '".$data["delivery_cycle"]."'
                ,product_detail = '".$data["product_detail"]."'
                ,status = '".$status."'
                ,representative_image = '".$upload_representative."'
                ,product_image1 = '".$upload_image1."'
                ,product_image2 = '".$upload_image2."'
                ,detail_img = '".$upload_detail_image."'            
                ,update_id  = '".$uuid."'      
                ,reduction  = '".$reduction."'  
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
    $data = [];
    $query = "
            select
               *
            from
              seller_company
            where uuid ='".$uuid."'
 
        ";
    $this->rodb->query($query);
    while($row = $this->rodb->next_row()){
        $data = $row;
    }
    return $data;
}

}