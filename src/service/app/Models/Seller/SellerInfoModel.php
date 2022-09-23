<?php

namespace App\Models\Seller;
use App\Models\CommonModel;

class SellerInfoModel extends CommonModel
{

    public function getMyInfo($uuid){
        $data = [];
        $query = "

        select
            *
        from 
            seller_company 
        where
            uuid = '".$uuid."'
           
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data= $row;
        }
        return $data;
    }
	
	public function infoUpdate($files,$data, $table_name = "seller_company"){
		$allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG','JPG','PDF');
		$upload_seller_documents_image = $data["seller_documents_ori_name"];
		if($files["seller_documents"]["name"] != "") {
			$upload_seller_documents_ori = "seller_documents";
			$upload_seller_documents_image = uniqid() . "." . pathinfo($files["seller_documents"]["name"], PATHINFO_EXTENSION);
			$this->uploadFileNew($files, $upload_seller_documents_image, $allowed_ext, $upload_seller_documents_ori);
		}
		$upload_seller_information_image = $data["seller_information_ori_name"];
		if($files["seller_information"]["name"] != ""){
			$upload_seller_information_ori = "seller_information";
			$upload_seller_information_image = uniqid().".".pathinfo($files["seller_information"]["name"], PATHINFO_EXTENSION);
			$this->uploadFileNew($files,$upload_seller_information_image,$allowed_ext,$upload_seller_information_ori);
		}
		$uuid = $_SESSION["login_info"]["uuid"];
		$query = "
            update
                ".$table_name."
            set
                seller_name = '".$data["seller_name"]."'
                ,email = '".$data["email"]."'
                ,phone = '".$data["phone"]."'
                ,fax = '".$data["fax"]."'
                ,address = '".$data["address"]."'
                ,company_name = '".$data["company_name"]."'
                ,company_code = '".$data["company_code"]."'
                ,classification = '".$data["classification"]."'
                ,seller_sales = '".$data['seller_sales']."'
                ,severely_disabled = '".$data['severely_disabled']."'
                ,mild_disabled = '".$data['mild_disabled']."'
                ,update_date = '".date("Y-m-d H:i:s")."'
                ,update_id = '".$uuid."'
                ,seller_documents = '".$upload_seller_documents_image."'
                ,seller_information = '".$upload_seller_information_image."'
            where uuid = '".$uuid."'
        ";
		
		$this->wrdb->update($query);
		
		return 1;
		
	}
	
	public function pwdCheck(){
		$uuid = $_SESSION["login_info"]["uuid"];
		$pwd = $_POST["password"];
		$query = "
			SELECT * FROM seller_company
			WHERE 1=1
			AND uuid='".$uuid."'
			AND password=SHA2('".$pwd."', 256)
		";
		$this->rodb->query($query);
		while($this->rodb->next_row()) {
			return 1;
		}
		return null;
	}

}
