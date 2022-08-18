<?php
namespace App\Models\Auth;
use App\Models\CommonModel;

class ForgotInfoModel extends CommonModel
{
	public function Register($data, $table_name = "search_id_pw"){
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
	
}