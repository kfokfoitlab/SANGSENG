<?php
namespace App\Models\Management\User;
use App\Models\CommonModel;

class ManageJobModel extends CommonModel
{
    private $table_name = "job";

    /*
    public function getList($user_uuid)
    { //{{{
        $data = [];
        $query = "
            select
                *
            from
                ".$this->table_name."
            where
                user_uuid = '".$user_uuid."'
            limit 1
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();
        $data = $row;

        return $data;

    } //}}}

    public function Detail($user_uuid)
    { //{{{
        $data = [];
        $query = "
            select
                *
            from
                ".$this->table_name."
            where
                user_uuid = '".$user_uuid."'
            limit 1
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();
        $data = $row;

        return $data;

    } //}}}

    public function Create($user_uuid, $data)
    { //{{{
        helper(["specialchars"]);

        $query = "
            replace into
                ".$this->table_name."
            set
                 user_uuid = '".$user_uuid."'
                ,resume_uuid = '".$data["resume_uuid"]."'
                ,title = '".specialchars($data["title"])."'
                ,inline_pr = '".specialchars($data["inline_pr"])."'
                ,register_date = '".date("Y-m-d H:i:s")."' 
        ";
        $idx = $this->wrdb->insert($query);

        return $idx;

    } //}}}

    public function Update($user_uuid, $data)
    { //{{{
        helper(["specialchars"]);

        $query = "
            update
                ".$this->table_name."
            set
                 resume_uuid = '".$data["resume_uuid"]."'
                ,title = '".specialchars($data["title"])."'
                ,inline_pr = '".specialchars($data["inline_pr"])."'
                ,register_date = '".date("Y-m-d H:i:s")."' 
            where
                user_uuid = '".$user_uuid."'
            limit 1
        ";
        $this->wrdb->insert($query);

        return 1;

    } //}}}

    public function Delete($user_uuid, $idx)
    { //{{{
        $query = "
            delete from
                ".$this->table_name."
            where
                user_uuid = '".$user_uuid."' and
                idx = ".(int)$idx."
        ";
        $this->wrdb->query($query);

        return 1;

    } //}}}
     */

}
