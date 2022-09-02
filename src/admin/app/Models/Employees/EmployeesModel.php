<?php
namespace App\Models\Employees;
use App\Models\CommonModel;

class EmployeesModel extends CommonModel
{
    private $table_name = "application";

    public function getListData($data)
    { // {{{
        $items = array();

        $common_query = " 1";

        // total records -------------------------------- {{{
        $query = "
            select
                count(*)
            from
                ".$this->table_name."
            where
                ".$common_query."
        ";
        $records_total = $this->rodb->simple_query($query);
        // ---------------------------------------------- }}}

		// Search --------------------------------------- {{{
		$search_query = null;
		if(@$data["search"]["value"]){
			$search_query  = " ";
		}
        // ---------------------------------------------- }}}

        // filtering {{{
        $filtering = array();
        foreach($data["columns"] as $key => $val){
            if(!@$val["search"]["value"]){
                continue;
            }
            else if($val["data"] == "progress"){
                if($val["search"]["value"] == "1"){
                    $filtering[] = "receipt_expire_date >= '".date("Y-m-d")."'";
                }
                else if($val["search"]["value"] == "2"){
                    $filtering[] = "receipt_expire_date < '".date("Y-m-d")."'";
                }
            }
            else if($val["data"] == "register_date"){
                $t = explode("~", $val["search"]["value"]);
                $filtering[] = "register_date between '".$t[0]." 00:00:00' and '".$t[1]." 23:59:59'";
            }
            else{
                $filtering[] = " (
                    lower(replace(".$val["data"].", ' ', '')) like '%".strtolower($val["search"]["value"])."%' or
                    lower(".$val["data"].") like '%".strtolower($val["search"]["value"])."%'
                )";
            }
        }
        $filtering_query = (count($filtering) > 0)? " and ".@join(" and ", $filtering):"";
        //debug($filtering_query);
        // ---------------------------------------------- }}}

        // filtered count ------------------------------- {{{
        $query = "
            select count(*)
            from
                ".$this->table_name."
            where
                ".$common_query."
                ".$search_query."
                ".$filtering_query."
        ";
        $filtered_total = $this->rodb->simple_query($query);
        // ---------------------------------------------- }}}

		// Pagination ----------------------------------- {{{
		$limit = $data["start"].", ".$data["length"];
        // ---------------------------------------------- }}}

		// Ordering ------------------------------------- {{{
        $order_arr = [];
        foreach($data["order"] as $val){
            $order_field_idx = $val["column"];
            $order_field = $data["columns"][$order_field_idx]["data"];
            $order_field = ($order_field == "num")? "idx":$order_field;
            $order_direction = $val["dir"];
            $order_arr[] = $order_field." ".$order_direction;
            
        }
        $order_query = @join(",", $order_arr);
        // ---------------------------------------------- }}}

        // query
        $query = "
            select
                 *
                ,(
                    select
                        title
                    from
                        db_job_employment_type
                    where
                        idx = ".$this->table_name.".employment_type
                    limit 1
                ) as employment_type_title
                ,(
                    select
                        title
                    from
                        db_job_career
                    where
                        idx = ".$this->table_name.".career
                    limit 1
                ) as career_title
                ,(
                    select
                        title
                    from
                        db_job_profession
                    where
                        idx = ".$this->table_name.".profession
                    limit 1
                ) as profession_title
                ,(
                    select
                        count(*)
                    from
                        application_receipt
                    where
                        application_uuid = ".$this->table_name.".uuid
                ) as receipt_count
            from
                ".$this->table_name."
            where
                ".$common_query."
                ".$search_query."
                ".$filtering_query."
            order by
                ".$order_query."
            limit
                ".$limit."
        ";
        $this->rodb->query($query);
        $num = $filtered_total - $data["start"];
        while($row = $this->rodb->next_row()){
            $row["num"] = $num--;

            unset($row["coordinate"]);

            // 진행여부
            $exp_time = strtotime($row["receipt_expire_date"]);
            $now_time = time();
            if($now_time < $exp_time){
                $row["progress"] = 1;
            }
            else {
                $row["progress"] = 0;
            }

            $items[] = $row;
        }

        return array(
             "records_total" => $records_total
            ,"data" => $items
            ,"filtered_total" => $filtered_total
        );


    } // }}}

    public function Detail($uuid)
    { //{{{
        $data = [];

        $query = "
            select
                *
            from
                ".$this->table_name."
            where
                uuid = '".$uuid."'
            limit
                1
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();
        $data["application"] = $row;


        $query = "
            select
                *
                ,(
                    select
                        name 
                    from
                        user
                    where
                        uuid = t1.user_uuid
                ) as user_name
                ,(
                    select
                        title 
                    from
                        resume
                    where
                        uuid = t1.resume_uuid
                ) as resume_title
            from
                ".$this->table_name."_receipt t1
            where
                application_uuid = '".$uuid."'
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data["receipt"][] = $row;
        }

        return $data;
    } //}}}

    public function Update($files, $data)
    { //{{{
        helper(["specialchars"]);

        // profile image upload
        $file = $files["profile_img"];
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
        }
        else {
            $profile_img_uuid = ",profile_img_uuid = null";
        }

        // welfare card upload
        $file = $files["welfare_img"];
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

        // coordinate
        $coor_x = @(float)$data["coordinate_x"];
        $coor_y = @(float)$data["coordinate_y"];
        $coordinate = "POINT(".$coor_x.", ".$coor_y.")";


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
                ".$welfare_card_uuid."
                ,impairment = '".$impairment."'
                ,impairment_score = ".$impairment_score."
                ,sns_homepage = '".$data["sns_homepage"]."'
                ,sns_blog = '".$data["sns_blog"]."'
                ,sns_facebook = '".$data["sns_facebook"]."'
                ,sns_twitter = '".$data["sns_twitter"]."'
                ,sns_linkedin = '".$data["sns_linkedin"]."'
                ,sns_youtube = '".$data["sns_youtube"]."'
                ,update_date = '".date("Y-m-d H:i:s")."'
            where
                uuid = '".$data["uuid"]."'
            limit 1
        ";
        $this->wrdb->update($query);

    } //}}}

    public function Recommend($type, $uuid)
    { //{{{

        $type = ($type == "enable")? "recommended = 1":"recommended = null"; 

        $query = "
            update
                ".$this->table_name."
            set
                ".$type."
            where
                uuid = '".$uuid."'
            limit 1
        ";
        $this->wrdb->update($query);

        return 1;

    } //}}}
}

