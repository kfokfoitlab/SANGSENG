<?php
	namespace App\Models\CS;
	use App\Models\CommonModel;
	
	class QuestionsModel extends CommonModel
	{
		private $table_name = "questions_board";
		
		public function getListData()
		{
			$query = "
            select
                *
            from ".$this->table_name." where 1=1
             and user_uuid = '".$_SESSION["login_info"]["uuid"]."'
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
		
		public function Register($data){
			
			$query = "
            insert into
                ".$this->table_name."
            set
                board_status = 1
                ,board_type= ".$data["board_type"]."
                ,user_uuid = '".$_SESSION["login_info"]["uuid"]."'
                ,user_company_name = '".$data["user_company_name"]."'
                ,user_email = '".$data["user_email"]."'
                ,user_phone = '".$data["user_phone"]."'
                ,manager_name = '".$data["manager_name"]."'
                ,title = '".$data["title"]."'
                ,content = '".$data["content"]."'
                ,agree_privacy = '".$data["agree_privacy"]."'
                ,register_date = '".date("Y-m-d H:i:s")."'
                ,register_id = '".$_SESSION["login_info"]["uuid"]."'
                ,update_date = '".date("Y-m-d H:i:s")."'
                ,update_id = '".$_SESSION["login_info"]["uuid"]."'
                ,del_yn = 'n'
        ";
			echo $query;
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
		
		public function getQuestionsBoard(){
			$query = "
			SELECT
				a.*,b.reply_content as reply_content,b.register_date as reply_register_date
			FROM
				".$this->table_name."
				as a left join questions_board_reply as b on a.idx = b.questions_board_idx
			WHERE
			    1=1
			    and a.idx = ".$_GET["idx"]."
			LIMIT 1
			";
			
			$this->rodb->query($query);
			$data = $this->rodb->next_row();
			
			return $data;
			
		}
		
		public function delete()
		{
			$query = "
			UPDATE
				".$this->table_name."
			SET
				del_yn='y'
			WHERE
				idx = ".$_POST["idx"]."
				AND user_uuid = '".$_SESSION["login_info"]["uuid"]."'
			LIMIT 1
			";

			$this->wrdb->update($query);
			
			return 1;
		}
		
	}