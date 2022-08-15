<?php
namespace App\Models\Database\Impairment;
use App\Models\CommonModel;

class TypeModel extends CommonModel
{
    private $table_name = "db_impairment_type";

    public function getListData($data)
    { // {{{
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
		if($data["search"]["value"]){
			$search_query  = " ";
		}
        // ---------------------------------------------- }}}

        // filtering {{{
        $filtering = array();
        foreach($data["columns"] as $key => $val){
            if(!@$val["search"]["value"]){
                continue;
            }
            else if($val["data"] == "registration_date"){
                $t = explode("~", $val["search"]["value"]);
                $filtering[] = "registration_date between '".$t[0]." 00:00:00' and '".$t[1]." 23:59:59'";
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
        $data = array();
        while($row = $this->rodb->next_row()){
            $row["num"] = $num--;

            $data[] = $row;
        }

        return array(
             "records_total" => $records_total
            ,"data" => $data
            ,"filtered_total" => $filtered_total
        );


    } // }}}

    public function showImpairmentList()
    { // {{{
        $query = "
            select
                *
            from
                ".$this->table_name."
            order by
                this_level, this_level_order
        ";
        $this->rodb->query($query);
        $data = [];
        while($row = $this->rodb->next_row()){
            if($row["this_level"] == 0){
                $data[0][] = $row;
            }
            else if($row["this_level"] == 1){
                $data[$row["level0_idx"]][] = $row;
            }
            else if($row["this_level"] == 2){
                $data[$row["level1_idx"]][] = $row;
            }
        }

        return $data;

    } // }}}

    public function getImpairmentList($this_level, $parent_idx)
    { // {{{
        if($parent_idx){
            $parent_query = " and level".($this_level-1)."_idx = ".$parent_idx;
        }
        $query = "
            select
                *
            from
                ".$this->table_name."
            where
                this_level = ".(int)$this_level."
                ".@$parent_query."
            order by
                this_level_order asc
        ";
        $this->rodb->query($query);
        $data = [];
        while($row = $this->rodb->next_row()){
            $data[] = $row;
        }

        return $data;
    } // }}}

    public function addImpairment($data)
    { // {{{

        $level0_idx = (@$data["level0_idx"])?$data["level0_idx"]:'null';
        $level1_idx = (@$data["level1_idx"])?$data["level1_idx"]:'null';

        if($data["this_level"] == 0){
            $level0_idx = 'null';
            $level1_idx = 'null';
        }
        else if($data["this_level"] == 1){
            $level1_idx = 'null';
        }

        $query = "
            select
                count(idx)
            from
                ".$this->table_name."
            where
                this_level = ".$data["this_level"]."
        ";
        $this_level_order = $this->rodb->simple_query($query);

        $query = "
            insert into
                ".$this->table_name."
            set
                 title = '".$data["new_title"]."'
                ,this_level = ".$data["this_level"]."
                ,this_level_order = ".$this_level_order."
                ,level0_idx = ".$level0_idx."
                ,level1_idx = ".$level1_idx."
        ";
        $new_idx = $this->wrdb->insert($query);
        return $new_idx;

    } // }}}

    public function updateImpairment($data)
    { // {{{
        $query = "
            update
                ".$this->table_name."
            set
                title = '".$data["title"]."'
            where
                idx = ".$data["idx"]."
            limit 1
        ";
        $this->wrdb->update($query);

        return true;

    } // }}}

    public function deleteImpairment($data)
    { // {{{

        // 하위분류 있으면 삭제 불가
        $query = "
            select
                count(idx)
            from
                ".$this->table_name."
            where
                level0_idx = ".$data["idx"]." or
                level1_idx = ".$data["idx"]."
        ";
        if($this->rodb->simple_query($query) > 0){
            return 0;
        }
        else{

            $query = "
                delete from
                    ".$this->table_name."
                where
                    idx = ".$data["idx"]."
                limit 1
            ";
            $this->wrdb->query($query);

            return 1;
        }

           
    } // }}}

    public function sortingImpairment($idx_list, $this_level)
    { // {{{
        if(@count($idx_list)){
            foreach($idx_list as $key => $val){
                $query = "
                    update
                        ".$this->table_name."
                    set
                        this_level_order = ".(int)$key."
                    where
                        this_level = ".(int)$this_level." and
                        idx = ".(int)$val."
                    limit 1
                ";
                $this->wrdb->query($query);
            }
        }

        return true;

    } // }}}
}
