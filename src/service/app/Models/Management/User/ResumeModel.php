<?php
namespace App\Models\Management\User;
use App\Models\CommonModel;

class ResumeModel extends CommonModel
{
    private $table_name = "resume";

    public function getList($user_uuid)
    { //{{{
        $data = [];
        $query = "
            select
                *
                ,(
                    select
                        title
                    from
                        db_job_career
                    where
                        idx = ".$this->table_name.".category_career
                ) as career_title
                ,(
                    select
                        title
                    from
                        db_job_employment_type
                    where
                        idx = ".$this->table_name.".category_employment_type
                ) as employment_type_title
                ,(
                    select
                        title
                    from
                        db_job_profession
                    where
                        idx = ".$this->table_name.".category_profession
                ) as profession_title
            from
                ".$this->table_name."
            where
                user_uuid = '".$user_uuid."'
            order by
                idx desc
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $data[] = $row;
        }

        return $data;

    } //}}}

    public function Detail($user_uuid, $uuid)
    { //{{{
        $data = [];
        $query = "
            select
                 *
                ,(
                    select
                        title
                    from
                        db_job_career
                    where
                        idx = ".$this->table_name.".category_career
                ) as career_title
                ,(
                    select
                        title
                    from
                        db_job_employment_type
                    where
                        idx = ".$this->table_name.".category_employment_type
                ) as employment_type_title
                ,(
                    select
                        title
                    from
                        db_job_profession
                    where
                        idx = ".$this->table_name.".category_profession
                ) as profession_title
            from
                ".$this->table_name."
            where
                user_uuid = '".$user_uuid."' and
                uuid = '".$uuid."'
            limit 1
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();
        $data = $row;

        return $data;

    } //}}}

    public function Create($user_uuid, $files, $data)
    { //{{{
        helper(["specialchars", "uuid_v4"]);

        $uuid = gen_uuid_v4();

        $title = specialchars($data["title"]);
        $introduce = addslashes($data["introduce"]);

        $school = [];
        $last_school_graduation = "";
        if(isset($data["school_grade"]) && is_array($data["school_grade"]) && count($data["school_grade"])){
            foreach($data["school_grade"] as $key => $val){
                $school[] = array(
                     "grade" => $data["school_grade"][$key]
                    ,"title" => specialchars($data["school_title"][$key])
                    ,"class" => specialchars($data["school_class"][$key])
                    ,"degree" => @$data["school_degree"][$key]
                    ,"graduate" => @$data["school_graduate"][$key]
                    ,"start_year" => @$data["school_start_year"][$key]
                    ,"start_month" => @$data["school_start_month"][$key]
                    ,"end_year" => @$data["school_end_year"][$key]
                    ,"end_month" => @$data["school_end_month"][$key]
                    ,"remark" => @addslashes($data["school_remark"][$key])
                );
                $last_school_graduation = $data["school_graduate"][$key];
            }
        }
        $school_json = json_encode($school, JSON_UNESCAPED_UNICODE);

        $career = [];
        $exployment_period = 0;
        if(isset($data["career_title"]) && is_array($data["career_title"]) && count($data["career_title"])){
            foreach($data["career_title"] as $key => $val){
                if(!$val){ continue; }

                $career[] = array(
                     "title" => specialchars($data["career_title"][$key])
                    ,"position" => specialchars($data["career_position"][$key])
                    ,"rank" => specialchars($data["career_rank"][$key])
                    ,"remark" => addslashes($data["career_remark"][$key])
                    ,"start_year" => @$data["career_start_year"][$key]
                    ,"start_month" => @$data["career_start_month"][$key]
                    ,"end_year" => @$data["career_end_year"][$key]
                    ,"end_month" => @$data["career_end_month"][$key]
                );

                $career_start_year = trim(str_replace("년", "", @$data["career_start_year"][$key]));
                $career_start_month = trim(str_replace("월", "", @$data["career_start_month"][$key]));
                $career_end_year = trim(str_replace("년", "", @$data["career_end_year"][$key]));
                $career_end_month = trim(str_replace("월", "", @$data["career_end_month"][$key]));

                $diff_year = ($career_end_year - $career_start_year) * 12;
                $diff_month = ($career_end_month - $career_start_month);
                $exployment = $diff_year - $diff_month;
                $exployment = ($exployment< 0)? 0 : $exployment;

                $exployment_period += $exployment;
            }
        }
        $career_json = json_encode($career, JSON_UNESCAPED_UNICODE);

        $award = [];
        $award_count = 0;
        if(isset($data["award_title"]) && is_array($data["award_title"]) && count($data["award_title"])){
            foreach($data["award_title"] as $key => $val){
                if(!$val){ continue; }

                $award[] = array(
                     "title" => specialchars($data["award_title"][$key])
                    ,"organizer" => specialchars($data["award_organizer"][$key])
                    ,"remark" => addslashes($data["award_remark"][$key])
                    ,"year" => @$data["award_year"][$key]
                    ,"month" => @$data["award_month"][$key]
                    ,"day" => @$data["award_day"][$key]
                );
            }
            $award_count = $key + 1;
        }
        $award_json = json_encode($award, JSON_UNESCAPED_UNICODE);

        $license = [];
        $license_count = 0;
        if(isset($data["license_title"]) && is_array($data["license_title"]) && count($data["license_title"])){
            foreach($data["license_title"] as $key => $val){
                if(!$val){ continue; }

                $license[] = array(
                     "title" => specialchars($data["license_title"][$key])
                    ,"agency" => specialchars($data["license_agency"][$key])
                    ,"remark" => addslashes($data["license_remark"][$key])
                    ,"year" => $data["license_year"][$key]
                    ,"month" => $data["license_month"][$key]
                    ,"day" => $data["license_day"][$key]
                );
            }
            $license_count = $key + 1;
        }
        $license_json = json_encode($license, JSON_UNESCAPED_UNICODE);

        $skill_lists = [];
        $skill_count = 0;
        $skill = @$data["skill"];
        $skill = explode(",", $skill);
        if(@is_array($skill) && count($skill) > 0){
            foreach($skill as $key => $val){
                $skill_lists[] = trim($val);
                $skill_count++;
            }
        }
        $skill_json = json_encode($skill_lists, JSON_UNESCAPED_UNICODE);

        // portfolio
        $portfolio = [];
        $portfolio_lists = [];
        if(is_array($files["portfolio"]) && count($files["portfolio"]) > 0){
            foreach($files["portfolio"]["name"] as $key => $val){
                if(!$val){ continue; }

                $portfolio_lists[] = array(
                     "name" => $files["portfolio"]["name"][$key]
                    ,"type" => $files["portfolio"]["type"][$key]
                    ,"tmp_name" => $files["portfolio"]["tmp_name"][$key]
                    ,"error" => $files["portfolio"]["error"][$key]
                    ,"size" => $files["portfolio"]["size"][$key]
                );

            }

            foreach($portfolio_lists as $file){
                if($file["error"] == 0){
                    $portfolio_uuid = $this->uploadFiles($file);
                    $portfolio[] = $portfolio_uuid;
                }
            }
        }
        $portfolio_json = json_encode($portfolio, JSON_UNESCAPED_UNICODE);

        $query = "
            insert into
                ".$this->table_name."
            set
                 user_uuid = '".$user_uuid."'
                ,uuid = '".$uuid."'
                ,title = '".$title."'
                ,category_profession = '".$data["category_profession"]."'
                ,category_employment_type = '".$data["category_employment_type"]."'
                ,category_career = '".$data["category_career"]."'
                ,category_pay_type = '".$data["category_pay_type"]."'
                ,category_pay = ".@(int)str_replace(",", "", $data["category_pay"])."
                ,introduce = '".$introduce."'
                ,school = '".$school_json."'
                ,career = '".$career_json."'
                ,award = '".$award_json."'
                ,license = '".$license_json."'
                ,skill = '".$skill_json."'
                ,portfolio = '".$portfolio_json."'
                ,last_school_graduation = '".$last_school_graduation."'
                ,exployment_period = ".(int)$exployment_period."
                ,award_count = ".(int)$award_count."
                ,license_count = ".(int)$license_count."
                ,skill_count = ".(int)$skill_count."
                ,register_date = '".date("Y-m-d H:i:s")."'
        ";
        $idx = $this->wrdb->insert($query);

        return $uuid;

    } //}}}

    public function Update($user_uuid, $files, $data)
    { //{{{
        helper(["specialchars", "uuid_v4"]);

        $uuid = gen_uuid_v4();

        $title = specialchars($data["title"]);
        $introduce = addslashes($data["introduce"]);

        $school = [];
        $last_school_graduation = "";
        if(isset($data["school_grade"]) && is_array($data["school_grade"]) && count($data["school_grade"])){
            foreach($data["school_grade"] as $key => $val){
                $school[] = array(
                     "grade" => $data["school_grade"][$key]
                    ,"title" => specialchars($data["school_title"][$key])
                    ,"class" => specialchars($data["school_class"][$key])
                    ,"degree" => @$data["school_degree"][$key]
                    ,"graduate" => @$data["school_graduate"][$key]
                    ,"start_year" => @$data["school_start_year"][$key]
                    ,"start_month" => @$data["school_start_month"][$key]
                    ,"end_year" => @$data["school_end_year"][$key]
                    ,"end_month" => @$data["school_end_month"][$key]
                    ,"remark" => @addslashes($data["school_remark"][$key])
                );
                $last_school_graduation = $data["school_graduate"][$key];
            }
        }
        $school_json = json_encode($school, JSON_UNESCAPED_UNICODE);

        $career = [];
        $exployment_period = 0;
        if(isset($data["career_title"]) && is_array($data["career_title"]) && count($data["career_title"])){
            foreach($data["career_title"] as $key => $val){
                if(!$val){ continue; }

                $career[] = array(
                     "title" => specialchars($data["career_title"][$key])
                    ,"position" => specialchars($data["career_position"][$key])
                    ,"rank" => specialchars($data["career_rank"][$key])
                    ,"remark" => addslashes($data["career_remark"][$key])
                    ,"start_year" => @$data["career_start_year"][$key]
                    ,"start_month" => @$data["career_start_month"][$key]
                    ,"end_year" => @$data["career_end_year"][$key]
                    ,"end_month" => @$data["career_end_month"][$key]
                );

                $career_start_year = trim(str_replace("년", "", @$data["career_start_year"][$key]));
                $career_start_month = trim(str_replace("월", "", @$data["career_start_month"][$key]));
                $career_end_year = trim(str_replace("년", "", @$data["career_end_year"][$key]));
                $career_end_month = trim(str_replace("월", "", @$data["career_end_month"][$key]));

                if($career_start_year && $career_end_year){
                    $diff_year = ($career_end_year - $career_start_year) * 12;
                    $diff_month = ($career_end_month - $career_start_month);
                    $exployment = $diff_year - $diff_month;
                    $exployment = ($exployment< 0)? 0 : $exployment;

                    $exployment_period += $exployment;
                }
            }
        }
        $career_json = json_encode($career, JSON_UNESCAPED_UNICODE);

        $award = [];
        $award_count = 0;
        if(isset($data["award_title"]) && is_array($data["award_title"]) && count($data["award_title"])){
            foreach($data["award_title"] as $key => $val){
                if(!$val){ continue; }

                $award[] = array(
                     "title" => specialchars($data["award_title"][$key])
                    ,"organizer" => specialchars($data["award_organizer"][$key])
                    ,"remark" => addslashes($data["award_remark"][$key])
                    ,"year" => @$data["award_year"][$key]
                    ,"month" => @$data["award_month"][$key]
                    ,"day" => @$data["award_day"][$key]
                );
            }
            $award_count = $key + 1;
        }
        $award_json = json_encode($award, JSON_UNESCAPED_UNICODE);

        $license = [];
        $license_count = 0;
        if(isset($data["license_title"]) && is_array($data["license_title"]) && count($data["license_title"])){
            foreach($data["license_title"] as $key => $val){
                if(!$val){ continue; }

                $license[] = array(
                     "title" => specialchars($data["license_title"][$key])
                    ,"agency" => specialchars($data["license_agency"][$key])
                    ,"remark" => addslashes($data["license_remark"][$key])
                    ,"year" => $data["license_year"][$key]
                    ,"month" => $data["license_month"][$key]
                    ,"day" => $data["license_day"][$key]
                );
            }
            $license_count = $key + 1;
        }
        $license_json = json_encode($license, JSON_UNESCAPED_UNICODE);

        $skill_lists = [];
        $skill_count = 0;
        $skill = @$data["skill"];
        $skill = explode(",", $skill);
        if(@is_array($skill) && count($skill) > 0){
            foreach($skill as $key => $val){
                $skill_lists[] = trim($val);
                $skill_count++;
            }
        }
        $skill_json = json_encode($skill_lists, JSON_UNESCAPED_UNICODE);

        // file handling (upload & remove)
        $file_items = $files["portfolio"];
        $file_field_name = "portfolio";
        $files_query = ",".$file_field_name." = null";

        $files_array = $this->fileHandle($file_items, $data["pre_file"], $data["remove_file"]);

        $files_json = json_encode($files_array, JSON_UNESCAPED_UNICODE);
        $files_query = ",".$file_field_name." = '".$files_json."'";


        /*
        // portfolio
        $portfolio = [];
        $portfolio_lists = [];
        if(is_array($files["portfolio"]) && count($files["portfolio"]) > 0){
            foreach($files["portfolio"]["name"] as $key => $val){
                if(!$val){ continue; }

                $portfolio_lists[] = array(
                     "name" => $files["portfolio"]["name"][$key]
                    ,"type" => $files["portfolio"]["type"][$key]
                    ,"tmp_name" => $files["portfolio"]["tmp_name"][$key]
                    ,"error" => $files["portfolio"]["error"][$key]
                    ,"size" => $files["portfolio"]["size"][$key]
                );

            }

            foreach($portfolio_lists as $file){
                if($file["error"] == 0){
                    $portfolio_uuid = $this->uploadFiles($file);
                    $portfolio[] = $portfolio_uuid;
                }
            }
        }
        $portfolio_json = json_encode($portfolio, JSON_UNESCAPED_UNICODE);
         */

        $query = "
            update
                ".$this->table_name."
            set
                 user_uuid = '".$user_uuid."'
                ,uuid = '".$uuid."'
                ,title = '".$title."'
                ,category_profession = '".$data["category_profession"]."'
                ,category_employment_type = '".$data["category_employment_type"]."'
                ,category_career = '".$data["category_career"]."'
                ,category_pay_type = '".$data["category_pay_type"]."'
                ,category_pay = ".@(int)str_replace(",", "", $data["category_pay"])."
                ,introduce = '".$introduce."'
                ,school = '".$school_json."'
                ,career = '".$career_json."'
                ,award = '".$award_json."'
                ,license = '".$license_json."'
                ,skill = '".$skill_json."'
                ".$files_query."
                ,last_school_graduation = '".$last_school_graduation."'
                ,exployment_period = ".(int)$exployment_period."
                ,award_count = ".(int)$award_count."
                ,license_count = ".(int)$license_count."
                ,skill_count = ".(int)$skill_count."
            where
                uuid = '".$data["uuid"]."'
            limit 1
        ";
        $idx = $this->wrdb->insert($query);

        return $uuid;

    } //}}}

    public function Delete($user_uuid, $uuid)
    { //{{{
        $query = "
            delete from
                ".$this->table_name."
            where
                uuid = '".$uuid."' and
                user_uuid = '".$user_uuid."'
            limit 1
        ";
        $this->wrdb->query($query);

        return 1;

    } //}}}

}
