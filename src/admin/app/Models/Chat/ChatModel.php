<?php
namespace App\Models\Chat;
use App\Models\CommonModel;

class ChatModel extends CommonModel
{
    private $table_name = "chat";

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
            else if($val["data"] == "user_name"){
                $filtering[] = "
                    user_uuid IN (
                        select
                            uuid
                        from
                            user
                        where
                            name like '%".$val["search"]["value"]."%'
                    )
                ";
            }
            else if($val["data"] == "company_name"){
                $filtering[] = "
                    company_uuid IN (
                        select
                            uuid
                        from
                            company
                        where
                            company_name like '%".$val["search"]["value"]."%'
                    )
                ";
            }
            else if($val["data"] == "update_date"){
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
                ".$this->table_name."_channel
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
                        name
                    from
                        user
                    where
                        uuid = ".$this->table_name."_channel.user_uuid
                    limit 1
                ) as user_name
                ,(
                    select
                        company_name
                    from
                        company
                    where
                        uuid = ".$this->table_name."_channel.company_uuid
                    limit 1
                ) as company_name
                ,(
                    select
                        count(idx)
                    from
                        ".$this->table_name."
                    where
                        channel_uuid = ".$this->table_name."_channel.uuid
                ) as chat_count
            from
                ".$this->table_name."_channel
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
                channel_uuid = '".$uuid."'
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){

            if($row["sender_type"] == "company"){
                $query1 = "
                    select
                        *
                    from
                        company
                    where
                        uuid = '".$row["sender_uuid"]."'
                ";
                $this->wrdb->query($query1);
                $row1 = $this->wrdb->next_row();
                $row["name"] = $row1["company_name"];
                $row["profile_img_uuid"] = $row1["profile_img_uuid"];
            }
            else {
                $query1 = "
                    select
                        *
                    from
                        user
                    where
                        uuid = '".$row["sender_uuid"]."'
                ";
                $this->wrdb->query($query1);
                $row1 = $this->wrdb->next_row();
                $row["name"] = $row1["name"];
                $row["profile_img_uuid"] = $row1["profile_img_uuid"];
            }

            $data[] = $row;
        }

        return $data;
    } //}}}
}


