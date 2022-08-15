<?php
namespace App\Models\Management\User;
use App\Models\CommonModel;

class ManageJobModel extends CommonModel
{
    private $table_name = "job";

    public function getList($user_uuid)
    { //{{{
        $data = [];
        $query = "
            select
                *
            from
                ".$this->table_name."
            where
                user_uuid = '".$user_uuid."'
            limit 1
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();
        $data = $row;

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
            $where[] = "t1.title like '%".$search_query["title"]."%'";
        }
        if($search_query["profession"]){
            $where[] = "t2.category_profession = ".$search_query["profession"]."";
        }
        if($search_query["employment_type"]){
            $where[] = "t2.category_employment_type = ".$search_query["employment_type"]."";
        }
        if($search_query["career"]){
            $where[] = "t2.category_career = ".$search_query["career"]."";
        }
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
                resume t2
            on
                t1.resume_uuid = t2.uuid
            ".$where_query."
        ";
        $data["count"] = $this->rodb->simple_query($query);

        $data["data"] = [];

        $query = "
            select
                t1.*
                ,t2.category_profession
                ,t2.category_employment_type
                ,t2.category_career
                ,t2.category_pay_type
                ,t2.category_pay
                ,(
                    select
                        address
                    from
                        user
                    where
                        uuid = t1.user_uuid
                    limit 1
                ) as address
                ,(
                    select
                        profile_img_uuid
                    from
                        user
                    where
                        uuid = t1.user_uuid
                    limit 1
                ) as profile_img_uuid
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
                        db_job_career
                    where
                        idx = t2.category_career
                    limit 1
                ) as career_title
                ,(
                    select
                        title
                    from
                        db_job_profession
                    where
                        idx = t2.category_profession
                    limit 1
                ) as profession_title
            from
                ".$this->table_name." t1
                inner join
                resume t2
            on
                t1.resume_uuid = t2.uuid
            ".$where_query."
            ".$orderby_query."
            ".$limit_query."
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()) {
            $address = explode(" ", $row["address"]);
            $row["address_short"] = $address[0]." ".$address[1];

            $data["data"][] = $row;
        }

        return $data;

    } //}}}

    public function Detail($user_uuid)
    { //{{{
        $data = [];

        $query = "
            select
                *
            from
                ".$this->table_name."
            where
                user_uuid = '".$user_uuid."'
            limit 1
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();

        // 북마킹 여부
        $row["bookmark"] = 0;
        if(@$_SESSION["login"] == "success" && $_SESSION["login_info"]["type"] == "company"){
            $query1 = "
                select
                    count(*)
                from
                    favorites_user
                where
                    company_uuid = '".$_SESSION["login_info"]["uuid"]."' and
                    user_uuid = '".$user_uuid."'
                limit 1
            ";
            $bookmark = $this->wrdb->simple_query($query1);
            if($bookmark > 0){
                $row["bookmark"] = 1;
            }

        }

        $data["data"] = $row;

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
                uuid = '".$row["resume_uuid"]."'
            limit 1
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();

        $data["resume"] = $row;

        return $data;

    } //}}}

    public function Create($user_uuid, $data)
    { //{{{
        helper(["specialchars"]);

        $query = "
            replace into
                ".$this->table_name."
            set
                 user_uuid = '".$user_uuid."'
                ,resume_uuid = '".$data["resume_uuid"]."'
                ,title = '".specialchars($data["title"])."'
                ,inline_pr = '".specialchars($data["inline_pr"])."'
                ,register_date = '".date("Y-m-d H:i:s")."' 
        ";
        $idx = $this->wrdb->insert($query);

        return $idx;

    } //}}}

    public function Update($user_uuid, $data)
    { //{{{
        helper(["specialchars"]);

        $query = "
            update
                ".$this->table_name."
            set
                 resume_uuid = '".$data["resume_uuid"]."'
                ,title = '".specialchars($data["title"])."'
                ,inline_pr = '".specialchars($data["inline_pr"])."'
                ,register_date = '".date("Y-m-d H:i:s")."' 
            where
                user_uuid = '".$user_uuid."'
            limit 1
        ";
        $this->wrdb->insert($query);

        return 1;

    } //}}}

    public function Delete($user_uuid, $idx)
    { //{{{
        $query = "
            delete from
                ".$this->table_name."
            where
                user_uuid = '".$user_uuid."' and
                idx = ".(int)$idx."
        ";
        $this->wrdb->query($query);

        return 1;

    } //}}}

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
                    favorites_user
                where
                    user_uuid = '".$user_uuid."' and
                    company_uuid = '".$company_uuid."'
            ";
        }

        $this->wrdb->insert($query);

        return 1;


    } //}}}

    public function getBookmarkAllList($limit, $search_query, $company_uuid)
    { //{{{
        $limit = "";
        if($limit){
            $limit = "limit ".$limit;
        }

        $where = [];
        $orderby = [];

        $where[] = 1;
        // where
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
                resume t2
            on
                t1.resume_uuid = t2.uuid
            ".$where_query."
        ";
        $data["count"] = $this->rodb->simple_query($query);

        $data["data"] = [];

        $query = "
            select
                t1.*
                ,t2.category_profession
                ,t2.category_employment_type
                ,t2.category_career
                ,t2.category_pay_type
                ,t2.category_pay
                ,(
                    select
                        address
                    from
                        user
                    where
                        uuid = t1.user_uuid
                    limit 1
                ) as address
                ,(
                    select
                        profile_img_uuid
                    from
                        user
                    where
                        uuid = t1.user_uuid
                    limit 1
                ) as profile_img_uuid
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
                        db_job_career
                    where
                        idx = t2.category_career
                    limit 1
                ) as career_title
                ,(
                    select
                        title
                    from
                        db_job_profession
                    where
                        idx = t2.category_profession
                    limit 1
                ) as profession_title
            from
                ".$this->table_name." t1
                inner join
                resume t2
            on
                t1.resume_uuid = t2.uuid
            ".$where_query."
            ".$orderby_query."
            ".$limit_query."
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()) {
            $address = explode(" ", $row["address"]);
            $row["address_short"] = $address[0]." ".$address[1];

            $data["data"][] = $row;
        }

        return $data;

    } //}}}
}
