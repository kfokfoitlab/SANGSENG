<?php
namespace App\Models\Product;
use App\Models\CommonModel;

class ProductModel extends CommonModel
{
    private $table_name = "seller_product";

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
        //debug($filtering_query);
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

            $items[] = $row;
        }

        return array(
            "records_total" => $records_total
        ,"data" => $items
        ,"filtered_total" => $filtered_total
        );


    } // }}}

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

        $query = "
            select
                *
            from
                resume
            where
                user_uuid = '".$row["user_uuid"]."' and
                uuid = '".$row["resume_uuid"]."'
            limit 1
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();
        $data["resume"] = $row;

        return $data;
    } //}}}

    public function Update($files, $data)
    { //{{{
      /*  $allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG');
        $upload_face_ori = "upload_face";
        $upload_face = $data["upload_face_ori_name"];
        $upload_card = $data["upload_card_ori_name"];
        if($files["upload_face"]["name"] != "") {
            $upload_face = uniqid() . "." . pathinfo($files["upload_face"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files, $upload_face, $allowed_ext, $upload_face_ori);
        }
        $upload_card_ori = "upload_card";
        if($files["upload_face"]["name"] != "") {
            $upload_card = uniqid() . "." . pathinfo($files["upload_card"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files, $upload_card, $allowed_ext, $upload_card_ori);
        }

        // profile image upload
        $file = $files["representative_image"];
        if($file["error"] == 0){
            // 기존 파일 있으면 업데이트
            $query = "
                select
                    profile_img_uuid
                from
                    ".$this->table_name."
                where
                    idx = '".$data["idx"]."'
                limit 1
            ";
            $origin_representative_image_img = $this->rodb->simple_query($query);
            $new_profile_img_uuid = $this->uploadFiles($file, $origin_representative_image_img);
        }
        else {
            $profile_img_uuid = ",profile_img_uuid = null";
        }

        // welfare card upload
        $file = $files["welfare_img"];
        if($file["error"] == 0){
            // 기존 파일 있으면 업데이트
            $query = "
                select
                    welfare_card_uuid
                from
                    ".$this->table_name."
                where
                    uuid = '".$data["uuid"]."'
                limit 1
            ";
            $origin_welfare_card_uuid = $this->rodb->simple_query($query);
            $new_welfare_card_uuid = $this->uploadFiles($file, $origin_welfare_card_uuid);
            $welfare_card_uuid = ",welfare_card_uuid = '".$new_welfare_card_uuid."'";
        }
        else {
            $welfare_card_uuid = ",welfare_card_uuid = null";
        }*/

        $query = "
            update
                ".$this->table_name."
          set
                 product_category = '".$data["product_category"]."'
                ,product_name = '".$data["product_name"]."'
                 ,product_price = '".$data["product_price"]."'
                ,product_quantity= '".$data["product_quantity"]."'
                 ,product_start= '".$data["product_start"]."'
                ,product_end = '".$data["product_end"]."'
                ,product_surtax = '".$data["product_surtax"]."'
                ,delivery_cycle = '".$data["delivery_cycle"]."'
                ,product_detail = '".$data["product_detail"]."'
                ,product_ranking = '".$data["product_ranking"]."'    
                ,product_detail = '".$data["product_detail"]."'    
                ,update_id  = '관리자'      
                ,update_date = '".date("Y-m-d H:i:s")."'
            where
                idx = '".$data["idx"]."'
            limit 1
        ";
        $this->wrdb->update($query);
    return 1;
    } //}}}

    public function statusUpdate($data)
    {
        $query = "
			UPDATE
				".$this->table_name."
			SET
				status = ".$data["status"]."
			WHERE
				idx = ".$data["idx"]."
			LIMIT 1
			";

        $this->wrdb->update($query);

        return 1;
    }

}


