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

        if($files["seller_information"]["name"] != ""){
            $seller_information_ori = $files["seller_information"]["name"];
            $upload_seller_information_ori = "seller_information";
            $upload_seller_information_image = uniqid().".".pathinfo($files["seller_information"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_seller_information_image,$allowed_ext,$upload_seller_information_ori);
        }

        if($files["seller_documents"]["name"] != ""){
            $seller_documents_ori = $files["seller_documents"]["name"];
            $upload_seller_documents_ori = "seller_documents";
            $upload_seller_documents_image = uniqid().".".pathinfo($files["seller_documents"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_seller_documents_image,$allowed_ext,$upload_seller_documents_ori);
        }

        if($files["sales_file"]["name"] != ""){
            $sales_file_ori = $files["sales_file"]["name"];
            $upload_sales_file_ori = "sales_file";
            $upload_sales_file_image = uniqid().".".pathinfo($files["sales_file"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_sales_file_image,$allowed_ext,$upload_sales_file_ori);
        }

        if($files["workers_file"]["name"] != ""){
            $workers_file_ori = $files["workers_file"]["name"];
            $upload_workers_file_ori = "workers_file";
            $upload_workers_file_image = uniqid().".".pathinfo($files["workers_file"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_workers_file_image,$allowed_ext,$upload_workers_file_ori);
        }

       if($files["seller_business_license"]["name"] != ""){
           $seller_business_license_ori = $files["seller_business_license"]["name"];
           $upload_seller_business_license_ori = "seller_business_license";
           $upload_seller_business_license_image = uniqid().".".pathinfo($files["seller_business_license"]["name"], PATHINFO_EXTENSION);
           $this->uploadFileNew($files,$upload_seller_business_license_image,$allowed_ext,$upload_seller_business_license_ori);
       }

        if($files["seller_logo"]["name"] != ""){
            $seller_logo_ori = $files["seller_logo"]["name"];
            $upload_seller_logo_ori = "seller_logo";
            $upload_seller_logo_image = uniqid().".".pathinfo($files["seller_logo"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_seller_logo_image,$allowed_ext,$upload_seller_logo_ori);
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
                ,seller_documents_ori = '".$seller_documents_ori."'
                ,seller_information = '".$upload_seller_information_image."'
                ,seller_information_ori = '".$seller_information_ori."'
                 ,workers_file = '".$upload_workers_file_image."'
                ,workers_file_ori = '".$workers_file_ori."'
                ,sales_file = '".$upload_sales_file_image."'
                ,sales_file_ori = '".$sales_file_ori."'
                ,seller_logo = '".$upload_seller_logo_image."'
                ,seller_logo_ori = '".$seller_logo_ori."'
                 ,seller_business_license = '".$upload_seller_business_license_image."'
                ,seller_business_license_ori = '".$seller_business_license_ori."'
            where uuid = '".$uuid."'
        ";
		$this->wrdb->update($query);

        $seller_severely_disabled = $data['severely_disabled'];
        $seller_mild_disabled = $data['mild_disabled'];
        $seller_sales = $data['seller_sales'];

        $product_info =[];
        $product_query = "
			SELECT * FROM seller_product
			WHERE register_id='".$uuid."'
		";
        $this->rodb->query($product_query);
        while($row = $this->rodb->next_row()){
            $product_info= $row;

            $contribution = $product_info["product_price"]/$seller_sales;
            $workers = $seller_mild_disabled+($seller_severely_disabled*2);
            $reduction = $contribution * $workers;
            $reduction = round($reduction,4);
            $query = "
            update
               seller_product
            set
                reduction = $reduction
            where product_no = '".$product_info["product_no"]."'
        ";
            $this->wrdb->update($query);
        }

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
