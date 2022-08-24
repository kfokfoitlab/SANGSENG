<?php

namespace App\Models\Seller;
use App\Models\CommonModel;

class SellerModel extends CommonModel
{
    public function Register($files, $data, $table_name = "seller_product"){
        /* $uploads_dir = './uploads';*/
        $allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG');

       /* $error = $files['representative_image']['error'];
        $representative_image = $files['representative_image']['name'];
        $exploded_file = explode(".",$representative_image);
        $ext = array_pop($exploded_file);
        $target_dir = ROOTPATH."/public/uploads/upload_files/";
        $file_tmp_name = $files["representative_image"]["tmp_name"];
        $file_ext = pathinfo($files["representative_image"]["name"], PATHINFO_EXTENSION);
        $file_new_name = uniqid().".".$file_ext;*/

        $file_ext1 = pathinfo($files["product_image1"]["name"], PATHINFO_EXTENSION);
        $file_new_name1 = uniqid().".".$file_ext;

        $file_ext2 = pathinfo($files["product_image2"]["name"], PATHINFO_EXTENSION);
        $file_new_name2 = uniqid().".".$file_ext;


        $uuid = $_SESSION["login_info"]["uuid"];
        $product_image1 = $files['product_image1']['name'];
        $product_image2 = $files['product_image2']['name'];
        $product_ranking = 9999;
        $status = '5';
      /*  if( $error != UPLOAD_ERR_OK ) {
            switch( $error ) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    echo "������ �ʹ� Ů�ϴ�. ($error)";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    echo "������ ÷�ε��� �ʾҽ��ϴ�. ($error)";
                    break;
                default:
                    echo "������ ����� ���ε���� �ʾҽ��ϴ�. ($error)";
            }
            exit;
        }

        if( !in_array($ext, $allowed_ext) ) {
            echo "������ �ʴ� Ȯ�����Դϴ�.";
            exit;
        }*/
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
              ,representative_image = '".$file_new_name."'
              ,product_image1 = '".$file_new_name1."'
              ,product_image2 = '".$file_new_name2."'
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
                    echo "������ �ʴ� Ȯ�����Դϴ�.";
                    exit;
                }

                if( $error != UPLOAD_ERR_OK ) {
                    switch( $error ) {
                        case UPLOAD_ERR_INI_SIZE:
                        case UPLOAD_ERR_FORM_SIZE:
                            echo "������ �ʹ� Ů�ϴ�. ($error)";
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            echo "������ ÷�ε��� �ʾҽ��ϴ�. ($error)";
                            break;
                        default:
                            echo "������ ����� ���ε���� �ʾҽ��ϴ�. ($error)";
                    }
                    exit;
                }
                move_uploaded_file($file_tmp_name,$target_dir. $file_new_name);
            }
            $error = $files['representative_image']['error'];
            $representative_image = $files['representative_image']['name'];
            $exploded_file = explode(".",$representative_image);
            $ext = array_pop($exploded_file);
            $target_dir = ROOTPATH."/public/uploads/upload_files/";
            $file_tmp_name = $files["representative_image"]["tmp_name"];
            $file_ext = pathinfo($files["representative_image"]["name"], PATHINFO_EXTENSION);
            $file_new_name = uniqid().".".$file_ext;
            if( $error != UPLOAD_ERR_OK ) {
                switch( $error ) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        echo "������ �ʹ� Ů�ϴ�. ($error)";
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        echo "������ ÷�ε��� �ʾҽ��ϴ�. ($error)";
                        break;
                    default:
                        echo "������ ����� ���ε���� �ʾҽ��ϴ�. ($error)";
                }
                exit;
            }

            if( !in_array($ext, $allowed_ext) ) {
                echo "������ �ʴ� Ȯ�����Դϴ�.";
                exit;
            }
            move_uploaded_file($file_tmp_name,$target_dir. $file_new_name);
            return 1;
        }
        else {
            return null;
        }
    }
}
header("Content-Type:text/html;charset=EUC-KR");?>