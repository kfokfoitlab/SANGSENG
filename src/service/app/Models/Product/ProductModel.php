<?php
namespace App\Models\Seller;
use App\Models\CommonModel;

class ProductModel extends CommonModel
{
    public function Register($files, $data, $table_name = "seller_product"){
        /* $uploads_dir = './uploads';*/
        $allowed_ext = array('jpg','jpeg','png','gif','pdf');

// ���� ����
        $error = $files['representative_image']['error'];
        $name = $files['representative_image']['name'];
        $exploded_file = explode(".",$name);
        $ext = array_pop($exploded_file);
        $target_dir = ROOTPATH."/public/uploads/upload_files/";
        $file_tmp_name = $files["representative_image"]["tmp_name"];
        $file_ext = pathinfo($files["representative_image"]["name"], PATHINFO_EXTENSION);
        $file_new_name = uniqid().".".$file_ext;
        $email= $_SESSION["login_info"]["email"] ;
        $product_ranking = 9999;
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
// Ȯ���� Ȯ��
        if( !in_array($ext, $allowed_ext) ) {
            echo "������ �ʴ� Ȯ�����Դϴ�.";
            exit;
        }
        $product_no = date("YmdHis").$idx;
// ���� �̵�
        move_uploaded_file($file_tmp_name,$target_dir. $file_new_name);

        // 1:���δ��,5:����(�Ǹ���),7:�ݷ�
        $status = '5';
        $query = "
            insert into
                ".$table_name."
            set
                 product_no = '".$product_no."'
                ,status = '".$status."'
                ,product_category = '".$data["product_category"]."'
                ,product_name = '".$data["product_price"]."'
                ,product_price = '".$data["product_name"]."'
                ,product_quantity = '".$data["product_quantity"]."'
                ,product_start = '".$data["product_start"]."'
                ,product_end = '".$data["product_end"]."'
                ,product_surtax = '".$data["product_surtax"]."'
                ,delivery_cycle = '".$data["delivery_cycle"]."'
                ,representative_image = '".$name."'
                ,register_date = '".date("Y-m-d H:i:s")."'
                ,register_id = '".$email."'
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

}
