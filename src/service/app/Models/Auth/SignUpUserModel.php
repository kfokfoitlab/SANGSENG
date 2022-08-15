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

    public function Register($files, $data, $table_name = "user")
    { //{{{

        helper(["uuid_v4", "specialchars"]);
        $uuid = gen_uuid_v4();

        // image upload
        $file = $files["walfare_card_file"];
        if($file["error"] == 0){
            $new_welfare_card_uuid = $this->uploadFiles($file, null);
            $welfare_card_uuid = ",welfare_card_uuid = '".$new_welfare_card_uuid."'";
        }
        else {
            $welfare_card_uuid = ",welfare_card_uuid = null";
        }

        // calcurating impairment score
        $impairment_score = 0;
        foreach($data["impairment"]["assistive_device"] as $score){
            $impairment_score += @(int)$score;
        }
        foreach($data["impairment"]["degree"] as $score){
            $impairment_score += @(int)$score;
        }
        foreach($data["impairment"]["physical_ability"] as $score){
            $impairment_score += @(int)$score;
        }

        $detail = specialchars($data["impairment"]["detail"]);
        $detail0 = str_replace("\n", "\\n", $detail);
        $detail0 = str_replace("\r", "\\r", $detail0);
        $detail0 = str_replace("\t", "\\t", $detail0);

        $remark = specialchars($data["impairment"]["remark"]);
        $remark = str_replace("\n", "\\n", $remark);
        $remark = str_replace("\r", "\\r", $remark);
        $remark = str_replace("\t", "\\t", $remark);

        $data["impairment"]["detail"] = $detail;
        $data["impairment"]["remark"] = $remark;

        $impairment = json_encode($data["impairment"], JSON_UNESCAPED_UNICODE);

        // status == 0:가입신청, 1:심사중, 5:승인,7:거절, 9: 탈퇴	
        $status = '5';

        $sbs = (@$data["sbs"] == "y")? 1 : 0;
        $ads = (@$data["ads"] == "y")? 1 : 0;

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
                ,name = '".$data["name"]."'
                ,email = '".$data["email"]."'
                ,password = SHA2('".$salt."', 256)
                ,password_hash = '".$password_hash."'
                ,phone = '".$data["phone"]."'
                ,tel = '".$data["tel"]."'
                ,fax = '".$data["fax"]."'
                ,post_code = '".$data["post_code"]."'
                ,address = '".$data["address"]."'
                ,address_detail = '".$data["address_detail"]."'
                ,coordinate = ".$coordinate."
                ,impairment = '".$impairment."'
                ,impairment_score = ".$impairment_score."
                ".$welfare_card_uuid."
                ,sbs = ".$sbs."
                ,ads = ".$ads."
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

    public function dupCheck($email, $table_name = "user")
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
