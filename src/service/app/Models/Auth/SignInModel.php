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
        // type 1 판매기업
        if($company_type==1) {
            return $this->SignInUser($email, $password);
        }
        // 아니면, 구매기업
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
            $_SESSION["login"] = "success";
            $_SESSION["login_info"] = array(
                 "uuid" => $row["uuid"]
                ,"type" => "buyer"
                ,"status" => $row["status"]
                ,"buyer_name" => $row["buyer_name"]
                ,"company_name" => $row["company_name"]
                ,"email" => $row["email"]
            );
            $_SESSION["buyer_info"] = $this->getBuyerinfo();
            $_SESSION["Expectation"] = $this->ExpectationMoney();
            $_SESSION["Contract"]= $this->getContractList();
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
            $_SESSION["login"] = "success";
            $_SESSION["login_info"] = array(
                 "uuid" => $row["uuid"]
                ,"type" => "seller"
                ,"status" => $row["status"]
                ,"seller_name" => $row["seller_name"]
                ,"company_name" => $row["company_name"]
                ,"email" => $row["email"]

            );
            $uuid = $_SESSION["login_info"]["uuid"];
            $_SESSION["totalSales"] = $this->getTotalSales($uuid);
            $_SESSION["expectationSales"] = $this->getexpectationSales($uuid);
            $_SESSION["completionContract"] = $this->getCompletionContract($uuid);
            $_SESSION["disabledCount"] = $this->getDisabledCount($uuid);
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

    public function getBuyerinfo(){
        $uuid = $_SESSION["login_info"]["uuid"];
        $buyer_info = [];
        $query = "
            select
                *
            from
              buyer_company
            where uuid = '".$uuid."'
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $buyer_info = $row;
        }
        return $buyer_info;
    }
    public function ExpectationMoney(){
        $uuid = $_SESSION["login_info"]["uuid"];
        $Expectation =[];
        $query = "       
        select sum(b.product_price) as Expectation
        from contract_condition a
        join seller_product b on a.seller_uuid = b.register_id
        where a.buyer_uuid = '".$uuid."'
        and contract_status = 1
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $Expectation = $row;
        }
        return $Expectation;
    }

    public function getContractList(){
        $uuid = $_SESSION["login_info"]["uuid"];
        $contract = [];
        $query = "       
        select *,a.register_date as 'contract_regdt' 
        from contract_condition a
        join seller_product b on a.seller_uuid = b.register_id
        where a.buyer_uuid = '".$uuid."'
        and contract_status = '5'
        limit 4;
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $contract[] = $row;
        }
        return $contract;
    }
    public function getTotalSales($uuid){
        $sales =[];
        $query = "
            select
              sum(b.product_price) as 'price'
            from
              contract_condition a
            join seller_product b on a.seller_uuid = b.register_id
            where a.seller_uuid = '$uuid'
            and contract_status = 5
                     
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $sales = $row;
        }
        return $sales;
    }
    public function getexpectationSales($uuid){
        $expectationSales =[];
        $query = "
            select
              sum(b.product_price) as 'price'
            from
              contract_condition a
            join seller_product b on a.seller_uuid = b.register_id
            where a.seller_uuid = '$uuid'
              and (contract_status = 2 or contract_status = 5)

                     
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $expectationSales = $row;
        }
        return $expectationSales;
    }
    public function getCompletionContract($uuid){
        $completionContract =[];
        $query = "
            select
             count(*) as'count'
            from
              contract_condition          
            where seller_uuid = '$uuid'
              and  contract_status = 5             
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $completionContract = $row;
        }
        return $completionContract;
    }

    public function getDisabledCount($uuid){
        $disabledCount =[];
        $query = "
            select
             severely_disabled,
             mild_disabled,
             company_name
            from
              seller_company          
            where uuid = '$uuid'            
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $disabledCount = $row;
        }
        return $disabledCount;
    }
}
