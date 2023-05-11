<?php
namespace App\Models\Product;
use App\Models\CommonModel;

class CategoryModel extends CommonModel
{
    private $table_name = "product_category";

    public function getListData($data)
    { // {{{
        $items = array();

        $common_query = " 1 ";

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

    public function Delete($idx){
        $query = "
            update
                product_category
            set
                del_yn = 'Y'
            where
                idx = '".$idx."'
        ";
        $this->wrdb->update($query);
        return "1";
    }

    public function Detail($idx)
    { //{{{
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

    public function CategorySort(){
        $data =[];
        $query = "
            select
             distinct category_type1, sort1
            from
              ".$this->table_name."
            order by sort1
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data[] = $row;
        }
        return $data;
    }

    public function CategorySearch($data){
        $category2 =[];
        $query = "
            select
               category_type2,sort2
            from
              product_category        
            where
                category_type1 = '".$data["category_type1"]."'
            order by sort2
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $category2[] = $row;
        }
        return $category2;
    }

    public function SortUpdate($data){
        if($data["type"] == 1){
            for($i = 1; $i <=$data['count']; $i++) {
            $query = "
            update
                " . $this->table_name . "
          set
                sort1 = '" . $data["sort1_" . $i] . "'
                ,update_date = '" . date("Y-m-d H:i:s") . "'
            where
                category_type1 = '" . $data["category_type1_" . $i] . "'
        ";
            $this->wrdb->update($query);
          }
            return 1;
        }else{
            for($i = 1; $i <=$data['count']; $i++) {
            $query = "
            update
                " . $this->table_name . "
               set
                sort2 = '" . $data["sort2_" . $i] . "'
                ,update_date = '" . date("Y-m-d H:i:s") . "'
            where
                category_type2 = '" . $data["category_type2_" . $i] . "'
                ";
                   $this->wrdb->update($query);
            }
            return 2;
        }
    }

    public function Update($data)
    { //{{{

        $query = "
            update
                " . $this->table_name . "
          set
                 category_type1 = '" . $data["category_type1"] . "'
                ,category_type2 = '" . $data["category_type2"] . "'
                ,category_type3 = '" . $data["category_type3"] . "'
                ,sort1 = '" . $data["sort1"] . "'
                ,sort2 = '" .$data["sort2"] . "'
                ,update_date = '" . date("Y-m-d H:i:s") . "'
            where
                idx = '" . $data["idx"] . "'
            limit 1
        ";
        $this->wrdb->update($query);

        $product_query = "
            update
                seller_product
          set
                 product_category = '" . $data["category_type1"] . "'
                ,product_category2 = '" . $data["category_type2"] . "'
                ,update_date = '" . date("Y-m-d H:i:s") . "'
            where
                product_category = '" . $data["old_category_type1"] . "'
                and product_category2 = '" . $data["old_category_type2"] . "'
        ";
        $this->wrdb->update($product_query);
        return 1;
    }

    public function Register($data){
        $category_type1 = $data['category_type1'];
        $category_type2 = $data['category_type2'];
        $category_type3 = $data['category_type3'];
        $sort1 = $data['sort1'];
        $sort2 = $data['sort2'];

        $query = "
            insert into
                ".$this->table_name."
            set
                category_type1 ='".$category_type1."'
                ,category_type2 = '".$category_type2."'
                ,category_type3 = '".$category_type3."'
                ,sort1 = '".$sort1."'
                ,sort2 = '".$sort2."'
               
                ,register_date = '".date("Y-m-d H:i:s")."'
                ,del_yn = 'N'
        ";
        $idx = $this->wrdb->insert($query);

        if($idx){
            return "1";
        }
        else {
            return null;
        }
    }

    public function statusUpdate($data)
    {
        $query = "
			UPDATE
				".$this->table_name."
			SET
				board_status = ".$data["status"]."
			WHERE
				idx = ".$data["idx"]."
			LIMIT 1
			";

        $this->wrdb->update($query);

        return 1;
    }


}