<?php
namespace App\Models\Management\User;
use App\Models\CommonModel;

class ApplicationModel extends CommonModel
{
    private $table_name = "application";

    public function getList($user_uuid)
    { //{{{
        $data = [];
        $query = "
            select
                 *
                ,(
                    select
                        profile_img_uuid
                    from
                        company
                    where
                        uuid = ".$this->table_name.".company_uuid
                    limit 1
                ) as company_img_uuid
                ,(
                    select
                        title
                    from
                        db_job_employment_type
                    where
                        idx = ".$this->table_name.".employment_type
                    limit 1
                ) as employment_type_title
                ,(
                    select
                        title
                    from
                        db_job_career
                    where
                        idx = ".$this->table_name.".career
                    limit 1
                ) as career_title
                ,(
                    select
                        title
                    from
                        db_job_profession
                    where
                        idx = ".$this->table_name.".profession
                    limit 1
                ) as profession_title
                ,(
                    select
                        status
                    from
                        ".$this->table_name."_receipt
                    where
                        user_uuid = '".$user_uuid."' and
                        application_uuid = ".$this->table_name.".uuid
                    limit 1
                ) as status
            from
                ".$this->table_name."
            where
                uuid IN (
                    select
                        application_uuid
                    from
                        ".$this->table_name."_receipt
                    where
                        user_uuid = '".$user_uuid."'
                )
            order by
                idx desc
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $address = explode(" ", $row["address"]);
            $row["address"] = $address[0]." ".$address[1];
            $data[] = $row;
        }

        return $data;

    } //}}}

    public function DetailReceipt($user_uuid, $application_uuid)
    { //{{{
        $data = [];
        $query = "
            select
                *
            from
                ".$this->table_name."_receipt
            where
                user_uuid = '".$user_uuid."' and
                application_uuid = '".$application_uuid."'
            limit 1
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();
        $data = $row;

        return $data;

    } //}}}

}
