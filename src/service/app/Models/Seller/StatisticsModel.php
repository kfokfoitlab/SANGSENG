<?php

namespace App\Models\Seller;
use App\Models\CommonModel;

class StatisticsModel extends CommonModel
{

    public function getStatistics($uuid){

        $static_list =[];
        $query = "
            select
                 product_name,sum(product_price) as product_price ,register_date,count(*) as contract_count
            from
              contract_condition  
            where register_date >= '2022-01-01'
              and register_date <= '2022-12-31'
              and seller_uuid = '".$uuid."'
              and contract_status = '5'
              group by product_name
              order by product_price asc
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $static_list[] = $row;
        }
        return $static_list;
    }

    public function TotalPrice($uuid){
        $query = "
            select
                sum(product_price) as 'sum_price'
            from contract_condition 
            where register_date >= '2022-01-01'
            and register_date <= '2022-12-31'
            and seller_uuid = '".$uuid."'

        ";
        $total = $this->rodb->simple_query($query);
        return $total;
    }
}
