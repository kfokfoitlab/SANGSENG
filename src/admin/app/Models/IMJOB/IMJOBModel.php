<?php
	
	namespace App\Models\IMJOB;
	use App\Models\CommonModel;
	
	class IMJOBModel extends CommonModel
	{
		private $table_name = "seller_company_worker";
		
		public function getListData($data)
		{ // {{{
			$items = array();
			
			$common_query = " 1 and (del_yn != 'Y' or del_yn is null)";
			
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
                 *
            from
                " . $this->table_name . "
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
		
		public function getWorkerInfo($table_name = "seller_company_worker"){
			
			$query = "
            select
                *
            from ".$table_name." where 1=1
			 and idx= ".$_GET["idx"]."
        ";
			$this->rodb->query($query);
			$data = $this->rodb->next_row();
			
			return $data;
		}
		
		public function Update($data,$files,$table_name = "seller_company_worker"){
			$allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG');
			$upload_face_ori = "upload_face";
			$upload_face = $data["upload_face_ori_name"];
			$upload_card = $data["upload_card_ori_name"];
			$upload_face_ori_new = $data["upload_face_ori"];
			$upload_card_ori_new = $data["upload_card_ori"];
			if($files["upload_face"]["name"] != "") {
				$upload_face_ori_new = $files["upload_face"]["name"];
				$upload_face = uniqid() . "." . pathinfo($files["upload_face"]["name"], PATHINFO_EXTENSION);
				$this->uploadFileNew($files, $upload_face, $allowed_ext, $upload_face_ori);
			}
			$upload_card_ori = "upload_card";
			if($files["upload_card"]["name"] != "") {
				$upload_card_ori_new = $files["upload_card"]["name"];
				$upload_card = uniqid() . "." . pathinfo($files["upload_card"]["name"], PATHINFO_EXTENSION);
				$this->uploadFileNew($files, $upload_card, $allowed_ext, $upload_card_ori);
			}
			
			$status = '1';
			$seller_uuid = $_SESSION["login_info"]["uuid"];
			$seller_data = $this->getSellerInfo($seller_uuid);
			
			$query = "
            update
                ".$table_name."
            set
                status = '".$status."'
				,worker_name = '".$data["worker_name"]."'
				,worker_term_start = '".$data["worker_term_start"]."'
				,worker_term_end = '".$data["worker_term_end"]."'
				,worker_birth = '".$data["worker_birth"]."'
				,working_status = '".$data["working_status"]."'
				,disability_degree = '".$data["disability_degree"]."'
				,upload_face = '".$upload_face."'
				,upload_card = '".$upload_card."'
				,upload_face_ori = '".$upload_face_ori_new."'
				,upload_card_ori = '".$upload_card_ori_new."'
                ,update_date = '".date("Y-m-d H:i:s")."'
                ,update_id = 'admin'
            where
                idx = ".$data["idx"]."
        ";
			//echo $query;
			$this->wrdb->update($query);
			return "1";
		}
		
		public function delete($table_name = "seller_company_worker"){
			
			$query = "
            update
                ".$table_name."
            set
                delete_date = '".date("Y-m-d H:i:s")."'
                ,delete_id = 'admin'
                ,del_yn = 'Y'
            where
                idx = ".$_GET["idx"]."
        ";
			//echo $query;
			$this->wrdb->update($query);
			return "1";
		}
		
		public function getSellerInfo($uuid)
		{ //{{{
			$data = [];
			$query = "
            select
                *
            from
                seller_company
            where
                uuid = '".$uuid."'
            limit 1
        ";
			$this->rodb->query($query);
			while($row = $this->rodb->next_row()) {
				$data = $row;
			}
			return $data;
		} //}}}
		
		public function uploadFileNEW($files,$fileName,$allowed_ext,$fileName_ori){
			$error = $files["$fileName_ori"]['error'];
			$name = $files["$fileName_ori"]['name'];
			$exploded_file = explode(".",$name);
			$ext = array_pop($exploded_file);
			$target_dir = UPLOADPATH."/service/public/uploads/";
			$target_dir_admin = UPLOADPATH."/admin/public/uploads/";
			$file_tmp_name = $files["$fileName_ori"]["tmp_name"];
			
			if(!is_dir($target_dir)){
				mkdir($target_dir,0777,true);
			}
			if(!is_dir($target_dir_admin)){
				mkdir($target_dir_admin,0777,true);
			}
			
			if( !in_array($ext, $allowed_ext) ) {
				echo "허용되지 않는 확장자입니다.";
				exit;
			}
			if( $error != UPLOAD_ERR_OK ) {
				switch( $error ) {
					case UPLOAD_ERR_INI_SIZE:
					case UPLOAD_ERR_FORM_SIZE:
						echo "파일이 너무 큽니다. ($error)";
						break;
					case UPLOAD_ERR_NO_FILE:
						echo "파일이 첨부되지 않았습니다. ($error)";
						break;
					default:
						echo "파일이 제대로 업로드되지 않았습니다. ($error)";
				}
				exit;
			}
			move_uploaded_file($file_tmp_name,$target_dir_admin.$fileName);
			copy($target_dir_admin.$fileName,$target_dir.$fileName);
		}

        public function regFormUpload($files){
            $allowed_ext = array('Xlsx','xlsx');
            if($files["register_file"]["name"] != ""){
                $register_file_ori = $files["register_file"]["name"];
                $upload_register_file_ori = "register_file";
                $upload_register_file_image = uniqid().".".pathinfo($files["register_file"]["name"], PATHINFO_EXTENSION);
                $this->uploadFileNew($files,$upload_register_file_image,$allowed_ext,$upload_register_file_ori);
            }
            $query = "
            insert into
                workers_excel
            set
                 register_date = '".date("Y-m-d H:i:s")."'
                ,register_file = '".$upload_register_file_image."'
                ,register_file_ori = '".$register_file_ori."'
                
        ";
            $idx = $this->wrdb->insert($query);
            if($idx){
                return 1;
            }
            else {
                return null;
            }
        }
	}