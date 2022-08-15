<?php
namespace App\Models\Management\Company;
use App\Models\CommonModel;

class ChatModel extends CommonModel
{
    private $table_name = "chat_channel";

    public function getList($company_uuid)
    { //{{{
        $data = [];
        $query = "
            select
                 t1.*
                ,t2.name
            from
                ".$this->table_name." t1
                inner join
                user t2
            on
                t1.user_uuid = t2.uuid
            where
                company_uuid = '".$company_uuid."'
            order by
                update_date desc
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data[] = $row;
        }

        return $data;

    } //}}}
}
