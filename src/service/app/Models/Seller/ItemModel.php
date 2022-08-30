<?php

namespace App\Models\Seller;
use App\Models\CommonModel;

class ItemModel extends CommonModel
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



public function ItemUpdateSubmit($files, $data){
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
    $product_no = $data["product_no"];
    $uuid = $_SESSION["login_info"]["uuid"];

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
                ,representative_image = '".$upload_representative."'
                ,product_image1 = '".$upload_image1."'
                ,product_image2 = '".$upload_image2."'            
                ,update_id  = '".$uuid."'      
                ,update_date = '".date("Y-m-d H:i:s")."'
            where
                register_id = '".$uuid."'
                and product_no = $product_no
        ";
    $this->wrdb->update($query);

}

}
header("Content-Type:text/html;charset=EUC-KR");?>