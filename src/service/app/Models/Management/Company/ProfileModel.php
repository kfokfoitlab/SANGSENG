<?php
namespace App\Models\Management\Company;
use App\Models\CommonModel;

class ProfileModel extends CommonModel
{
    private $table_name = "company";

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

        $data = $row;

        return $data;

    } //}}}

    public function Update($section, $file, $data)
    { //{{{
        switch($section){
            case "section1":
                $this->UpdateSection1($file, $data);
                break;

            case "section3":
                $this->UpdateSection3($file, $data);
                break;
        }

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
            $_SESSION["login_info"]["profile_img_uuid"] = "";
        }

        // coordinate
        $coor_x = @(float)$data["coordinate_x"];
        $coor_y = @(float)$data["coordinate_y"];
        $coordinate = "POINT(".$coor_x.", ".$coor_y.")";

        $query = "
            update
                ".$this->table_name."
            set
                 manager_email = '".$data["manager_email"]."'
                ,manager_name = '".$data["manager_name"]."'
                ,did_tel = '".$data["did_tel"]."'
                ,gen_tel = '".$data["gen_tel"]."'
                ,phone = '".$data["phone"]."'
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
