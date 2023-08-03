<?php
namespace App\Models_m\Auth;
use App\Models_m\CommonModel;

class ForgotInfoModel extends CommonModel
{
	public function Register($data, $table_name = "search_id_pw"){

		$result = $this->getCompanyEmail($data);
        if($result ==3){
            return null;
        }
        if($result == 1) {
            $seller_query = "
			SELECT
				*
			FROM
				seller_company
			WHERE
				phone = '".$data["user_phone"]."'
				and company_name ='".$data["company_name"]."'
				and seller_name = '".$data['manager_name']."'
			";
            if($data["search_type"] == 2){
                $seller_query = $seller_query." AND email = '".$data["user_id"]."'";
            }
            $seller_query = $seller_query." LIMIT 1";
            $this->rodb->query($seller_query);
            $seller_info= $this->rodb->next_row();
            $status = '1';
            $query = "
            insert into
                ".$table_name."
            set
                status = '".$status."'
                ,user_id = '".$seller_info["email"]."'
                ,user_phone = '".$data["user_phone"]."'
                ,company_name = '".$data["company_name"]."'
                ,manager_name = '".$data["manager_name"]."'
                ,search_type = '".$data["search_type"]."'
                ,register_date = '".date("Y-m-d H:i:s")."'
                ,register_id = '".$seller_info["uuid"]."'
                ,company_type = 'seller'
        ";
            $idx = $this->wrdb->insert($query);
            if($idx){
                return "1";
            }
            else {
                return null;
            }
        }

		if($result == 2) {
            $buyer_query = "
			SELECT
				*
			FROM
				buyer_company
			WHERE
				phone = '".$data["user_phone"]."'
				and company_name ='".$data["company_name"]."'
				and buyer_name = '".$data['manager_name']."'
			";
            if($data["search_type"] == 2){
                $buyer_query = $buyer_query." AND email = '".$data["user_id"]."'";
            }
            $buyer_query = $buyer_query." LIMIT 1";
            $this->rodb->query($buyer_query);
            $buyer_info= $this->rodb->next_row();
            $status = '1';
            $query = "
            insert into
                ".$table_name."
            set
                status = '".$status."'
                ,user_id = '".$buyer_info["email"]."'
                ,user_phone = '".$data["user_phone"]."'
                ,company_name = '".$data["company_name"]."'
                ,manager_name = '".$data["manager_name"]."'
                ,search_type = '".$data["search_type"]."'
                ,register_date = '".date("Y-m-d H:i:s")."'
                ,register_id = '".$buyer_info["uuid"]."'
                ,company_type = 'buyer'
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
	
	public function getCompanyEmail($data)
	{
		$query = "
			SELECT
				*
			FROM
				seller_company
			WHERE
				phone = '".$data["user_phone"]."'
				and company_name ='".$data["company_name"]."'
				and seller_name = '".$data['manager_name']."'
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
				and company_name ='".$data["company_name"]."'
				and buyer_name = '".$data['manager_name']."'
			";
		
		if($data["search_type"] == 2){
			$query = $query." AND email = '".$data["user_id"]."'";
		}
		$query = $query." LIMIT 1";

//			echo $query;
		$this->rodb->query($query);
		$result = $this->rodb->next_row();
		
		if($result != ""){
			return 2 ;
		}
		
		return 3;
	}
	
}