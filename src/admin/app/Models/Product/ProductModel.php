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
    public function getCategoryList(){
        $category_type =[];
        $query = "
            select
                distinct category_type1 as category_type1
            from
              product_category
            where del_yn ='N'
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $category_type[]= $row;
        }
        return $category_type;
    }

    public function Category($idx){
        $category_type =[];
        $query = "
            select
                distinct category_type1 as category_type1
            from
              product_category  
            where del_yn ='N'
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $category_type['category_type1'][]= $row;
        }
        $product_query = "
            select
                 product_category
            from
              seller_product 
            where idx = '".$idx."'
        ";
        $this->rodb->query($product_query);
        $product_info = $this->rodb->next_row();
        $query = "
            select
                distinct category_type2 as category_type2
            from
              product_category  
            where category_type1 = '".$product_info['product_category']."'
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $category_type['category_type2'][] = $row;
        }
        return $category_type;
    }
    public function CategorySearch($data){
        $category_type = [];
        $query = "
            select
              DISTINCT category_type2
            from
              product_category        
            where
                category_type1 = '".$data["category_type1"]."'
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $category_type[] = $row;
        }
        return $category_type;
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

    public function Update($files, $data)
    { //{{{
        $allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG');

        if($files["representative_image"]["name"] != ""){
            $representative_ori = $files["representative_image"]["name"];
            $upload_representative_ori = "representative_image";
            $upload_representative = uniqid().".".pathinfo($files["representative_image"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_representative,$allowed_ext,$upload_representative_ori);
        }else{
            $representative_ori = $data["representative_image"];
            $upload_representative = $data["representative_image"];
        }
        if($files["product_image1"]["name"] != ""){
            $product_image1_ori = $files["product_image1"]["name"];
            $upload_image1_ori = "product_image1";
            $upload_image1 = uniqid().".".pathinfo($files["product_image1"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_image1,$allowed_ext,$upload_image1_ori);
        }else{
            $product_image1_ori = $data["product_image1"];
            $upload_image1 = $data["product_image1"];
        }
        if($files["product_image2"]["name"] != ""){
            $product_image2_ori = $files["product_image2"]["name"];
            $upload_image2_ori = "product_image2";
            $upload_image2 = uniqid().".".pathinfo($files["product_image2"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_image2,$allowed_ext,$upload_image2_ori);
        }else{
            $product_image2_ori = $data["product_image2"];
            $upload_image2 = $data["product_image2"];
        }
        if($files["detail_img"]["name"] != ""){
            $detail_img_ori = $files["detail_img"]["name"];
            $upload_detail_image_ori = "detail_img";
            $upload_detail_image = uniqid().".".pathinfo($files["detail_img"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_detail_image,$allowed_ext,$upload_detail_image_ori);
        }else{
            $detail_img_ori = $data["detail_img"];
            $upload_detail_image = $data["detail_img"];
        }


        $query = "
            update
                ".$this->table_name."
          set
                 product_category = '".$data["product_category"]."'
                ,product_category2 = '".$data["product_category2"]."'
                ,product_name = '".$data["product_name"]."'
                 ,product_price = '".$data["product_price"]."'
                ,product_quantity= '".$data["product_quantity"]."'
                 ,product_start= '".$data["product_start"]."'
                ,product_end = '".$data["product_end"]."'
                ,product_surtax = '".$data["product_surtax"]."'
                ,delivery_cycle = '".$data["delivery_cycle"]."'
                ,product_detail = '".$data["product_detail"]."'    
                ,representative_image = '".$upload_representative."'
                ,product_image1 = '".$upload_image1."'
                ,product_image2 = '".$upload_image2."'
                ,detail_img = '".$upload_detail_image."'
                ,representative_image_ori = '".$representative_ori."'
                ,product_image1_ori = '".$product_image1_ori."'
                ,product_image2_ori = '".$product_image2_ori."'
                ,detail_img_ori = '".$detail_img_ori."'
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
        $update_query = "";
        if($data['status'] == 9){
            $update_query = "status = ".$data["status"].", del_yn = 'Y' ";
        }elseif($data['status'] == 7){
	        $update_query = "status = ".$data["status"].", del_yn ='N', status_comment = '".$_GET["status_comment"]."'";
        }else{
            $update_query = "status = ".$data["status"].", del_yn ='N' ";
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


