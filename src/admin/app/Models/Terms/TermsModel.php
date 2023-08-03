<?php
namespace App\Models\Terms;
use App\Models\CommonModel;

class TermsModel extends CommonModel
{
    private $table_name = "terms";

    public function getData($category)
    { //{{{
        $query = "
            select
                *
            from
                ".$this->table_name."
            where
                category = '".$category."'
            limit 1
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();

        return $row;

    } //}}}

    public function Register($data){
        $query = "
            insert into
                ".$this->table_name."
            
            set
                  category = '".$data["category"]."'
                ,contents = '".addslashes($data["contents"])."'
                ,register_date = '".date("Y-m-d H:i:s")."'
                ,terms_status = '1'
        ";
        $idx = $this->wrdb->insert($query);
        return 1;
    }

    public function Update($data)
    { //{{{

        $query = "
            replace into
                ".$this->table_name."
            set
                 category = '".$data["category"]."'
                ,contents = '".addslashes($data["contents"])."'
                ,update_date = '".date("Y-m-d H:i:s")."'
        ";
        $this->wrdb->insert($query);

        return 1;

    } //}}}

}
