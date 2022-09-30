<?php

namespace App\Models\Delivery;
use App\Models\CommonModel;

class buyerDeliveryModel extends CommonModel
{

    public function getContractList($uuid)
    {
        $contractList = [];
        $query = "
            select
               *
            from
              contract_condition 
            where del_yn != 'Y'
             AND buyer_uuid ='" . $uuid . "'
             and contract_status = '5'
           order by 
               idx desc
        ";
        $this->rodb->query($query);
        while ($row = $this->rodb->next_row()) {
            $contractList[] = $row;
        }
        return $contractList;
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
           order by 
               idx desc
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $delivery[] = $row;
        }
        return $delivery;

    }

    public function getContents($data){
        $contents = [];
        $contract_no = $data['cn'];
        $query = "
            select
               *
            from
              contract_condition 
            where del_yn != 'Y'
             and contract_status = '5'
             and contract_no ='".$contract_no."'
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $contents = $row;
        }
        return $contents;

    }

    public function DeliveryStatus($data){
        $idx = $data['idx'];
        $delivery_status = '5';
        $buyer_uuid = $_SESSION['login_info']['uuid'];
        $buyer_company = $_SESSION['login_info']['company_name'];
        $query = "
            update
                delivery
            set
               delivery_status = '".$delivery_status."'
              ,delivery_arrival = '".date("Y-m-d")."'
              ,buyer_company = '".$buyer_company."'
              ,buyer_uuid = '".$buyer_uuid."'
            where idx = '".$idx."'
        ";
        $this->wrdb->update($query);
        return 1;
    }
}
