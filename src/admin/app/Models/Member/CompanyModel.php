<?php
namespace App\Models\Member;
use App\Models\CommonModel;

class CompanyModel extends CommonModel
{
    private $table_name = "seller_company";

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

    public function Confirm($uuid, $status)
    { //{{{
        $query = "
            update
                ".$this->table_name."
            set
                status = '".$status."'
            where
                uuid = '".$uuid."'
            limit 1
        ";
        $this->wrdb->update($query);

        return 1;
    } //}}}

    public function Update($files, $data)
    { //{{{

        // image upload
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

}


