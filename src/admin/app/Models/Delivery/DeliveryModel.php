<?php
	
	namespace App\Models\Delivery;
	use App\Models\CommonModel;
	
	class DeliveryModel extends CommonModel
	{
		private $table_name = "contract_condition";
		
		public function getListData($data)
		{ // {{{
			$items = array();
			
			$common_query = " 1 and (del_yn != 'Y' or del_yn is null) and contract_status=5";
			
			// total records -------------------------------- {{{
			$query = "
            select
                count(*)
            from
                " . $this->table_name . "
            where
                " . $common_query . "
        ";
			$records_total = $this->rodb->simple_query($query);
			// ---------------------------------------------- }}}
			
			// Search --------------------------------------- {{{
			$search_query = null;
			if (@$data["search"]["value"]) {
				$search_query = " ";
			}
			// ---------------------------------------------- }}}
			
			// filtering {{{
			$filtering = array();
			foreach ($data["columns"] as $key => $val) {
				if (!@$val["search"]["value"]) {
					continue;
				} else if ($val["data"] == "register_date") {
					$t = explode("~", $val["search"]["value"]);
					$filtering[] = "register_date between '" . $t[0] . " 00:00:00' and '" . $t[1] . " 23:59:59'";
				} else {
					$filtering[] = " (
                    lower(replace(" . $val["data"] . ", ' ', '')) like '%" . strtolower($val["search"]["value"]) . "%' or
                    lower(" . $val["data"] . ") like '%" . strtolower($val["search"]["value"]) . "%'
                )";
				}
			}
			$filtering_query = (count($filtering) > 0) ? " and " . @join(" and ", $filtering) : "";
			// ---------------------------------------------- }}}
			
			// filtered count ------------------------------- {{{
			$query = "
            select count(*)
            from
                " . $this->table_name . "
            where
                " . $common_query . "
                " . $search_query . "
                " . $filtering_query . "
        ";
			$filtered_total = $this->rodb->simple_query($query);
			// ---------------------------------------------- }}}
			
			// Pagination ----------------------------------- {{{
			$limit = $data["start"] . ", " . $data["length"];
			// ---------------------------------------------- }}}
			
			// Ordering ------------------------------------- {{{
			$order_arr = [];
			foreach ($data["order"] as $val) {
				$order_field_idx = $val["column"];
				$order_field = $data["columns"][$order_field_idx]["data"];
				$order_field = ($order_field == "num") ? "idx" : $order_field;
				$order_direction = $val["dir"];
				$order_arr[] = $order_field . " " . $order_direction;
				
			}
			$order_query = @join(",", $order_arr);
			// ---------------------------------------------- }}}
			
			// query
			$query = "
            select
                 *,(select count(*) from delivery as b where a.contract_no = b.contract_no) as delivery_total_cnt,
       				(select count(*) from delivery as b where a.contract_no = b.contract_no and b.delivery_status = 1) as delivery_wait_cnt,
      				(select count(*) from delivery as b where a.contract_no = b.contract_no and b.delivery_status = 3) as delivery_ready_cnt,
    				(select count(*) from delivery as b where a.contract_no = b.contract_no and b.delivery_status = 5) as delivery_end_cnt
            from
                " . $this->table_name . " as a
            where
                " . $common_query . "
                " . $search_query . "
                " . $filtering_query . "
            order by
                " . $order_query . "
            limit
                " . $limit . "
        ";
			$this->rodb->query($query);
			$num = $filtered_total - $data["start"];
			while ($row = $this->rodb->next_row()) {
				$row["num"] = $num--;
				
				unset($row["coordinate"]);
				
				$items[] = $row;
			}
			
			return array(
				"records_total" => $records_total
			, "data" => $items
			, "filtered_total" => $filtered_total
			);
		}
		
	}