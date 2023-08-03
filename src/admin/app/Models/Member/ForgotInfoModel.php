<?php
	namespace App\Models\Member;
	use App\Models\CommonModel;
	
	class ForgotInfoModel extends CommonModel
	{
		private $table_name = "search_id_pw";
		
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
		public function getCompanyEmail($data)
		{
			//echo $user_phone;
			$query = "
			SELECT
				*
			FROM
				search_id_pw
			WHERE
				register_id = '".$data["rgd"]."'
				and idx = '".$data['idx']."'
			LIMIT 1
			";
			
//			echo $query;
			$this->rodb->query($query);
			$data = $this->rodb->next_row();
			return $data;
		}
		
		public function resetPw($data)
		{
			//echo $user_phone;
			$table = "";
			if($data["type"] == "seller" ) {
				$table = "seller_company";
			}elseif($data["type"] == "buyer" ){
				$table = "buyer_company";
			}
			
			$resetPw = str_replace('-','',$data["phone"].'a!');
			$query = "
			UPDATE
			".$table."
			SET
				password = SHA2('".$resetPw."', 256)
			WHERE
				phone = '".$data["phone"]."'
				and email = '".$data["email"]."'
			";

//			echo $query;
			$this->wrdb->update($query);
			return $resetPw;
		}
	}


