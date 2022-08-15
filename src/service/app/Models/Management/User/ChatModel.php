<?php
namespace App\Models\Management\User;
use App\Models\CommonModel;

class ChatModel extends CommonModel
{
    private $table_name = "chat_channel";

    public function getList($user_uuid)
    { //{{{
        $data = [];
        $query = "
            select
                 t1.*
                ,t2.company_name
            from
                ".$this->table_name." t1
                inner join
                company t2
            on
                t1.company_uuid = t2.uuid
            where
                user_uuid = '".$user_uuid."'
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
