<?php
namespace App\Models\Management\Company;
use App\Models\CommonModel;

class ApplicationModel extends CommonModel
{
    private $table_name = "application";

    public function getList($company_uuid, $limit = null)
    { //{{{
        $limit = "";
        if($limit){
            $limit = "limit ".$limit;
        }

        $data = [];
        $query = "
            select
                 *
                ,(
                    select
                        profile_img_uuid
                    from
                        company
                    where
                        uuid = ".$this->table_name.".company_uuid
                    limit 1
                ) as company_img_uuid
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
                        db_job_profession
                    where
                        idx = ".$this->table_name.".profession
                    limit 1
                ) as profession_title
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
                        count(*)
                    from
                        application_receipt
                    where
                        application_uuid = ".$this->table_name.".uuid
                ) as receipt_count
            from
                ".$this->table_name."
            where
                company_uuid = '".$company_uuid."'
            order by
                idx desc
            ".$limit."
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){

            $address = explode(" ", $row["address"]);
            $row["address_short"] = $address[0]." ".$address[1];

            $data[] = $row;
        }

        return $data;

    } //}}}

    public function getAllList($limit, $search_query = null)
    { //{{{

        $limit = "";
        if($limit){
            $limit = "limit ".$limit;
        }

        $where = [];
        $orderby = [];

        $where[] = 1;
        // where
        if($search_query["title"]){
            $where[] = "title like '%".$search_query["title"]."%'";
        }
        if($search_query["address"]){
            $where[] = "address like '".$search_query["address"]."%'";
        }
        if($search_query["profession"]){
            $where[] = "profession = ".$search_query["profession"]."";
        }
        if($search_query["employment_type"]){
            $where[] = "employment_type = ".$search_query["employment_type"]."";
        }
        if($search_query["career"]){
            $where[] = "career = ".$search_query["career"]."";
        }
        if($search_query["work_type"]){
            $where[] = "work_type = ".$search_query["work_type"]."";
        }
        $where_query = "where ".@join(" and ", $where);

        // order by
        if($search_query["sort"] == "rcd"){ // recommend
            $orderby[] = "recommended asc";
            $orderby[] = "idx desc";
        }
        else if($search_query["sort"] == "rct"){ // recent
            $orderby[] = "idx desc";
        }
        else if($search_query["sort"] == "exp"){ // expire
            $orderby[] = "receipt_expire_date asc";
        }
        else {
            $orderby[] = "recommended asc";
            $orderby[] = "idx desc";
        }
        $orderby_query = "order by ".@join(", ", $orderby);

        // limit
        $l_start = $search_query["length"] * ($search_query["page"] - 1);
        $l_end = $search_query["length"];
        $limit_query = "limit ".$l_start.", ".$l_end;

        $data = [];

        // total
        $query = "
            select
                count(*)
            from
                ".$this->table_name."
            ".$where_query."
        ";
        $data["count"] = $this->rodb->simple_query($query);

        $data["data"] = [];
        $query = "
            select
                 *
                ,(
                    select
                        profile_img_uuid
                    from
                        company
                    where
                        uuid = ".$this->table_name.".company_uuid
                    limit 1
                ) as company_img_uuid
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
            ".$where_query."
            ".$orderby_query."
            ".$limit_query."
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $address = explode(" ", $row["address"]);
            $row["address_short"] = $address[0]." ".$address[1];

            $row["bookmark"] = 0;
            // 북마킹 여부
            if(@$_SESSION["login"] == "success" && $_SESSION["login_info"]["type"] == "user"){
                $query1 = "
                    select
                        count(*)
                    from
                        favorites_recruit
                    where
                        user_uuid = '".$_SESSION["login_info"]["uuid"]."' and
                        application_uuid = '".$row["uuid"]."'
                    limit 1
                ";
                $bookmark = $this->wrdb->simple_query($query1);
                if($bookmark > 0){
                    $row["bookmark"] = 1;
                }

            }
            $data["data"][] = $row;
        }

        return $data;

    } //}}}

    public function getApplicationList($application_uuid)
    { //{{{
        $data = [];
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
                    limit 1
                 ) as user_name
                ,(
                    select
                        title
                    from
                        db_job_employment_type
                    where
                        idx = t2.category_employment_type
                    limit 1
                ) as employment_type_title
                ,(
                    select
                        title
                    from
                        db_job_profession
                    where
                        idx = t2.category_profession
                    limit 1
                ) as profession_title
                ,(
                    select
                        title
                    from
                        db_job_career
                    where
                        idx = t2.category_career
                    limit 1
                ) as career_title
            from
                ".$this->table_name."_receipt t1
                inner join
                resume t2
            on
                t1.resume_uuid = t2.uuid
            where
                t1.application_uuid = '".$application_uuid."'
            order by
                t1.idx desc
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data[] = $row;
        }

        return $data;

    } //}}}

    public function getApplicationReceiptData($application_uuid, $resume_uuid)
    { //{{{
        $data = [];

        // receipt
        $query = "
            select
                *
            from
                ".$this->table_name."_receipt
            where
                application_uuid = '".$application_uuid."' and
                resume_uuid = '".$resume_uuid."'
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();
        $data["receipt"] = $row;

        // resume
        $query = "
            select
                 *
                ,(
                    select
                        title
                    from
                        db_job_career
                    where
                        idx = resume.category_career
                ) as career_title
                ,(
                    select
                        title
                    from
                        db_job_employment_type
                    where
                        idx = resume.category_employment_type
                ) as employment_type_title
                ,(
                    select
                        title
                    from
                        db_job_profession
                    where
                        idx = resume.category_profession
                ) as profession_title
            from
                resume
            where
                uuid = '".$resume_uuid."'
            limit 1
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();
        $data["resume"] = $row;

        // user profile
        $query = "
            select
                *
            from
                user
            where
                uuid = '".$data["receipt"]["user_uuid"]."'
            limit 1
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();
            $address = explode(" ", $row["address"]);
            $row["address_short"] = $address[0]." ".$address[1];

        $data["user_profile"] = $row;


        return $data;

    } //}}}

    public function getRecommendedList($limit)
    { //{{{
        $limit = "";
        if($limit){
            $limit = "limit ".$limit;
        }

        $data = [];
        $query = "
            select
                 *
                ,(
                    select
                        profile_img_uuid
                    from
                        company
                    where
                        uuid = ".$this->table_name.".company_uuid
                    limit 1
                ) as company_img_uuid
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
                recommended = 1
            order by
                idx desc
            ".$limit."
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $address = explode(" ", $row["address"]);
            $row["address"] = $address[0]." ".$address[1];
            $data[] = $row;
        }

        return $data;

    } //}}}

    public function Detail($company_uuid, $uuid)
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
                company_uuid = '".$company_uuid."' and
                uuid = '".$uuid."'
            limit 1
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();

        $data = $row;



        return $data;

    } //}}}

    public function DetailRecruit($uuid)
    { //{{{
        $data = [];
        $query = "
            select
                *
                ,(
                    select
                        company_name
                    from
                        company
                    where
                        uuid = ".$this->table_name.".company_uuid
                    limit 1
                ) as company_name
                ,(
                    select
                        profile_img_uuid
                    from
                        company
                    where
                        uuid = ".$this->table_name.".company_uuid
                    limit 1
                ) as company_img_uuid
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
                        title
                    from
                        db_job_work_type
                    where
                        idx = ".$this->table_name.".work_type
                    limit 1
                ) as work_type_title
            from
                ".$this->table_name."
            where
                uuid = '".$uuid."'
            limit 1
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();

            $address = explode(" ", $row["address"]);
            $row["address_short"] = $address[0]." ".$address[1];

            $row["bookmark"] = 0;
            // 북마킹 여부
            if(@$_SESSION["login"] == "success" && $_SESSION["login_info"]["type"] == "user"){
                $query1 = "
                    select
                        count(*)
                    from
                        favorites_recruit
                    where
                        user_uuid = '".$_SESSION["login_info"]["uuid"]."' and
                        application_uuid = '".$row["uuid"]."'
                    limit 1
                ";
                $bookmark = $this->wrdb->simple_query($query1);
                if($bookmark > 0){
                    $row["bookmark"] = 1;
                }

            }


        $data = $row;

        return $data;

    } //}}}

    public function Create($company_uuid, $data)
    { //{{{
        helper(["specialchars", "uuid_v4"]);

        $uuid = gen_uuid_v4();

        $title = specialchars($data["title"]);

        $data["pay_min"] = (int)str_replace(",", "", $data["pay_min"]);
        $data["pay_max"] = (int)str_replace(",", "", $data["pay_max"]);

        // coordinate
        $coor_x = @(float)$data["coordinate_x"];
        $coor_y = @(float)$data["coordinate_y"];
        $coordinate = "POINT(".$coor_x.", ".$coor_y.")";

        $data["pay_negotiability"] = ($data["pay_negotiability"] == "y")?1:0;
        $data["social_insurance_1"] = ($data["social_insurance_1"] == "y")?1:0;
        $data["social_insurance_2"] = ($data["social_insurance_2"] == "y")?1:0;
        $data["social_insurance_3"] = ($data["social_insurance_3"] == "y")?1:0;
        $data["social_insurance_4"] = ($data["social_insurance_4"] == "y")?1:0;
        $data["severance_pay"] = ($data["severance_pay"] == "y")?1:0;
        $data["receipt_method_doc"] = ($data["receipt_method_doc"] == "y")?1:0;
        $data["receipt_method_interview"] = ($data["receipt_method_interview"] == "y")?1:0;

        // calcurating impairment score
        $impairment_score = 0;
        foreach($data["impairment"]["physical_ability"] as $score){
            $impairment_score += @(int)$score;
        }
        $impairment = json_encode($data["impairment"], JSON_UNESCAPED_UNICODE);

        $welfare = json_encode($data["welfare"], JSON_UNESCAPED_UNICODE);

        $query = "
            insert into
                ".$this->table_name."
            set
                 uuid = '".$uuid."'
                ,company_uuid = '".$company_uuid."'
                ,title = '".$title."'
                ,application_detail = '".addslashes($data["application_detail"])."'
                ,employment_type = ".$data["employment_type"]."
                ,career = ".$data["career"]."
                ,recruitment_number = ".$data["recruitment_number"]."
                ,profession = ".$data["profession"]."
                ,profession_detail = '".addslashes($data["profession_detail"])."'
                ,post_code = '".$data["post_code"]."'
                ,address = '".$data["address"]."'
                ,address_detail = '".$data["address_detail"]."'
                ,coordinate = ".$coordinate."
                ,work_type = ".$data["work_type"]."
                ,work_type_detail = '".addslashes($data["work_type_detail"])."'
                ,pay_type = '".$data["pay_type"]."'
                ,pay_min = ".$data["pay_min"]."
                ,pay_max = ".$data["pay_max"]."
                ,pay_negotiability = ".$data["pay_negotiability"]."
                ,pay_detail = '".addslashes($data["pay_detail"])."'
                ,social_insurance_1 = ".$data["social_insurance_1"]."
                ,social_insurance_2 = ".$data["social_insurance_2"]."
                ,social_insurance_3 = ".$data["social_insurance_3"]."
                ,social_insurance_4 = ".$data["social_insurance_4"]."
                ,insurance_detail = '".addslashes($data["insurance_detail"])."'
                ,severance_pay = ".$data["severance_pay"]."
                ,severance_pay_detail = '".addslashes($data["severance_pay_detail"])."'
                ,receipt_expire_date = '".$data["receipt_expire_date"]."'
                ,receipt_method_doc = ".$data["receipt_method_doc"]."
                ,receipt_method_interview = ".$data["receipt_method_interview"]."
                ,receipt_method_detail = '".addslashes($data["receipt_method_detail"])."'
                ,impairment = '".$impairment."'
                ,impairment_score = ".$impairment_score."
                ,preference_detail = '".addslashes($data["preference_detail"])."'
                ,additional_condition_detail = '".$data["additional_condition_detail"]."'
                ,welfare = '".$welfare."'
                ,welfare_detail = '".addslashes($data["welfare_detail"])."'
                ,register_date = '".date("Y-m-d H:i:s")."'
        ";
        $uuid = $this->wrdb->insert($query);

        return $uuid;

    } //}}}

    public function Update($company_uuid, $data)
    { //{{{
        helper(["specialchars", "uuid_v4"]);

        $uuid = $data["uuid"];

        $title = specialchars($data["title"]);

        $data["pay_min"] = (int)str_replace(",", "", $data["pay_min"]);
        $data["pay_max"] = (int)str_replace(",", "", $data["pay_max"]);

        // coordinate
        $coor_x = @(float)$data["coordinate_x"];
        $coor_y = @(float)$data["coordinate_y"];
        $coordinate = "POINT(".$coor_x.", ".$coor_y.")";

        $data["pay_negotiability"] = ($data["pay_negotiability"] == "y")?1:0;
        $data["social_insurance_1"] = ($data["social_insurance_1"] == "y")?1:0;
        $data["social_insurance_2"] = ($data["social_insurance_2"] == "y")?1:0;
        $data["social_insurance_3"] = ($data["social_insurance_3"] == "y")?1:0;
        $data["social_insurance_4"] = ($data["social_insurance_4"] == "y")?1:0;
        $data["severance_pay"] = ($data["severance_pay"] == "y")?1:0;
        $data["receipt_method_doc"] = ($data["receipt_method_doc"] == "y")?1:0;
        $data["receipt_method_interview"] = ($data["receipt_method_interview"] == "y")?1:0;

        // calcurating impairment score
        $impairment_score = 0;
        foreach($data["impairment"]["physical_ability"] as $score){
            $impairment_score += @(int)$score;
        }
        $impairment = json_encode($data["impairment"], JSON_UNESCAPED_UNICODE);

        $welfare = json_encode($data["welfare"], JSON_UNESCAPED_UNICODE);

        $query = "
            update
                ".$this->table_name."
            set
                 title = '".$title."'
                ,application_detail = '".addslashes($data["application_detail"])."'
                ,employment_type = ".$data["employment_type"]."
                ,career = ".$data["career"]."
                ,recruitment_number = ".(int)$data["recruitment_number"]."
                ,profession = ".$data["profession"]."
                ,profession_detail = '".addslashes($data["profession_detail"])."'
                ,post_code = '".$data["post_code"]."'
                ,address = '".$data["address"]."'
                ,address_detail = '".$data["address_detail"]."'
                ,coordinate = ".$coordinate."
                ,work_type = ".$data["work_type"]."
                ,work_type_detail = '".addslashes($data["work_type_detail"])."'
                ,pay_type = '".$data["pay_type"]."'
                ,pay_min = ".$data["pay_min"]."
                ,pay_max = ".$data["pay_max"]."
                ,pay_negotiability = ".$data["pay_negotiability"]."
                ,pay_detail = '".addslashes($data["pay_detail"])."'
                ,social_insurance_1 = ".$data["social_insurance_1"]."
                ,social_insurance_2 = ".$data["social_insurance_2"]."
                ,social_insurance_3 = ".$data["social_insurance_3"]."
                ,social_insurance_4 = ".$data["social_insurance_4"]."
                ,insurance_detail = '".addslashes($data["insurance_detail"])."'
                ,severance_pay = ".$data["severance_pay"]."
                ,severance_pay_detail = '".addslashes($data["severance_pay_detail"])."'
                ,receipt_expire_date = '".$data["receipt_expire_date"]."'
                ,receipt_method_doc = ".$data["receipt_method_doc"]."
                ,receipt_method_interview = ".$data["receipt_method_interview"]."
                ,receipt_method_detail = '".addslashes($data["receipt_method_detail"])."'
                ,impairment = '".$impairment."'
                ,impairment_score = ".$impairment_score."
                ,preference_detail = '".addslashes($data["preference_detail"])."'
                ,additional_condition_detail = '".$data["additional_condition_detail"]."'
                ,welfare = '".$welfare."'
                ,welfare_detail = '".addslashes($data["welfare_detail"])."'
                ,register_date = '".date("Y-m-d H:i:s")."'
            where
                uuid = '".$uuid."' and
                company_uuid = '".$company_uuid."'
        ";
        $this->wrdb->update($query);

        return 1; 

    } //}}}

    public function ApplicationList($user_uuid, $application_uuid)
    { //{{{
        $data = [];
        $query = "
            select
                *
            from
                ".$this->table_name."_receipt
            where
                user_uuid = '".$user_uuid."' and
                application_uuid = '".$application_uuid."'
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data[] = $row;
        }

        return $data;

    } //}}}

    public function CloseApplication($application_uuid)
    { //{{{
        $query = "
            update
                ".$this->table_name."
            set
                status = '3'
            where
                uuid = '".$application_uuid."'
            limit 1
        ";
        $this->wrdb->update($query);

        return 1;

    } //}}}

    public function DeleteApplication($application_uuid)
    { //{{{

        $query = "
            delete from
                ".$this->table_name."
            where
                uuid = '".$application_uuid."'
            limit 1
        ";
        $this->wrdb->update($query);

        return 1;

    } //}}}

    // 지원서 제출
    public function Applicated($user_uuid, $data)
    { //{{{
        $query = "
            select
                company_uuid
            from
                ".$this->table_name."
            where
                uuid = '".$data["application_uuid"]."'
            limit 1
        ";
        $company_uuid = $this->rodb->simple_query($query);

        $query = "
            insert into
                application_receipt
            set
                 application_uuid = '".$data["application_uuid"]."'
                ,company_uuid = '".$company_uuid."'
                ,user_uuid = '".$user_uuid."'
                ,resume_uuid = '".$data["resume_uuid"]."'
                ,remark = '".addslashes($data["remark"])."'
                ,register_date = '".date("Y-m-d H:i:s")."'
        ";
        $idx = $this->wrdb->insert($query);

        return $idx;

    } //}}}

    public function Bookmark($type, $application_uuid, $user_uuid)
    { //{{{

        if($type == "add"){
            $query = "
                insert ignore into
                    favorites_recruit
                set
                     user_uuid = '".$user_uuid."'
                    ,application_uuid = '".$application_uuid."'
                    ,register_date = '".date("Y-m-d H:i:s")."'
            ";
        }
        else {
            $query = "
                delete from
                    favorites_recruit
                where
                    user_uuid = '".$user_uuid."' and
                    application_uuid = '".$application_uuid."'
            ";
        }

        $this->wrdb->insert($query);

        return 1;


    } //}}}

    public function getBookmarkAllList($limit, $search_query, $user_uuid)
    { //{{{

        $limit = "";
        if($limit){
            $limit = "limit ".$limit;
        }

        $where = [];
        $orderby = [];

        $where[] = 1;
        // where
        $where[] = "t2.user_uuid = '".$user_uuid."'";
        $where_query = "where ".@join(" and ", $where);

        // order by
        $orderby[] = "t1.idx desc";
        $orderby_query = "order by ".@join(", ", $orderby);

        // limit
        $l_start = $search_query["length"] * ($search_query["page"] - 1);
        $l_end = $search_query["length"];
        $limit_query = "limit ".$l_start.", ".$l_end;

        $data = [];

        // total
        $query = "
            select
                count(*)
            from
                ".$this->table_name." t1
                inner join 
                favorites_recruit t2
            on
                t1.uuid = t2.application_uuid
            ".$where_query."
        ";
        $data["count"] = $this->rodb->simple_query($query);

        $data["data"] = [];
        $query = "
            select
                 *
                ,(
                    select
                        profile_img_uuid
                    from
                        company
                    where
                        uuid = t1.company_uuid
                    limit 1
                ) as company_img_uuid
                ,(
                    select
                        title
                    from
                        db_job_employment_type
                    where
                        idx = t1.employment_type
                    limit 1
                ) as employment_type_title
                ,(
                    select
                        title
                    from
                        db_job_career
                    where
                        idx = t1.career
                    limit 1
                ) as career_title
                ,(
                    select
                        title
                    from
                        db_job_profession
                    where
                        idx = t1.profession
                    limit 1
                ) as profession_title
                ,(
                    select
                        count(*)
                    from
                        application_receipt
                    where
                        application_uuid = t1.uuid
                ) as receipt_count
            from
                ".$this->table_name." t1
                inner join 
                favorites_recruit t2
            on
                t1.uuid = t2.application_uuid
            ".$where_query."
            ".$orderby_query."
            ".$limit_query."
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $address = explode(" ", $row["address"]);
            $row["address_short"] = $address[0]." ".$address[1];

            $row["bookmark"] = 0;
            // 북마킹 여부
            if(@$_SESSION["login"] == "success" && $_SESSION["login_info"]["type"] == "user"){
                $query1 = "
                    select
                        count(*)
                    from
                        favorites_recruit
                    where
                        user_uuid = '".$_SESSION["login_info"]["uuid"]."' and
                        application_uuid = '".$row["uuid"]."'
                    limit 1
                ";
                $bookmark = $this->wrdb->simple_query($query1);
                if($bookmark > 0){
                    $row["bookmark"] = 1;
                }

            }
            $data["data"][] = $row;
        }

        return $data;

    } //}}}

    // 채용 처리
    public function Result($application_uuid, $resume_uuid, $type)
    { //{{{

        if($type == "approve"){
            $query = "
                update
                    ".$this->table_name."_receipt
                set
                    status = '5'
                where
                    application_uuid = '".$application_uuid."' and
                    resume_uuid = '".$resume_uuid."'
                limit 1
            ";
        }
        else {
            $query = "
                update
                    ".$this->table_name."_receipt
                set
                    status = '9'
                where
                    application_uuid = '".$application_uuid."' and
                    resume_uuid = '".$resume_uuid."'
                limit 1
            ";
        }

        $this->wrdb->update($query);

        return 1;

    } //}}}
}
