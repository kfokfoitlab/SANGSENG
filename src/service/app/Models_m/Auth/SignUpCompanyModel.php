<?php
namespace App\Models_m\Auth;
use App\Models_m\CommonModel;

class SignUpCompanyModel extends CommonModel
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

    public function Register($files,$data, $table_name = "seller_company")
    { //{{{
        $allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG','JPG','PDF');
        if($files["seller_business_license"]["name"] != ""){
            $seller_business_license_ori =  str_replace('&','＆', $files["seller_business_license"]["name"]);
            $upload_seller_business_license_ori = "seller_business_license";
            $upload_seller_business_license_image = uniqid().".".pathinfo($files["seller_business_license"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_seller_business_license_image,$allowed_ext,$upload_seller_business_license_ori);
        }

        if($files["seller_documents"]["name"] != ""){
            $seller_documents_ori =  str_replace('&','＆', $files["seller_documents"]["name"]);
            $upload_seller_documents_ori = "seller_documents";
            $upload_seller_documents_image = uniqid().".".pathinfo($files["seller_documents"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_seller_documents_image,$allowed_ext,$upload_seller_documents_ori);
        }


        helper(["uuid_v4", "specialchars"]);
        $uuid = gen_uuid_v4();
        // status == 0:가입신청, 1:심사중, 5:승인,7:거절, 9: 탈퇴
        $status = '1';
        $del_yn = 'N';
        $receive_yn  = (@$data["sbs"] == "Y")? 'Y' : 'N';
        $salt = $data["password"];
        $query = "
            insert into
                ".$table_name."
            set
                 uuid = '".$uuid."'
                ,status = '".$status."'
                ,seller_name = '".$data["seller_name"]."'
                ,email = '".$data["email"]."'
                ,password = SHA2('".$salt."', 256)
                ,phone = '".$data["phone"]."'
                ,fax = '".$data["fax"]."'
                ,address = '".$data["address"]."'
                ,company_name = '".$data["company_name"]."'
                ,company_code = '".$data["company_code"]."'
                ,classification = '".$data["classification"]."'
                ,seller_sales = '".$data['seller_sales']."'       
                ,receive_yn = '".$receive_yn ."'
                ,register_date = '".date("Y-m-d H:i:s")."'
                ,register_id = '".$uuid."'
                 ,del_yn = '".$del_yn ."'
                ,seller_documents = '".$upload_seller_documents_image."'
                ,seller_business_license = '".$upload_seller_business_license_image."'
                ,seller_business_license_ori = '".$seller_business_license_ori."'
                ,seller_documents_ori = '".$seller_documents_ori."'
        ";
        $idx = $this->wrdb->insert($query);

        if($idx){
            return $uuid;
        }
        else {
            return null;
        }

    } //}}}

    public function dupCheck($email, $table_name = "seller_company")
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