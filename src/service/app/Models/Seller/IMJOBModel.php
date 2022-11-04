<?php
	
	namespace App\Models\Seller;
	use App\Models\CommonModel;
	
	class IMJOBModel extends CommonModel
	{
		public function Register($data,$files,$table_name = "seller_company_worker"){
			$allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG');

            if($files["upload_face"]["name"] != ""){
                $upload_face_ori = "upload_face";
                $upload_face = uniqid().".".pathinfo($files["upload_face"]["name"], PATHINFO_EXTENSION);
                $this->uploadFileNew($files,$upload_face,$allowed_ext,$upload_face_ori);
            }
            if($files["upload_card"]["name"] != ""){
                $upload_card_ori = "upload_card";
                $upload_card = uniqid().".".pathinfo($files["upload_card"]["name"], PATHINFO_EXTENSION);
                $this->uploadFileNew($files,$upload_card,$allowed_ext,$upload_card_ori);
            }
			$status = '5';
			$seller_uuid = $_SESSION["login_info"]["uuid"];
			$seller_data = $this->getSellerInfo($seller_uuid);
			
			$query = "
            insert into
                ".$table_name."
            set
                status = '".$status."'
                ,company_name = '".$seller_data["company_name"]."'
				,company_code = '".$seller_data["company_code"]."'
				,worker_name = '".$data["worker_name"]."'
				,worker_term_start = '".$data["worker_term_start"]."'
				,worker_term_end = '".$data["worker_term_end"]."'
				,worker_birth = '".$data["worker_birth"]."'
				,working_status = '".$data["working_status"]."'
				,disability_degree = '".$data["disability_degree"]."'
				,del_yn = 'N'
				,upload_face = '".$upload_face."'
				,upload_card = '".$upload_card."'
				,upload_face_ori = '".$files["upload_face"]["name"]."'
				,upload_card_ori = '".$files["upload_card"]["name"]."'
                ,register_date = '".date("Y-m-d H:i:s")."'
                ,register_id = '".$_SESSION["login_info"]["uuid"]."'
        ";
			$idx = $this->wrdb->insert($query);
			if($idx){
				return "1";
			}
			else {
				return null;
			}
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
			
			$status = '5';
			$seller_uuid = $_SESSION["login_info"]["uuid"];
			$seller_data = $this->getSellerInfo($seller_uuid);
			
			$query = "
            update
                ".$table_name."
            set
                status = '".$status."'
                ,company_name = '".$seller_data["company_name"]."'
				,company_code = '".$seller_data["company_code"]."'
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
                ,update_id = '".$_SESSION["login_info"]["uuid"]."'
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
                ,delete_id = '".$_SESSION["login_info"]["uuid"]."'
                ,del_yn = 'Y'
            where
                idx = ".$_GET["idx"]."
        ";
			//echo $query;
			$this->wrdb->update($query);
			return "1";
		}
		
		public function getWorkerList($table_name = "seller_company_worker"){
			
			$seller_uuid = $_SESSION["login_info"]["uuid"];
			$seller_data = $this->getSellerInfo($seller_uuid);
			
			if($_GET["w_s1"] != ""){
				$where[] = "working_status=1";
			}if($_GET["w_s2"] != ""){
				$where[] = "working_status=2";
			}if($_GET["w_s3"] != ""){
				$where[] = "working_status=3";
			}
			$where_query = "";
			if($_GET["w_s1"] != "" || $_GET["w_s2"] != "" || $_GET["w_s3"] != "") {
				$where_query = " and (" . @join(" or ", $where) . ")";
			}
			$query = "
            select
                *
            from ".$table_name." where 1=1
			 and register_id= '".$seller_uuid."'
             and (del_yn != 'Y' or del_yn is null)".$where_query."

        ";
			if($_GET["search_v"] != ""){
				$query = $query." and (worker_name like '%".$_GET["search_v"]."%'
				 or worker_birth like '%".$_GET["search_v"]."%')";
			}
			if($_GET["search_status"] != ""){
				$query = $query." and status = ".$_GET["search_status"];
			}
			$data_cnt = [];
			$this->rodb->query($query);
			while($row = $this->rodb->next_row()){
				$data_cnt[] = $row;
			}
			$data["count"] = count($data_cnt);
			$page_start = 0;
			if($_GET["p_n"] != ""){
				$page_start = ($_GET["p_n"] - 1)*10;
			}
			$query = $query." order by idx desc";
			$query = $query." limit ".$page_start.", 10";
			//echo $query;
			//echo $query;
			$data["data"] = [];
			$this->rodb->query($query);
			while($row = $this->rodb->next_row()){
				$data["data"][] = $row;
			}
			return $data;
		}
		
		public function getWorkerCount($table_name = "seller_company_worker"){
			
			$seller_uuid = $_SESSION["login_info"]["uuid"];
			$seller_data = $this->getSellerInfo($seller_uuid);
			
			$query = "
            select
                count(*) as worker_cnt,count(case when disability_degree=1 then 1 end) as degree_1_cnt,
                count(case when disability_degree=2 then 1 end) as degree_2_cnt
            from ".$table_name." where 1=1
			 and register_id= '".$seller_uuid."'
			 and (del_yn != 'y' or del_yn is null)
			 and (status = 5)
        ";
			$this->rodb->query($query);
			while($row = $this->rodb->next_row()){
				$data_cnt = $row;
			}
			return $data_cnt;
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
		
	}