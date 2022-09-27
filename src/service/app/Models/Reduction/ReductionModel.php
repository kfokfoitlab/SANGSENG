<?php

namespace App\Models\Reduction;
use App\Models\CommonModel;

class ReductionModel extends CommonModel
{

    public function getdocumentList(){
        $uuid = $_SESSION['login_info']['uuid'];
        $data= [];
        $query = "
            select
                *
            from
              contract_condition
            where contract_status = '5'
            and del_yn !='Y' 
            and buyer_uuid = '".$uuid."'
           order by 
               idx desc
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data[] = $row;
        }
        return $data;
    }

    public function getWorkflowId($data){
        $workflow = [];
        $contract_no = $data['cn'];
        $query = "
            select
                *
            from
              contract_condition
            where contract_status = '5'
            and del_yn !='Y' 
            and contract_no = '".$contract_no."'
           order by 
               idx desc
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $workflow = $row;
       }
        return $workflow;
    }

    public function getBuyerdownloadList($data){
        $buyer_info = [];
        $contract_no = $data['cn'];

        $query = "
         select
            *
            from
            contract_condition
            where
            contract_no = ".$contract_no."
            ";
        $this->rodb->query($query);
        $contract = $this->rodb->next_row();

        $buyer_uuid = $contract["buyer_uuid"];
        $query = "
            select
                *
            from
              buyer_company
            where status = '5'
            and del_yn !='Y' 
            and uuid = '".$buyer_uuid."'
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $buyer_info= $row;
        }
        return $buyer_info;
    }


    public function getdownloadList($data){
        $seller_info = [];
        $contract_no = $data['cn'];

        $query = "
         select
            *
            from
            contract_condition
            where
            contract_no = ".$contract_no."
            ";
        $this->rodb->query($query);
        $contract = $this->rodb->next_row();

        $seller_uuid = $contract["seller_uuid"];
        $query = "
            select
                *
            from
              seller_company
            where status = '5'
            and del_yn !='Y' 
            and uuid = '".$seller_uuid."'
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $seller_info= $row;
        }
        return $seller_info;
    }

    public function provisionUpload($files,$data){
        $allowed_ext = array('jpg','jpeg','png','gif','pdf','PNG','JPG','PDF');
        if($files["provision_file"]["name"] != ""){
            $contract_provision_file_ori = $files["provision_file"]["name"];
            $upload_provision_file_ori = "provision_file";
            $upload_provision_file_image = uniqid().".".pathinfo($files["provision_file"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files,$upload_provision_file_image,$allowed_ext,$upload_provision_file_ori);
        }
        $contract_no =$data['contract_no'];
        $query = "
            update
                contract_condition
            set
                 provision_file = '".$upload_provision_file_image."'
                ,provision_file_ori = '".$contract_provision_file_ori."'
            where contract_no = '".$contract_no."'
        ";
        $this->wrdb->update($query);
        return 1;
    }
}
