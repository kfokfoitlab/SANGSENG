<?php
namespace App\Models\Auth;
use App\Models\CommonModel;

class SignInModel extends CommonModel
{
    public function SignIn($email, $password,$company_type)
    { // {{{

      /*  helper("specialchars");
        $user_id = specialchars($user_id);

        $check_email=preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $user_id);*/
        // 이메일 형식이면 인재회원
        if($company_type==1) {
            return $this->SignInUser($email, $password);
        }
        // 아니면, 기업회원
        else {
            return $this->SignInCompany($email, $password);
        }

    } // }}}

    private function SignInUser($email, $password)
    { //{{{
        $query = "
            select
                *
            from
                buyer_company
            where
                status != '9' and
                email = '".$email."' and
                password = SHA2('".$password."', 256)
            limit 1
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();
        if(isset($row["idx"])){
            // verify password
         /*   if(!password_verify($password, $row["password_hash"])){
                return array(
                     "result" => "failed"
                    ,"type" => "Invalid"
                );
            }*/

            $_SESSION["login"] = "success";
            $_SESSION["login_info"] = array(
                 "uuid" => $row["uuid"]
                ,"status" => $row["status"]
                ,"name" => $row["name"]
                ,"email" => $row["email"]
            );

            return array(
                 "result" => "success"
            );

        }else{
            return array(
                 "result" => "failed"
                ,"type" => "Invalid"
            );
        }

    } //}}}

    private function SignInCompany($email, $password)
    { //{{{
        $query = "
            select
                *
            from
                seller_company
            where
                status != '9' and
                email = '".$email."' and
                password = SHA2('".$password."', 256)
            limit 1
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();
        if(isset($row["idx"])){
            // verify password
            if(!password_verify($password, $row["password_hash"])){
                return array(
                     "result" => "failed"
                    ,"type" => "Invalid"
                );
            }

            $_SESSION["login"] = "success";
            $_SESSION["login_info"] = array(
                 "uuid" => $row["uuid"]
                ,"profile_img_uuid" => $row["profile_img_uuid"]
                ,"type" => "company"
                ,"status" => $row["status"]
                ,"verification" => $row["verification"]
                ,"name" => $row["company_name"]
                ,"manager_name" => $row["manager_name"]
                ,"email" => $row["manager_email"]
            );

            return array(
                 "result" => "success"
            );

        }else{
            return array(
                 "result" => "failed"
                ,"type" => "Invalid"
            );
        }

    } //}}}

}
