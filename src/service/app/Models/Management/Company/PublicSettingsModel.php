<?php
namespace App\Models\Management\Company;
use App\Models\CommonModel;

class PublicSettingsModel extends CommonModel
{
    private $table_name = "company_public";

    public function getData($uuid)
    { //{{{
        $data = [];

        $query = "
            select
                *
                ,ST_X(t1.coordinate) as latitude
                ,ST_Y(t1.coordinate) as logitude
            from
                company t1
                left join
                ".$this->table_name." t2
            on
                t1.uuid = t2.company_uuid
            where
                t1.uuid = '".$uuid."'
            limit
                1
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();

        $photos = json_decode($row["photos"], true);
        $row["photos"] = $photos;

        $data = $row;

        return $data;

    } //}}}

    public function Update($company_uuid, $files, $data)
    { //{{{
        
        helper("specialchars");

        // 자동 승인 설정
        $config_approve = $this->getConfiguration("approve");
        // 비공개
        if($data["public_status"] == 3){
            $status = 3;
        }
        // 심사등록
        else if($config_approve["approve_register_public_company"] == "1" && $data["public_status"] == 5){
            $status = 1;
        }
        // 자동등록
        else if($config_approve["approve_register_public_company"] == "0" && $data["public_status"] == 5){
            $status = 5;
        }


        // file handling (upload & remove)
        $file_items = $files["photos"];
        $file_field_name = "photos";
        $files_query = ",".$file_field_name." = null";

        $files_array = $this->fileHandle($file_items, $data["pre_file"], $data["remove_file"]);

        $files_json = json_encode($files_array, JSON_UNESCAPED_UNICODE);
        $files_query = ",".$file_field_name." = '".$files_json."'";


        $query = "
            replace into
                ".$this->table_name."
            set
                 company_uuid = '".$company_uuid."'
                ,status = '".$status."'
                ,description = '".addslashes($data["description"])."'
                ,business_type = '".specialchars($data["business_type"])."'
                ,business_item = '".specialchars($data["business_item"])."'
                ,founded_date = '".$data["founded_date"]."'
                ,major_business = '".specialchars($data["major_business"])."'
                ".$files_query."
                ,sns_homepage = '".@$data["sns_homepage"]."'
                ,sns_facebook = '".@$data["sns_facebook"]."'
                ,sns_twitter = '".@$data["sns_twitter"]."'
                ,sns_instagram = '".@$data["sns_instagram"]."'
                ,sns_linkedin = '".@$data["sns_linkedin"]."'
                ,sns_youtube = '".@$data["sns_youtube"]."'
                ,register_date = '".date("Y-m-d H:i:s")."'
        ";
        $idx = $this->wrdb->insert($query);

        return $idx;

    } //}}}
}
