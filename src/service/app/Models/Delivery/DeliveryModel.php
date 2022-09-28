<?php

namespace App\Models\Delivery;
use App\Models\CommonModel;

class DeliveryModel extends CommonModel
{

    public function getContractList($uuid){
        $contractList = [];
        $query = "
            select
               *
            from
              contract_condition 
            where del_yn != 'Y'
             AND seller_uuid ='".$uuid."'
             and contract_status = '5'
           order by 
               idx desc
           
           
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $contractList = $row;
        }
        return $contractList;

    }

    public function Register($files,$data){
        $allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG');
        if($files["invoice_file"]["name"] != ""){
            $invoice_file_ori = $files["invoice_file"]["name"];
            $upload_invoice_file_ori = "invoice_file";
            $upload_invoice_file_image = uniqid().".".pathinfo($files["invoice_file"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_invoice_file_image,$allowed_ext,$upload_invoice_file_ori);
        }
        $contract_no = $data['contract_no'];
        $seller_uuid = $data['seller_uuid'];
        $seller_company = $data['seller_company'];
        $product_no = $data['product_no'];
        $product_price = $data['product_price'];
        $product_name = $data['product_name'];
        $delivery_status = '2';
        $delivery_no = date("YmdHis");
        $dcount = $data['count'];
        $delivery_predicted =$data['delivery_predicted'];
        $query = "
          insert into
             delivery
          set
              delivery_no = '".$delivery_no."'
              ,contract_no = '".$contract_no."'
              ,delivery_status = '".$delivery_status."'              
              ,seller_company = '".$seller_company."' 
              ,seller_uuid = '".$seller_uuid."'         
              ,product_no = '".$product_no."'
              ,product_name = '".$product_name."'
              ,product_price = '".$product_price."'
              ,delivery_start = '".date("Y-m-d")."'
              ,delivery_predicted = '".$delivery_predicted."'
              ,invoice_file = '".$upload_invoice_file_image."'
              ,invoice_file_ori = '".$invoice_file_ori."'
              ,register_date = '".date("Y-m-d")."'
              ,register_id = '".$seller_uuid."'
              ,dcount = '".$dcount."'
              ,del_yn = 'N'
      ";
        $idx = $this->wrdb->insert($query);
        if($idx){
            return 1;
        }
        else {
            return null;
       }
    }

    public function getDeliveryList($data){
        $delivery = [];
        $contract_no = $data['cn'];
        $uuid = $_SESSION['login_info']['uuid'];
        $query = "
            select
                *
            from
              delivery
            where del_yn !='Y' 
            and contract_no = '".$contract_no."'
            and seller_uuid = '".$uuid."'
           order by 
               idx desc
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $delivery[] = $row;
        }
        return $delivery;

    }


}
