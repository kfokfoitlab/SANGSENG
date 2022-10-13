<?php
namespace App\Models\Member;
use App\Models\CommonModel;

class SellerModel extends CommonModel
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
        $allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG','JPG','PDF');
        if($files["seller_information"]["name"] != ""){
            $seller_information_ori = $files["seller_information"]["name"];
            $upload_seller_information_ori = "seller_information";
            $upload_seller_information_image = uniqid().".".pathinfo($files["seller_information"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_seller_information_image,$allowed_ext,$upload_seller_information_ori);
        }else{
            $seller_information_ori = $data["seller_information_ori"];
            $upload_seller_information_image = $data["seller_information"];
        }

        if($files["seller_documents"]["name"] != ""){
            $seller_documents_ori = $files["seller_documents"]["name"];
            $upload_seller_documents_ori = "seller_documents";
            $upload_seller_documents_image = uniqid().".".pathinfo($files["seller_documents"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_seller_documents_image,$allowed_ext,$upload_seller_documents_ori);
        }else{
            $seller_documents_ori = $data["seller_documents_ori"];
            $upload_seller_documents_image = $data["seller_documents"];
        }

        if($files["sales_file"]["name"] != ""){
            $sales_file_ori = $files["sales_file"]["name"];
            $upload_sales_file_ori = "sales_file";
            $upload_sales_file_image = uniqid().".".pathinfo($files["sales_file"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_sales_file_image,$allowed_ext,$upload_sales_file_ori);
        }else{
            $sales_file_ori = $data["sales_file_ori"];
            $upload_sales_file_image = $data["sales_file"];
        }

        if($files["workers_file"]["name"] != ""){
            $workers_file_ori = $files["workers_file"]["name"];
            $upload_workers_file_ori = "workers_file";
            $upload_workers_file_image = uniqid().".".pathinfo($files["workers_file"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_workers_file_image,$allowed_ext,$upload_workers_file_ori);
        }else{
            $workers_file_ori = $data["workers_file_ori"];
            $upload_workers_file_image = $data["workers_file"];
        }

        if($files["seller_business_license"]["name"] != ""){
            $seller_business_license_ori = $files["seller_business_license"]["name"];
            $upload_seller_business_license_ori = "seller_business_license";
            $upload_seller_business_license_image = uniqid().".".pathinfo($files["seller_business_license"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_seller_business_license_image,$allowed_ext,$upload_seller_business_license_ori);
        }else{
            $seller_business_license_ori = $data["seller_business_license_ori"];
            $upload_seller_business_license_image = $data["seller_business_license"];
        }

        if($files["seller_logo"]["name"] != ""){
            $seller_logo_ori = $files["seller_logo"]["name"];
            $upload_seller_logo_ori = "seller_logo";
            $upload_seller_logo_image = uniqid().".".pathinfo($files["seller_logo"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_seller_logo_image,$allowed_ext,$upload_seller_logo_ori);
        }else{
            $seller_logo_ori = $data["seller_logo_ori"];
            $upload_seller_logo_image = $data["seller_logo"];
        }
        $uuid = $data["uuid"];
        $query = "
            update
                    ".$this->table_name."
            set
                seller_name = '".$data["seller_name"]."'
                ,email = '".$data["email"]."'
                ,phone = '".$data["phone"]."'
                ,fax = '".$data["fax"]."'
                ,address = '".$data["address"]."'
                ,company_name = '".$data["company_name"]."'
                ,company_code = '".$data["company_code"]."'
                ,seller_sales = '".$data['seller_sales']."'
                ,severely_disabled = '".$data['severely_disabled']."'
                ,mild_disabled = '".$data['mild_disabled']."'
                ,update_date = '".date("Y-m-d H:i:s")."'
                ,update_id = '".$uuid."'
                ,seller_documents = '".$upload_seller_documents_image."'
                ,seller_documents_ori = '".$seller_documents_ori."'
                ,seller_information = '".$upload_seller_information_image."'
                ,seller_information_ori = '".$seller_information_ori."'
                ,workers_file = '".$upload_workers_file_image."'
                ,workers_file_ori = '".$workers_file_ori."'
                ,sales_file = '".$upload_sales_file_image."'
                ,sales_file_ori = '".$sales_file_ori."'
                ,seller_logo = '".$upload_seller_logo_image."'
                ,seller_logo_ori = '".$seller_logo_ori."'
                 ,seller_business_license = '".$upload_seller_business_license_image."'
                ,seller_business_license_ori = '".$seller_business_license_ori."'
                 where uuid = '".$uuid."'
        ";
        $this->wrdb->update($query);

        return 1;

    } //}}}

    public function statusUpdate($data)
    {
        $update_query = "";
        if($data['status'] == 9){
            $update_query = "status = ".$data["status"].", del_yn = 'Y' ";
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


