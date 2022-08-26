<?php
	
	namespace App\Models\Seller;
	use App\Models\CommonModel;
	
	class IMJOBModel extends CommonModel
	{
		public function Register($data,$files,$table_name = "seller_company_worker"){
			$allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG');
			$upload_face_ori = "upload_face";
			$upload_face = uniqid().".".pathinfo($files["upload_face"]["name"], PATHINFO_EXTENSION);
			$this->uploadFileNew($files,$upload_face,$allowed_ext,$upload_face_ori);
			$upload_card_ori = "upload_card";
			$upload_card = uniqid().".".pathinfo($files["upload_card"]["name"], PATHINFO_EXTENSION);
			$this->uploadFileNew($files,$upload_card,$allowed_ext,$upload_card_ori);
			
			$status = '1';
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
				,upload_face = '".$upload_face."'
				,upload_card = '".$upload_card."'
                ,register_date = '".date("Y-m-d H:i:s")."'
        ";
			$idx = $this->wrdb->insert($query);
			if($idx){
				return "1";
			}
			else {
				return null;
			}
		}
		
	}