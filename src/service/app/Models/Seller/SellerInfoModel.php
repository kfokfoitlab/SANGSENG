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
        }else{
            $seller_information_ori = $data["seller_information_ori"];
            $upload_seller_information_image = $data["seller_information"];
        }

        if($files["seller_documents"]["name"] != ""){
            $seller_documents_ori = $files["seller_documents"]["name"];
            $upload_seller_documents_ori = "seller_documents";
            $upload_seller_documents_image = uniqid().".".pathinfo($files["seller_documents"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_seller_documents_image,$allowed_ext,$upload_seller_documents_ori);
        }else{
            $seller_documents_ori = $data["seller_documents_ori"];
            $upload_seller_documents_image = $data["seller_documents"];
        }

        if($files["sales_file"]["name"] != ""){
            $sales_file_ori = $files["sales_file"]["name"];
            $upload_sales_file_ori = "sales_file";
            $upload_sales_file_image = uniqid().".".pathinfo($files["sales_file"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_sales_file_image,$allowed_ext,$upload_sales_file_ori);
        }else{
            $sales_file_ori = $data["sales_file_ori"];
            $upload_sales_file_image = $data["sales_file"];
        }

        if($files["workers_file"]["name"] != ""){
            $workers_file_ori = $files["workers_file"]["name"];
            $upload_workers_file_ori = "workers_file";
            $upload_workers_file_image = uniqid().".".pathinfo($files["workers_file"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_workers_file_image,$allowed_ext,$upload_workers_file_ori);
        }else{
            $workers_file_ori = $data["workers_file_ori"];
            $upload_workers_file_image = $data["workers_file"];
        }

       if($files["seller_business_license"]["name"] != ""){
           $seller_business_license_ori = $files["seller_business_license"]["name"];
           $upload_seller_business_license_ori = "seller_business_license";
           $upload_seller_business_license_image = uniqid().".".pathinfo($files["seller_business_license"]["name"], PATHINFO_EXTENSION);
           $this->uploadFileNew($files,$upload_seller_business_license_image,$allowed_ext,$upload_seller_business_license_ori);
       }else{
           $seller_business_license_ori = $data["seller_business_license_ori"];
           $upload_seller_business_license_image = $data["seller_business_license"];
       }

        if($files["seller_logo"]["name"] != ""){
            $seller_logo_ori = $files["seller_logo"]["name"];
            $upload_seller_logo_ori = "seller_logo";
            $upload_seller_logo_image = uniqid().".".pathinfo($files["seller_logo"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_seller_logo_image,$allowed_ext,$upload_seller_logo_ori);
        }else{
            $seller_logo_ori = $data["seller_logo_ori"];
            $upload_seller_logo_image = $data["seller_logo"];
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

        $mild_disabled_count = "
                    select
                        count(*) as mild_disabled_cnt
                    from
                        seller_company_worker
                    where
                         uuid ='".$uuid."'
                         and disability_degree ='2'
                    limit 1
                ";
        $this->rodb->query($mild_disabled_count);
        $mild_disabled_cnt = $this->rodb->next_row();
        $mild_disabled = $mild_disabled_cnt["mild_disabled_cnt"];

        $severely_disabled_count = "
                    select
                        count(*) as severely_disabled_cnt
                    from
                        seller_company_worker
                    where
                         uuid ='".$uuid."'
                         and disability_degree ='1'
                    limit 1
                ";
        $this->rodb->query($severely_disabled_count);
        $severely_disabled_cnt = $this->rodb->next_row();
        $severely_disabled = $severely_disabled_cnt["severely_disabled_cnt"];

        $product_info =[];
        $product_query = "
			SELECT * FROM seller_product
			WHERE register_id='".$uuid."'
		";
        $this->rodb->query($product_query);
        while($row = $this->rodb->next_row()){
            $product_info= $row;

            $contribution = $data["product_price"]/$data["seller_sales"];
            $workers = $mild_disabled+($severely_disabled*2);
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
	
	public function pwdCheck($uuid){
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

    public function PwdUpdate($uuid){
        $password = $_POST['new_password'];
        $query = "
            update
                seller_company
            set
              password = SHA2('".$password."', 256)
            where
            uuid = '".$uuid."'
            limit 1
        ";
        $this->wrdb->update($query);
        return "1";
    }

}
