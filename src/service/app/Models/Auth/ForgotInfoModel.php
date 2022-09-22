<?php
namespace App\Models\Auth;
use App\Models\CommonModel;

class ForgotInfoModel extends CommonModel
{
	public function Register($data, $table_name = "search_id_pw"){

		$result = $this->getCompanyEmail($data);
		if($result != 1) {
			return null;
		}

		$status = '1';
		$query = "
            insert into
                ".$table_name."
            set
                status = '".$status."'
                ,user_id = '".$data["user_id"]."'
                ,user_phone = '".$data["user_phone"]."'
                ,company_name = '".$data["company_name"]."'
                ,manager_name = '".$data["manager_name"]."'
                ,search_type = '".$data["search_type"]."'
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
	
	public function getCompanyEmail($data)
	{
		$query = "
			SELECT
				*
			FROM
				seller_company
			WHERE
				phone = '".$data["user_phone"]."'
			";
		if($data["search_type"] == 2){
			$query = $query." AND email = '".$data["user_id"]."'";
		}
		$query = $query." LIMIT 1";

//			echo $query;
		$this->rodb->query($query);
		$result = $this->rodb->next_row();
		
		if($result != ""){
			return 1 ;
		}
		
		$query = "
			SELECT
				*
			FROM
				buyer_company
			WHERE
				phone = '".$data["user_phone"]."'
			";
		
		if($data["search_type"] == 2){
			$query = $query." AND email = '".$data["user_id"]."'";
		}
		$query = $query." LIMIT 1";

//			echo $query;
		$this->rodb->query($query);
		$result = $this->rodb->next_row();
		
		if($result != ""){
			return 1 ;
		}
		
		return 2;
	}
	
}