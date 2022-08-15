<?php
namespace App\Models\Management\User;
use App\Models\CommonModel;

class ProfileModel extends CommonModel
{
    private $table_name = "user";

    public function getProfile($uuid)
    { //{{{
        $data = [];

        $query = "
            select
                *
                ,ST_X(coordinate) as latitude
                ,ST_Y(coordinate) as logitude
            from
                ".$this->table_name."
            where
                uuid = '".$uuid."'
            limit
                1
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();

            $address = explode(" ", $row["address"]);
            $row["address_short"] = $address[0]." ".$address[1];

        $data = $row;

        return $data;

    } //}}}

    public function Update($section, $file, $data)
    { //{{{
        switch($section){
            case "section1":
                $this->UpdateSection1(@$file["profile_img"], $data);
                break;

            case "section2":
                $this->UpdateSection2(@$file["welfare_img"], $data);
                break;

            case "section3":
                $this->UpdateSection3($file, $data);
                break;
        }

        return 1;

    } //}}}

    // 기업이 북마크 등록함.
    public function Bookmark($type, $company_uuid, $user_uuid)
    { //{{{

        if($type == "add"){
            $query = "
                insert ignore into
                    favorites_user
                set
                     user_uuid = '".$user_uuid."'
                    ,company_uuid = '".$company_uuid."'
                    ,register_date = '".date("Y-m-d H:i:s")."'
            ";
        }
        else {
            $query = "
                delete from
                    favorites_company
                where
                    user_uuid = '".$user_uuid."' and
                    company_uuid = '".$company_uuid."'
            ";
        }

        $this->wrdb->insert($query);

        return 1;


    } //}}}


    /**
     * Update Section1
     */
    private function UpdateSection1($file, $data)
    { //{{{
        // image upload
        if($file["error"] == 0){
            // 기존 파일 있으면 업데이트
            $query = "
                select
                    profile_img_uuid
                from
                    ".$this->table_name."
                where
                    uuid = '".$data["uuid"]."'
                limit 1
            ";
            $origin_profile_img_uuid = $this->rodb->simple_query($query);
            $new_profile_img_uuid = $this->uploadFiles($file, $origin_profile_img_uuid);
            $profile_img_uuid = ",profile_img_uuid = '".$new_profile_img_uuid."'";

            $_SESSION["login_info"]["profile_img_uuid"] = $new_profile_img_uuid;
        }
        else {
            $profile_img_uuid = ",profile_img_uuid = null";
            $_SESSION["profile_img_uuid"] = "";
        }

        // coordinate
        $coor_x = @(float)$data["coordinate_x"];
        $coor_y = @(float)$data["coordinate_y"];
        $coordinate = "POINT(".$coor_x.", ".$coor_y.")";

        $query = "
            update
                ".$this->table_name."
            set
                 name = '".$data["name"]."'
                ,email = '".$data["email"]."'
                ,phone = '".$data["phone"]."'
                ,tel = '".$data["tel"]."'
                ,fax = '".$data["fax"]."'
                ,post_code = '".$data["post_code"]."'
                ,address = '".$data["address"]."'
                ,address_detail = '".$data["address_detail"]."'
                ,coordinate = ".$coordinate."
                ".$profile_img_uuid."
                ,update_date = '".date("Y-m-d H:i:s")."'
            where
                uuid = '".$data["uuid"]."'
            limit 1
        ";
        $this->wrdb->update($query);

        return 1;
        
    } //}}}

    /**
     * Update Section2
     */
    private function UpdateSection2($file, $data)
    { //{{{
        helper(["specialchars"]);

        // image upload
        if($file["error"] == 0){
            // 기존 파일 있으면 업데이트
            $query = "
                select
                    welfare_card_uuid
                from
                    ".$this->table_name."
                where
                    uuid = '".$data["uuid"]."'
                limit 1
            ";
            $origin_welfare_card_uuid = $this->rodb->simple_query($query);
            $new_welfare_card_uuid = $this->uploadFiles($file, $origin_welfare_card_uuid);
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

        $query = "
            update
                ".$this->table_name."
            set
                 impairment = '".$impairment."'
                ,impairment_score = ".$impairment_score."
                ".$welfare_card_uuid."
                ,update_date = '".date("Y-m-d H:i:s")."'
            where
                uuid = '".$data["uuid"]."'
            limit 1
        ";
        $this->wrdb->update($query);

        return 1;
        
    } //}}}

    /**
     * Update Section3
     */
    private function UpdateSection3($file, $data)
    { //{{{

        $query = "
            update
                ".$this->table_name."
            set
                 sns_homepage = '".$data["homepage"]."'
                ,sns_blog = '".$data["blog"]."'
                ,sns_facebook = '".$data["facebook"]."'
                ,sns_twitter = '".$data["twitter"]."'
                ,sns_linkedin = '".$data["linkedin"]."'
                ,sns_youtube = '".$data["youtube"]."'
                ,update_date = '".date("Y-m-d H:i:s")."'
            where
                uuid = '".$data["uuid"]."'
            limit 1
        ";
        $this->wrdb->update($query);

        return 1;
        
    } //}}}
}
