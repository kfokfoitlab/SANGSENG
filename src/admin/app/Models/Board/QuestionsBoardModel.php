<?php
	namespace App\Models\Board;
	use App\Models\CommonModel;
	
	class QuestionsBoardModel extends CommonModel
	{
		private $table_name = "questions_board";
		
		public function getListData($data)
		{ // {{{
			$items = array();
			
			$common_query = " 1 and del_yn != 'Y'";
			
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
		
		public function Register($data,$files, $table_name = "notice_board"){
			$allowed_ext = array();
			$upload_face_ori = "upload_file";
			$upload_file = uniqid().".".pathinfo($files["upload_file"]["name"], PATHINFO_EXTENSION);
			$this->uploadFileNew($files,$upload_file,$allowed_ext,$upload_face_ori);
			
			$query = "
            insert into
                ".$table_name."
            set
                board_status = '".$data["board_status"]."'
                ,user_id = 'admin'
                ,title = '".$data["title"]."'
                ,content = '".$data["content"]."'
                ,upload_file = '".$upload_file."'
                ,register_date = '".date("Y-m-d H:i:s")."'
                ,register_id = 'admin'
                ,update_date = '".date("Y-m-d H:i:s")."'
                ,update_id = 'admin'
                ,del_yn = 'n'
        ";
			//echo $query;
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
		
		public function getQuestionsBoard($data){
			$query = "
			SELECT
				a.*,b.reply_content as reply_content,b.idx as reply_idx
			FROM
				".$this->table_name."
				as a left join questions_board_reply as b on a.idx = b.questions_board_idx
			WHERE
				a.idx = ".$data["idx"]."
			LIMIT 1
			";
			//echo $query;
			$this->rodb->query($query);
			$data = $this->rodb->next_row();
			
			return $data;
			
		}
		
		public function replySubmit($data, $table_name = "questions_board_reply"){

			$query = "
            insert into
                ".$table_name."
            set
                questions_board_idx = '".$data["idx"]."'
				,user_uuid = '".$data["user_uuid"]."'
				,user_company_name = '".$data["user_company_name"]."'
				,user_email = '".$data["user_email"]."'
				,user_phone = '".$data["user_phone"]."'
				,manager_name = '".$data["manager_name"]."'
				,reply_content = '".$data["reply_content"]."'
				,register_date = '".date("Y-m-d H:i:s")."'
                ,register_id = 'admin'
                ,update_date = '".date("Y-m-d H:i:s")."'
                ,update_id = 'admin'
                ,del_yn = 'n'
        ";
			$idx = $this->wrdb->insert($query);
			
			if($idx){
				$query = "
					UPDATE
						".$this->table_name."
					SET
						board_status = 2
					WHERE   1=1
					    AND	idx = ".$data["idx"]."
						AND user_uuid = '".$data["user_uuid"]."'
					LIMIT 1
					";
				$this->wrdb->update($query);
				return "1";
			}
			else {
				return null;
			}
		}
		
		public function replyUpdateSubmit($data, $table_name = "questions_board_reply")
		{
			$query = "
			UPDATE
				".$table_name."
			SET
				reply_content = '".$data["reply_content"]."'
                ,update_date = '".date("Y-m-d H:i:s")."'
                ,update_id = 'admin'
			WHERE 1=1
				AND idx = ".$data["reply_idx"]."
				AND user_uuid = '".$data["user_uuid"]."'
			LIMIT 1
			";
			
			//echo $query;
			$this->wrdb->update($query);
			
			return 1;
		}
		
		public function delete()
		{
			$query = "
			UPDATE
				".$this->table_name."
			SET
				del_yn='Y'
			WHERE
				idx = ".$_GET["idx"]."
			LIMIT 1
			";
			
			$this->wrdb->update($query);
			
			return 1;
		}
		
	}