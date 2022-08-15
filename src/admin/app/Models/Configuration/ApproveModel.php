<?php
namespace App\Models\Configuration;
use App\Models\CommonModel;

class ApproveModel extends CommonModel
{
    private $table_name = "config_approve";

    public function getList()
    { //{{{
        $query = "
            select
                *
            from
                ".$this->table_name."
            order by
                idx asc
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();

        return $row;

    } //}}}

    public function Update($data)
    { //{{{

        $query = "
            insert into
                ".$this->table_name."
            set
                 idx = 1
                ,approve_signup_user                = ".(int)$data["approve_signup_user"]."
                ,approve_signup_company             = ".(int)$data["approve_signup_company"]." 
                ,approve_register_job               = ".(int)$data["approve_register_job"]." 
                ,approve_register_public_company    = ".(int)$data["approve_register_public_company"]." 
                ,approve_register_application       = ".(int)$data["approve_register_application"]." 
            on duplicate key update
                 approve_signup_user                = ".(int)$data["approve_signup_user"]."
                ,approve_signup_company             = ".(int)$data["approve_signup_company"]." 
                ,approve_register_job               = ".(int)$data["approve_register_job"]." 
                ,approve_register_public_company    = ".(int)$data["approve_register_public_company"]." 
                ,approve_register_application       = ".(int)$data["approve_register_application"]." 
        ";
        $this->wrdb->insert($query);

        return 1;

    } //}}}
}


