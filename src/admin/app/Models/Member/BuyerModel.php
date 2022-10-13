<?php
namespace App\Models\Member;
use App\Models\CommonModel;

class BuyerModel extends CommonModel
{
    private $table_name = "buyer_company";

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
        if($files["buyer_documents"]["name"] != ""){
            $buyer_documents_ori = $files["buyer_documents"]["name"];
            $upload_buyer_documents_ori = "buyer_documents";
            $upload_buyer_documents_image = uniqid().".".pathinfo($files["buyer_documents"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_buyer_documents_image,$allowed_ext,$upload_buyer_documents_ori);
        }else{
            $buyer_documents_ori = $data["buyer_documents_ori"];
            $upload_buyer_documents_image = $data["buyer_documents"];
        }
        $query = "
            update
                ".$this->table_name."
            set
                 email = '".$data["email"]."'
                ,buyer_name = '".$data["buyer_name"]."'
                ,phone = '".$data["phone"]."'
                ,address = '".$data["address"]."'
                ,company_name = '".$data["company_name"]."'
                ,company_code = '".$data["company_code"]."'
                ,classification = '".$data["classification"]."'
                ,fax = '".$data["fax"]."'
                ,severely_disabled = '".$data["severely_disabled"]."'
                ,mild_disabled = '".$data["mild_disabled"]."'
                ,update_date = '".date("Y-m-d H:i:s")."'
                ,buyer_documents = '".$upload_buyer_documents_image."'
                ,buyer_documents_ori = '".$buyer_documents_ori."'
            where
                uuid = '".$data["uuid"]."'
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


