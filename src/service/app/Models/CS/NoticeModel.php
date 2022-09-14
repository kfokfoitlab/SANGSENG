<?php
	namespace App\Models\CS;
	use App\Models\CommonModel;
	
	class NoticeModel extends CommonModel
	{
		private $table_name = "notice_board";
		
		public function getListData()
		{
			$query = "
            select
                *
            from ".$this->table_name." where 1=1
			 and board_status=1
             and (del_yn != 'y' or del_yn is null)
        ";
			if($_GET["search_v"] != ""){
				$query = $query." and (title like '%".$_GET["search_v"]."%'
				 or content like '%".$_GET["search_v"]."%')";
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
			$data["data"] = [];
			$this->rodb->query($query);
			while($row = $this->rodb->next_row()){
				$data["data"][] = $row;
			}
			return $data;
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
		
		public function hitUpdate()
		{
			$query = "
			UPDATE
				".$this->table_name."
			SET
				hit = hit + 1
			WHERE
				idx = ".$_GET["idx"]."
			LIMIT 1
			";
			
			$this->wrdb->update($query);
			
			return 1;
		}
		
		public function getNoticeBoard(){
			$query = "
			SELECT
				*
			FROM
				".$this->table_name."
			WHERE
			    1=1
			    and idx = ".$_GET["idx"]."
			LIMIT 1
			";
			
			$this->rodb->query($query);
			$data = $this->rodb->next_row();
			
			return $data;
			
		}
		
	}