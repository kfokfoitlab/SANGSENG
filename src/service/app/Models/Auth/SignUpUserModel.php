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
        $allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG','JPG','PDF','BMP','bmp','GIF');

        $buyer_documents_ori =  str_replace('&','＆', $files["buyer_documents"]["name"]);
        $upload_buyer_documents_ori = "buyer_documents";
        $upload_buyer_documents_image = uniqid().".".pathinfo($files["buyer_documents"]["name"], PATHINFO_EXTENSION);
        $this->uploadFileNew($files,$upload_buyer_documents_image,$allowed_ext,$upload_buyer_documents_ori);


        helper(["uuid_v4", "specialchars"]);
        $uuid = gen_uuid_v4();
        // status == 0:가입신청, 1:심사중, 5:승인,7:거절, 9: 탈퇴	
        $status = '1';
        $del_yn = 'N';
        $receive_yn  = (@$data["sbs"] == "Y")? 'Y' : 'N';
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
    public function dupCheck($email)
    {
        $query = "
			SELECT
				count(*) 
			FROM
				seller_company
			WHERE
				email = '" . $email. "'
				limit 1
			";
//			echo $query;
        $seller = $this->rodb->simple_query($query);
        if ($seller != 0) {
            return 1;
        }
            $query = "
			SELECT
				count(*)
			FROM
				buyer_company
			WHERE
				email = '" . $email . "'
				limit 1
			";
        $buyer = $this->rodb->simple_query($query);
        if ($buyer != 0) {
                return 2;
            }
        return 3;
    }


    public function buyerCheck($email){
        $query = "
            select
                count(*)
            from
                buyer_company
            where
                email = '".$email."'
            limit 1
        ";
        return $this->rodb->simple_query($query);
    }
    public function sellerCheck($email){
        $query = "
            select
                count(*)
            from
                seller_company
            where
                email = '".$email."'
            limit 1
        ";
        return $this->rodb->simple_query($query);
    }
}
