<?php

namespace App\Models\Reduction;
use App\Models\CommonModel;

class ReductionModel extends CommonModel
{

    public function getdocumentList($result){
        $uuid = $_SESSION['login_info']['uuid'];
        $where = "";
        if($result['cn'] != ""){
            $contract_no = $result['cn'];
         $where = "  and contract_no = ".$contract_no." ";
        }

        $data= [];
        $query = "
            select
                *
            from
              contract_condition
            where contract_status = 5
            and del_yn !='Y' 
            and buyer_uuid = '".$uuid."'
            $where
           order by 
               idx desc
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data[] = $row;
        }
        return $data;
    }

    public function test($data){
        $seller_info = [];
        $seller_uuid = $data['su'];
        $query = "
            select
                *
            from
              seller_company
            where status = '5'
            and del_yn !='Y' 
            and uuid = '".$seller_uuid."'
           order by 
               idx desc
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
           $seller_info = $row;
       }
        return $seller_info;

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


}
