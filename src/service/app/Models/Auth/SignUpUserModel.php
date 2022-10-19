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
        $allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG','JPG','PDF');
        $buyer_documents_ori = $files["buyer_documents"]["name"];
        $upload_buyer_documents_ori = "buyer_documents";
        $upload_buyer_documents_image = uniqid().".".pathinfo($files["buyer_documents"]["name"], PATHINFO_EXTENSION);
        $this->uploadFileNew($files,$upload_buyer_documents_image,$allowed_ext,$upload_buyer_documents_ori);

        helper(["uuid_v4", "specialchars"]);
        $uuid = gen_uuid_v4();
        // status == 0:가입신청, 1:심사중, 5:승인,7:거절, 9: 탈퇴	
        $status = '1';
        $del_yn = 'N';
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
                ,interest_cleaning = '".$data["interest_cleaning"]."'            
                ,receive_yn = '".$receive_yn ."'
                ,del_yn = '".$del_yn ."'
                ,register_date = '".date("Y-m-d H:i:s")."'
                ,register_id ='".$uuid."'
                ,buyer_documents = '".$upload_buyer_documents_image."'
                ,buyer_documents_ori = '".$buyer_documents_ori."'
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
