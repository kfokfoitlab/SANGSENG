<?php
namespace App\Models;
use App\Models\CommonModel;

class CompanyModel extends CommonModel
{
    private $table_name = "company";

    public function getAllList($limit, $search_query = null)
    { //{{{
        $limit = "";
        if($limit){
            $limit = "limit ".$limit;
        }

        $where = [];
        $orderby = [];

        $where[] = "t1.status = '5'";
        // where
        if($search_query["title"]){
            $where[] = "t1.company_name like '%".$search_query["title"]."%'";
        }
        if($search_query["address"]){
            $where[] = "t1.address like '".$search_query["title"]."%'";
        }
        if($search_query["business_type"]){
            $where[] = "t2.business_type like '".$search_query["title"]."%'";
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
                company t1
                inner join
                company_public t2
            on
                t1.uuid = t2.company_uuid
            ".$where_query."
        ";
        $data["count"] = $this->rodb->simple_query($query);

        $data["data"] = [];
        $query = "
            select
                *
                ,(
                    select
                        count(*)
                    from
                        application
                    where
                        company_uuid = t1.uuid and
                        status = '5'
                ) as application_count
            from
                company t1
                inner join
                company_public t2
            on
                t1.uuid = t2.company_uuid
            ".$where_query."
            ".$orderby_query."
            ".$limit_query."
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $address = explode(" ", $row["address"]);
            $row["address_short"] = $address[0]." ".$address[1];

            $row["description_plain"] = strip_tags($row["description"]);

            $row["bookmark"] = 0;
            // 북마킹 여부
            if(@$_SESSION["login"] == "success" && $_SESSION["login_info"]["type"] == "user"){
                $query1 = "
                    select
                        count(*)
                    from
                        favorites_company
                    where
                        user_uuid = '".$_SESSION["login_info"]["uuid"]."' and
                        company_uuid = '".$row["uuid"]."'
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

    public function Detail($uuid)
    { //{{{
        $data = [];
        $query = "
            select
                *
                ,(
                    select
                        count(*)
                    from
                        application
                    where
                        company_uuid = t1.uuid and
                        status = '5'
                ) as application_count
            from
                company t1
                inner join
                company_public t2
            on
                t1.uuid = t2.company_uuid
            where
                t1.status = '5'
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();

        $row["bookmark"] = 0;
        // 북마킹 여부
        if(@$_SESSION["login"] == "success" && $_SESSION["login_info"]["type"] == "user"){
            $query1 = "
                select
                    count(*)
                from
                    favorites_company
                where
                    user_uuid = '".$_SESSION["login_info"]["uuid"]."' and
                    company_uuid = '".$row["uuid"]."'
                limit 1
            ";
            $bookmark = $this->wrdb->simple_query($query1);
            if($bookmark > 0){
                $row["bookmark"] = 1;
            }

        }

        $address = explode(" ", $row["address"]);
        $row["address_short"] = $address[0]." ".$address[1];

        return $row;

    } //}}}

    // 사업 유형 검색용
    public function getBusinessType()
    { //{{{
        $data = [];
        $query = "
            select
                distinct(business_type) as business_type
            from
                company_public
            order by
                1
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data[] = $row["business_type"];
        }

        return $data;

    } //}}}

    public function Bookmark($type, $company_uuid, $user_uuid)
    { //{{{

        if($type == "add"){
            $query = "
                insert ignore into
                    favorites_company
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

    public function getBookmarkAllList($limit, $search_query, $user_uuid)
    { //{{{
        $limit = "";
        if($limit){
            $limit = "limit ".$limit;
        }

        $where = [];
        $orderby = [];

        $where[] = "t1.status = '5'";
        // where
        $where[] = "company_uuid IN (
            select
                company_uuid
            from
                favorites_company
            where
                user_uuid = '".$user_uuid."'
        )";
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
                company t1
                inner join
                company_public t2
            on
                t1.uuid = t2.company_uuid
            ".$where_query."
        ";
        $data["count"] = $this->rodb->simple_query($query);

        $data["data"] = [];
        $query = "
            select
                *
                ,(
                    select
                        count(*)
                    from
                        application
                    where
                        company_uuid = t1.uuid and
                        status = '5'
                ) as application_count
            from
                company t1
                inner join
                company_public t2
            on
                t1.uuid = t2.company_uuid
            ".$where_query."
            ".$orderby_query."
            ".$limit_query."
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $address = explode(" ", $row["address"]);
            $row["address_short"] = $address[0]." ".$address[1];

            $row["description_plain"] = strip_tags($row["description"]);

            $row["bookmark"] = 0;
            // 북마킹 여부
            if(@$_SESSION["login"] == "success" && $_SESSION["login_info"]["type"] == "user"){
                $query1 = "
                    select
                        count(*)
                    from
                        favorites_company
                    where
                        user_uuid = '".$_SESSION["login_info"]["uuid"]."' and
                        company_uuid = '".$row["uuid"]."'
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
}
