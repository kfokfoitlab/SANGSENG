<?php
namespace App\Models\Product;
use App\Models\CommonModel;

class CategoryModel extends CommonModel
{
    private $table_name = "product_category";

    public function getListData($data)
    { // {{{
        $items = array();

        $common_query = " 1 and del_yn != 'Y' ";

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

    public function Register($data){

        $category_type1 = $data['category_type1'];
        $category_type2 = $data['category_type2'];
        $category_type3 = $data['category_type3'];

        $query = "
            insert into
                ".$this->table_name."
            set
                category_type1 ='".$category_type1."'
                ,category_type2 = '".$category_type2."'
                ,category_type3 = '".$category_type3."'
                ,register_date = '".date("Y-m-d H:i:s")."'
                ,del_yn = 'N'
        ";
       // echo $query;
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

    public function noticeDelete($data)
    {
        $query = "
			UPDATE
				".$this->table_name."
			SET
			    delete_date = '".date("Y-m-d H:i:s")."'
                ,delete_id = 'admin'
				,del_yn = 'Y'
				,board_status = '2'
			WHERE
				idx = ".$data["idx"]."
			LIMIT 1
			";

        $this->wrdb->update($query);

        return 1;
    }

    public function getNoticeBoard($data){
        $query = "
			SELECT
				*
			FROM
				".$this->table_name."
			WHERE
				idx = ".$data["idx"]."
			LIMIT 1
			";

        $this->rodb->query($query);
        $data = $this->rodb->next_row();

        return $data;

    }

    public function noticeUpdate($data,$files, $table_name = "notice_board"){
        $allowed_ext = array();
        $upload_file_ori = "upload_file";
        if($files["upload_file"]["name"] != "") {
            $upload_file_ori_new = $files["upload_file"]["name"];
            $upload_file = uniqid() . "." . pathinfo($files["upload_file"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files, $upload_file, $allowed_ext, $upload_file_ori);
        }else{
            $upload_file = $data["upload_file_ori_name"];
            $upload_file_ori_new = $data["upload_face_ori"];
        }
        $title = addslashes($data['title']);
        $content = addslashes($data['content']);
        $content = str_replace("\r\n", "<br>", $content);
        $title = str_replace("\r\n", "<br>", $title);
        $query = "
            update
                ".$table_name."
            set
                board_status = '".$data["board_status"]."'
                ,user_id = 'admin'
                ,title = '".$title."'
                ,content = '".$content."'
                ,upload_file = '".$upload_file."'
                ,upload_file_ori = '".$upload_file_ori_new."'
                ,update_date = '".date("Y-m-d H:i:s")."'
                ,update_id = 'admin'
            where
                idx = ".$data["idx"]."
        ";
        //echo $query;
        $this->wrdb->update($query);
        return "1";
    }

}