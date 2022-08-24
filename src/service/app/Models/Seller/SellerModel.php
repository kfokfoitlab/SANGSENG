<?php

namespace App\Models\Seller;
use App\Models\CommonModel;
use function PHPUnit\Framework\equalTo;

class SellerModel extends CommonModel
{
    public function Register($files, $data, $table_name = "seller_product"){
        $allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG');
        $file_ext = pathinfo($files["representative_image"]["name"], PATHINFO_EXTENSION);
        $file_new_name3 = uniqid().".".$file_ext;
        $file_ext1 = pathinfo($files["product_image1"]["name"], PATHINFO_EXTENSION);
        $file_new_name1 = uniqid().".".$file_ext1;
        $file_ext2 = pathinfo($files["product_image2"]["name"], PATHINFO_EXTENSION);
        $file_new_name2 = uniqid().".".$file_ext2;
        $uuid = $_SESSION["login_info"]["uuid"];
        $company_name = $_SESSION["login_info"]["company_name"];
        $product_ranking = 9999;
        $status = '5';
        $product_no = date("YmdHis");
        $quantity_count = $data['cnt'];

        $query = "
          insert into
              ".$table_name."
          set
               product_no = '".$product_no."'
              ,status = '".$status."'
              ,product_category = '".$data["product_category"]."'
              ,product_name = '".$data["product_name"]."'
              ,product_price1 = '".$data["product_price1"]."'
              ,product_quantity1 = '".$data["product_quantity1"]."'
              ,product_price2 = '".$data["product_price2"]."'
              ,product_quantity2 = '".$data["product_quantity2"]."'
              ,product_price3 = '".$data["product_price3"]."'
              ,product_quantity3 = '".$data["product_quantity3"]."'
              ,product_price4 = '".$data["product_price4"]."'
              ,product_quantity4 = '".$data["product_quantity4"]."'
              ,product_price5 = '".$data["product_price5"]."'
              ,product_quantity5 = '".$data["product_quantity5"]."'
              ,product_start = '".$data["product_start"]."'
              ,product_end = '".$data["product_end"]."'
              ,product_surtax = '".$data["product_surtax"]."'
              ,delivery_cycle = '".$data["delivery_cycle"]."'
              ,product_detail = '".$data["product_detail"]."'
              ,representative_image = '".$file_new_name3."'
              ,product_image1 = '".$file_new_name1."'
              ,product_image2 = '".$file_new_name2."'
              ,register_date = '".date("Y-m-d H:i:s")."'
              ,register_id = '".$uuid."'
              ,quantity_count = '".$quantity_count."'
              ,company_name = '".$company_name."'
              ,product_ranking = '".$product_ranking."'
      ";
            $idx = $this->wrdb->insert($query);
        if($idx){
            for($i =1; $i <=3; $i++){
                $imege_name = "product_image".$i;
                if($i == 3){
                    $imege_name = "representative_image";
                }
                $error = $files["$imege_name"]['error'];
                $name = $files["$imege_name"]['name'];
                $exploded_file = explode(".",$name);
                $ext = array_pop($exploded_file);
                $target_dir = ROOTPATH."/public/uploads/upload_files/";
                $file_tmp_name = $files["$imege_name"]["tmp_name"];

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
                move_uploaded_file($file_tmp_name,$target_dir. ${"file_new_name".$i});
            }
            return 1;
        }
        else {
            return null;
        }
    }
}
header("Content-Type:text/html;charset=EUC-KR");?>