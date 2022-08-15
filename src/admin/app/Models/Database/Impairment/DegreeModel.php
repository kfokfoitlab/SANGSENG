<?php
namespace App\Models\Database\Impairment;
use App\Models\CommonModel;

class DegreeModel extends CommonModel
{
    private $table_name = "db_impairment_degree";

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
            $data[$row["code"]]["title"] = $row["title"];
            $data[$row["code"]]["items"][] = array(
                 "item" => $row["item"]
                ,"score" => $row["score"]
            );
        }

        return $data;

    } //}}}

    public function Update($data)
    { //{{{

        $insert_query = [];
        foreach($data as $key => $val){
            $insert_query[] = "(
                 NULL
                ,'".strtoupper(specialchars($val["code"]))."'
                ,'".$val["title"]."'
                ,'".$val["item"]."'
                ,'".$val["score"]."'
                ,".($key+1)."
            )";
        }

        $query = "
            truncate table
                ".$this->table_name."
        ";
        $this->wrdb->query($query);

        $query = "
            insert into
                ".$this->table_name."
            values
                ".join(",", $insert_query)."
        ";
        $idx = $this->wrdb->insert($query);

        return 1;

    } //}}}

}
