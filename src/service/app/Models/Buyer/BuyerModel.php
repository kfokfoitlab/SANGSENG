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
            limit 5
           
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data["data"][] = $row;

        }
        return $data;
    }


    public function Register($files, $data, $table_name = "seller_product"){
        /* $uploads_dir = './uploads';*/
        $allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG');
        $error = $files['representative_image']['error'];
        $representative_image = $files['representative_image']['name'];
        $exploded_file = explode(".",$representative_image);
        $ext = array_pop($exploded_file);
        $target_dir = ROOTPATH."/public/uploads/upload_files/";
        $file_tmp_name = $files["representative_image"]["tmp_name"];
        $file_ext = pathinfo($files["representative_image"]["name"], PATHINFO_EXTENSION);
        $file_new_name = uniqid().".".$file_ext;
        $uuid = $_SESSION["login_info"]["uuid"];
        $product_image1 = $files['product_image1']['name'];
        $product_image2 = $files['product_image2']['name'];
        $product_ranking = 9999;
        $status = '5';
        if( $error != UPLOAD_ERR_OK ) {
            switch( $error ) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    echo "파일이 너무 큽니다. ($error)";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    echo "파일이 첨부되지 않았습니다. ($error)";
                    break;
                default:
                    echo "파일이 제대로 업로드되지 않았습니다. ($error)";
            }
            exit;
        }
        if( !in_array($ext, $allowed_ext) ) {
            echo "허용되지 않는 확장자입니다.";
            exit;
        }
        for($i =1; $i<=$data["cnt"]; $i++){
            $product_no = date("YmdHis").$i;
            $price_num ="product_price".$i;
            $quantity_no ="product_quantity".$i;
            $query = "
          insert into
              ".$table_name."
          set
               product_no = '".$product_no."'
              ,status = '".$status."'
              ,product_category = '".$data["product_category"]."'
              ,product_name = '".$data["product_name"]."'
              ,product_price = '".$data["$price_num"]."'
              ,product_quantity = '".$data["$quantity_no"]."'
              ,product_start = '".$data["product_start"]."'
              ,product_end = '".$data["product_end"]."'
              ,product_surtax = '".$data["product_surtax"]."'
              ,delivery_cycle = '".$data["delivery_cycle"]."'
              ,product_detail = '".$data["product_detail"]."'
              ,representative_image = '".$representative_image."'
              ,product_image1 = '".$product_image1."'
              ,product_image2 = '".$product_image2."'
              ,register_date = '".date("Y-m-d H:i:s")."'
              ,register_id = '".$uuid."'
              ,product_ranking = '".$product_ranking."'
      ";
            $idx = $this->wrdb->insert($query);
        }
        if($idx){
            for($i =1; $i <=2; $i++){
                $imege_name = "product_image".$i;
                $error = $files["$imege_name"]['error'];
                $name = $files["$imege_name"]['name'];
                $exploded_file = explode(".",$name);
                $ext = array_pop($exploded_file);
                $target_dir = ROOTPATH."/public/uploads/upload_files/";
                $file_tmp_name = $files["$imege_name"]["tmp_name"];
                $file_ext = pathinfo($files["$imege_name"]["name"], PATHINFO_EXTENSION);
                $file_new_name = uniqid().".".$file_ext;

                if( !in_array($ext, $allowed_ext) ) {
                    echo "허용되지 않는 확장자입니다.";
                    exit;
                }

                if( $error != UPLOAD_ERR_OK ) {
                    switch( $error ) {
                        case UPLOAD_ERR_INI_SIZE:
                        case UPLOAD_ERR_FORM_SIZE:
                            echo "파일이 너무 큽니다. ($error)";
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            echo "파일이 첨부되지 않았습니다. ($error)";
                            break;
                        default:
                            echo "파일이 제대로 업로드되지 않았습니다. ($error)";
                    }
                    exit;
                }
                move_uploaded_file($file_tmp_name,$target_dir. $file_new_name);
            }
            move_uploaded_file($file_tmp_name,$target_dir. $file_new_name);
            return 1;
        }
        else {
            return null;
        }
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
                product_no =20220825101801 
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
    //1:승인대기,2:진행중,3:구매기업 승인,5:계약완료,7:반려,9:계약취소
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
            $ranking["data"]= $row;

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
            where 
                product_category =$value
           order by 
              register_date  desc
           
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $list["data"]= $row;

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