<?php
namespace App\Models\Auth;
use App\Models\CommonModel;

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

    public function Register($data, $table_name = "company")
    { //{{{

        // business_number
        $business_number = str_replace("-", "", $data["business_number"]);

        // 중복체크
        $query = "
            select
                count(*)
            from
                ".$table_name."
            where
                business_number = '".$business_number."'
            limit 1
        ";
        if($this->rodb->simple_query($query) > 0){
            return null;
        }

        helper("uuid_v4");
        $uuid = gen_uuid_v4();


        // status == 0:가입신청, 1:심사중, 5:승인,7:거절, 9: 탈퇴	
        $status = '0';

        $sbs = (@$data["sbs"] == "y")? 1 : 0;
        $ads = (@$data["ads"] == "y")? 1 : 0;
        $agree_marketing = (@$data["agree_marketing"] == "y")? 1 : 0;
        $agree_newsletter = (@$data["agree_newsletter"] == "y")? 1 : 0;
        $agree_sms = (@$data["agree_sms"] == "y")? 1 : 0;

        // encoding password
        $salt = $data["password"];
        $password_hash = password_hash($salt, PASSWORD_BCRYPT, ["cost" => 10]);

        // coordinate
        $coor_x = @(float)$data["coordinate_x"];
        $coor_y = @(float)$data["coordinate_y"];
        $coordinate = "POINT(".$coor_x.", ".$coor_y.")";

        $query = "
            insert into
                ".$table_name."
            set
                 uuid = '".$uuid."'
                ,status = '".$status."'
                ,verification = 0
                ,company_name = '".$data["company_name"]."'
                ,business_number = '".$business_number."'
                ,password = SHA2('".$salt."', 256)
                ,password_hash = '".$password_hash."'
                ,ceo_name = '".$data["ceo_name"]."'
                ,manager_name = '".$data["manager_name"]."'
                ,manager_email = '".$data["manager_email"]."'
                ,did_tel = '".$data["did_tel"]."'
                ,gen_tel = '".$data["gen_tel"]."'
                ,phone = '".$data["phone"]."'
                ,fax = '".$data["fax"]."'
                ,post_code = '".$data["post_code"]."'
                ,address = '".$data["address"]."'
                ,address_detail = '".$data["address_detail"]."'
                ,coordinate = ".$coordinate."
                ,sbs = ".$sbs."
                ,ads = ".$ads."
                ,register_path = '".@$data["register_path"]."'
                ,agree_marketing = ".$agree_marketing."
                ,agree_newsletter = ".$agree_newsletter."
                ,agree_sms = ".$agree_sms."
                ,register_date = '".date("Y-m-d H:i:s")."'
        ";
        $idx = $this->wrdb->insert($query);

        if($idx){
            return $uuid;
        }
        else {
            return null;
        }

    } //}}}
}
