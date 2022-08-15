<?php
namespace App\Models\Database;
use App\Models\CommonModel;

class DatabaseModel extends CommonModel
{
    private $table_name = "db_impairment";

    public function getImpairmentAll()
    { //{{{
        $data = [];  

        $items = [
             "AssistiveDevice"
            ,"Degree"
            ,"PhysicalAbility"
            ,"Type"
        ];

        foreach($items as $val){
            $item = $this->{"getImpairment".$val}();
            $data[$val] = $item;
        }

        return $data;

    } //}}}

    public function getJobAll()
    { //{{{
        $data = [];  

        $items = [
             "Career"
            ,"EmploymentType"
            ,"WorkType"
            ,"Profession"
            ,"Welfare"
        ];

        foreach($items as $val){
            $item = $this->{"getJob".$val}();
            $data[$val] = $item;
        }

        return $data;

    } //}}}


    public function getImpairmentAssistiveDevice($class = "assistive_device")
    { //{{{
        $data = [];

        $query = "
            select
                *
            from
                ".$this->table_name."_".$class."
            order by
                ordering asc
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data[$row["code"]][] = $row;
        }

        return $data;

    } //}}}

    public function getImpairmentDegree($class = "degree")
    { //{{{
        $data = [];

        $query = "
            select
                *
            from
                ".$this->table_name."_".$class."
            order by
                ordering asc
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data[$row["code"]][] = $row;
        }

        return $data;

    } //}}}

    public function getImpairmentPhysicalAbility($class = "physical_ability")
    { //{{{
        $data = [];

        $query = "
            select
                *
            from
                ".$this->table_name."_".$class."
            order by
                ordering asc
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data[$row["code"]][] = $row;
        }

        return $data;

    } //}}}

    public function getImpairmentType($class = "type")
    { //{{{
        $data = [];

        $query = "
            select
                *
            from
                ".$this->table_name."_".$class."
            order by
                this_level, this_level_order
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data["level".$row["this_level"]][] = $row;
        }

        return $data;

    } //}}}


    public function getJobCareer($class = "career")
    { //{{{
        $data = [];
        $query = "
            select
                *
            from
                db_job_".$class."
            order by
                ordering
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data[] = $row;
        }

        return $data;
    } //}}}

    public function getJobEmploymentType($class = "employment_type")
    { //{{{
        $data = [];
        $query = "
            select
                *
            from
                db_job_".$class."
            order by
                ordering
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data[] = $row;
        }

        return $data;
    } //}}}

    public function getJobWorkType($class = "work_type")
    { //{{{
        $data = [];
        $query = "
            select
                *
            from
                db_job_".$class."
            order by
                ordering
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data[] = $row;
        }

        return $data;
    } //}}}

    public function getJobProfession($class = "profession")
    { //{{{
        $data = [];
        $query = "
            select
                *
            from
                db_job_".$class."
            order by
                ordering
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data[] = $row;
        }

        return $data;
    } //}}}

    public function getJobWelfare($class = "welfare")
    { //{{{
        $data = [];
        $query = "
            select
                *
            from
                db_job_".$class."
            order by
                ordering
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data[] = $row;
        }

        return $data;
    } //}}}

}

