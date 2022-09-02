<?php
namespace App\Models\Auth;
use App\Models\CommonModel;

class SignUpUserModel extends CommonModel
{
    public function getTermsData($category, $table_name = "terms")
    { //{{{
        $query = "
            select
                *
            from
                ".$table_name."
            where
                category = '".$category."'
            limit 1
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();

        return $row;

    } //}}}

    public function Register($files, $data, $table_name = "buyer_company")
    { //{{{
       /* $uploads_dir = './uploads';*/
        $allowed_ext = array('jpg','jpeg','png','gif','pdf');

// 변수 정리
        $error = $files['buyer_documents']['error'];
        $name = $files['buyer_documents']['name'];
        $exploded_file = explode(".",$name);
        $ext = array_pop($exploded_file);
        $target_dir = ROOTPATH."/public/uploads/upload_files/";
        $file_tmp_name = $files["buyer_documents"]["tmp_name"];
        $file_ext = pathinfo($files["buyer_documents"]["name"], PATHINFO_EXTENSION);
        $file_new_name = uniqid().".".$file_ext;

        // echo $allowed_ext[0];
// 오류 확인
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
// 확장자 확인
      if( !in_array($ext, $allowed_ext) ) {
            echo "허용되지 않는 확장자입니다.";
            exit;
        }
// 파일 이동
        move_uploaded_file($file_tmp_name,$target_dir. $file_new_name);
        helper(["uuid_v4", "specialchars"]);
        $uuid = gen_uuid_v4();
        // status == 0:가입신청, 1:심사중, 5:승인,7:거절, 9: 탈퇴	
        $status = '5';
        $receive_yn  = (@$data["ads"] == "y")? 'Y' : 'N';
        if($data['tax_rate'] == null){
            $data['tax_rate'] = 10;
        }
        $salt = $data["password"];
        $query = "
            insert into
                ".$table_name."
            set
                 uuid = '".$uuid."'
                ,status = '".$status."'
                ,buyer_name = '".$data["buyer_name"]."'
                ,email = '".$data["email"]."'
                ,password = SHA2('".$salt."', 256)
                ,phone = '".$data["phone"]."'
                ,fax = '".$data["fax"]."'
                ,address = '".$data["address"]."'
                ,company_name = '".$data["company_name"]."'
                ,company_code = '".$data["company_code"]."'
                ,classification = '".$data["classification"]."'
                ,tax_rate = '".$data['tax_rate']."'
                ,workers = '".$data['workers']."'
                ,severely_disabled = '".$data['severely_disabled']."'
                ,mild_disabled = '".$data['mild_disabled']."'
                ,interest_office = '".$data["interest_office"]."'
                ,interest_daily = '".$data["interest_daily"]."'
                ,interest_computerized = '".$data["interest_computerized"]."'
                ,interest_food = '".$data["interest_food"]."'            
                ,receive_yn = '".$receive_yn ."'
                ,register_date = '".date("Y-m-d H:i:s")."'
                ,register_id ='".$uuid."'
                ,buyer_documents = '".$name."'
        ";
        $idx = $this->wrdb->insert($query);

        if($idx){
            return $uuid;
        }
        else {
            return null;
        }

    } //}}}

    public function dupCheck($email, $table_name = "buyer_company")
    { //{{{
        $query = "
            select
                count(*)
            from
                ".$table_name."
            where
                email = '".$email."'
            limit 1
        ";
        return $this->rodb->simple_query($query);
    } //}}}
}
