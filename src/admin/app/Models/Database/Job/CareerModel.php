<?php
namespace App\Models\Database\Job;
use App\Models\CommonModel;

class CareerModel extends CommonModel
{
    private $table_name = "db_job_career";

    public function getList()
    { //{{{
        $data = [];
        $query = "
            select
                *
            from
                ".$this->table_name."
            order by
                ordering asc
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data[] = $row;
        }

        return $data;

    } //}}}

    public function Update($data)
    { //{{{

        $query = "
            delete from
                ".$this->table_name."
        ";
        $idx = $this->wrdb->query($query);

        $insert_query = [];
        foreach($data as $key => $val){
            $idx = ($val["idx"])? $val["idx"] : "null";

            $query = "
                insert into
                    ".$this->table_name."
                set
                     idx = ".$idx."
                    ,title = '".$val["title"]."'
                    ,description = '".$val["description"]."'
                    ,ordering = ".($key + 1)."
                on duplicate key update
                     title = '".$val["title"]."'
                    ,description = '".$val["description"]."'
                    ,ordering = ".($key + 1)."
            ";
            $idx = $this->wrdb->insert($query);
        }

        return 1;

    } //}}}
}


