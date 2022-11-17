<?php

namespace App\Models\Delivery;
use App\Models\CommonModel;

class SellerDeliveryModel extends CommonModel
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
            $contractList[] = $row;
        }
        return $contractList;

    }

    public function invoice($data){
        $contract_no = $data['contract_no'];
        $seller_uuid = $data['seller_uuid'];
        $buyer_company = $data['buyer_company'];
        $buyer_uuid = $data['buyer_uuid'];
        $seller_company = $data['seller_company'];
        $product_no = $data['product_no'];
        $product_price = $data['product_price'];
        $product_name = $data['product_name'];
        $delivery_status = '1';
        $delivery_no = date("YmdHis");
        $dcount = $data['count'];

        $delivery_predicted =$data['delivery_predicted'];
        $contract_date = $data['register_date'];
        $query = "
          insert into
             delivery
          set
              delivery_no = '".$delivery_no."'
              ,contract_no = '".$contract_no."'
              ,delivery_status = '".$delivery_status."'              
              ,seller_company = '".$seller_company."' 
              ,seller_uuid = '".$seller_uuid."'         
              ,buyer_uuid = '".$buyer_uuid."'     
              ,buyer_company = '".$buyer_company."'     
              ,product_no = '".$product_no."'
              ,product_name = '".$product_name."'
              ,product_price = '".$product_price."'
              ,delivery_predicted = '".$delivery_predicted."'
              ,contract_date = '".$data['register_date']."'
              ,register_date = '".date("Y-m-d")."'
              ,register_id = '".$seller_uuid."'
              ,dcount = '".$dcount."'
              ,del_yn = 'N'
      ";
        $idx = $this->wrdb->insert($query);
        if($idx){
            $query = "
            update
                buyer_company
            set
                buyer_notification = '1'
            where uuid ='".$buyer_uuid."'
        ";
            $this->wrdb->update($query);
            return 1;
        }
        else {
            return null;
        }
    }

    public function invoiceUpdate($files,$data){
        $allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG');
        if($files["invoice_file_new"]["name"] != ""){
            $invoice_file_ori = $files["invoice_file_new"]["name"];
            $upload_invoice_file_ori = "invoice_file_new";
            $upload_invoice_file_image = uniqid().".".pathinfo($files["invoice_file_new"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_invoice_file_image,$allowed_ext,$upload_invoice_file_ori);
        }
        $idx = $data['idx'];
        $delivery_status = '3';
        $query = "
            update
                delivery
            set
                 delivery_status = '".$delivery_status."'
                ,invoice_file_ori = '".$invoice_file_ori."'
                ,invoice_file = '".$upload_invoice_file_image."'
            where idx = '".$idx."'
        ";
        $this->wrdb->update($query);
        return 1;
    }



    public function Register($files,$data){
        $allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG');
        if($files["invoice_file"]["name"] != ""){
            $invoice_file_ori = $files["invoice_file"]["name"];
            $upload_invoice_file_ori = "invoice_file";
            $upload_invoice_file_image = uniqid().".".pathinfo($files["invoice_file"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_invoice_file_image,$allowed_ext,$upload_invoice_file_ori);
        }
        $msg = "";
        $set = "";
        if($data['type'] == 'start') {
            $set = ",register_date = '".date("Y-m-d")."' , delivery_start = '".date("Y-m-d")."'" ;
            $msg = "배송이 등록되었습니다.";
        }else if($data['type'] == 'update'){
            $msg = "수정완료되었습니다";
        }
        $buyer_uuid = $data['buyer_uuid'];

        $idx = $data['idx'];
        $delivery_status = '3';
        $query = "
            update
                delivery
            set
                 delivery_status = '".$delivery_status."'
                ,invoice_file_ori = '".$invoice_file_ori."'
                ,invoice_file = '".$upload_invoice_file_image."'
                $set
            where idx = '".$idx."'
        ";
        $this->wrdb->update($query);
        $query = "
            update
                buyer_company
            set
                buyer_notification = '1'
            where uuid ='".$buyer_uuid."'
        ";
        $this->wrdb->update($query);
        return $msg;
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
        ";
        $delivery_cnt = [];
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $delivery_cnt[] = $row;
        }
        $delivery["count"] = count($delivery_cnt);
        $page_start = 0;
        if($_GET["p_n"] != ""){
            $page_start = ($_GET["p_n"] - 1)*10;
        }
        $query = $query." order by delivery_status asc";
        $query = $query." limit ".$page_start.", 10";

        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $delivery[] = $row;
        }
        return $delivery;

    }


    public function getContents($data){
        $contents = [];
        $contract_no = $data['cn'];
        $uuid = $_SESSION['login_info']['uuid'];
        $query = "
            select
               *
            from
              contract_condition 
            where del_yn != 'Y'
             AND seller_uuid ='".$uuid."'
             and contract_status = '5'
             and contract_no ='".$contract_no."'
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $contents = $row;
        }
        return $contents;

    }

}
