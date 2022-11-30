<?php
namespace App\Models\Video;
use App\Models\CommonModel;

class VideoModel extends CommonModel
{
    private $table_name = "promotion_video";

    public function getListData($data)
    { // {{{
        $items = array();

        $common_query = " 1  ";

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

    public function Register($data,$files){
        helper(["uuid_v4", "specialchars"]);
        $uuid = gen_uuid_v4();

        $allowed_ext = array('mp4');
        if($files["main_video"]["name"] != ""){
            $main_video_ori = $files["main_video"]["name"];
            $upload_main_video_ori = "main_video";
            $upload_main_video_image = uniqid().".".pathinfo($files["main_video"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_main_video_image,$allowed_ext,$upload_main_video_ori);
        }

        if($files["sub_video1"]["name"] != ""){
            $sub_video1_ori = $files["sub_video1"]["name"];
            $upload_sub_video1_ori = "sub_video1";
            $upload_sub_video1_image = uniqid().".".pathinfo($files["sub_video1"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_sub_video1_image,$allowed_ext,$upload_sub_video1_ori);
        }

        if($files["sub_video2"]["name"] != ""){
            $sub_video2_ori = $files["sub_video2"]["name"];
            $upload_sub_video2_ori = "sub_video2";
            $upload_sub_video2_image = uniqid().".".pathinfo($files["sub_video2"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_sub_video2_image,$allowed_ext,$upload_sub_video2_ori);
        }

        $video_title = addslashes($data['video_title']);
        $video_comment = addslashes($data['video_comment']);
        $video_comment = str_replace("\r\n", "<br>", $video_comment);
        $video_title = str_replace("\r\n", "<br>", $video_title);

        $query = "
            insert into
                ".$this->table_name."
            set
                uuid = '".$uuid."'
                ,video_status = '1'
                ,video_title = '".$video_title."'
                ,video_comment = '".$video_comment."'
                ,main_video = '".$upload_main_video_image."'
                ,main_video_ori = '".$main_video_ori."'
                ,sub_video1 = '".$upload_sub_video1_image."'
                ,sub_video1_ori ='".$sub_video1_ori."'
                ,sub_video2 = '".$upload_sub_video2_image."'
                ,sub_video2_ori = '".$sub_video2_ori."'
                ,del_yn ='N'
                ,register_date = '".date("Y-m-d")."'
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
        if($data['status'] == 9){
            $query = "
			UPDATE
				".$this->table_name."
			SET
				video_status = ".$data["status"]."
				,del_yn ='Y'
			WHERE
				idx = ".$data["idx"]."
			LIMIT 1
			";
            $this->wrdb->update($query);
            return 1;
        }else{
        $query = "
			UPDATE
				".$this->table_name."
			SET
				video_status = ".$data["status"]."
			WHERE
				idx = ".$data["idx"]."
			LIMIT 1
			";
        $this->wrdb->update($query);
        return 1;
        }
    }



}