<?php
namespace App\Models\TermsHistory;
use App\Models\CommonModel;

class TermsHistoryModel extends CommonModel
{
    private $table_name = "terms";

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

    public function Detail($data)
    { //{{{
        $idx = $data['idx'];
        $data = [];

        $query = "
            select
                *
            from
                ".$this->table_name."
            where
                idx = '".$idx."'
            limit
                1
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();
        $data = $row;

        return $data;
    } //}}}

    public function Update($data)
    { //{{{
        $query = "
			UPDATE
				".$this->table_name."
			SET
			contents = '".$data['contents']."'
			,update_date = '".date("Y-m-d H:i:s")."'
			WHERE
				idx = ".$data["idx"]."
			LIMIT 1
			";
        $this->wrdb->update($query);
        return 1;

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

    public function ContentsUpdate($data){
        $idx = $data['idx'];
        $data = [];

        $query = "
            select
                *
            from
                ".$this->table_name."
            where
                idx = '".$idx."'
            limit
                1
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();
        $data = $row;
        return $data;

    }


    public function statusUpdate($data)
    {
        if($data['status'] == '2'){
        $update_query = "terms_status = '".$data["status"]."' ,  use_date = '".date("Y-m-d H:i:s")."' ";
        }
        if($data['status'] == '3'){
        $update_query = "terms_status = '".$data["status"]."' , unused_date = '".date("Y-m-d H:i:s")."' ";
        }

        $query = "
			UPDATE
				".$this->table_name."
			SET
			$update_query
			WHERE
				idx = ".$data["idx"]."
			LIMIT 1
			";

        $this->wrdb->update($query);

        return 1;
    }

}


