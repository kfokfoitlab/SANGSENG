<?php
namespace App\Models_m\Auth;
use App\Models_m\CommonModel;

class SignInModel extends CommonModel
{
    public function SignIn($email, $password,$company_type)
    {
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
                ,"phone" => $row["phone"]
            );
            $_SESSION["buyer_info"] = $this->getBuyerinfo();
            $_SESSION["Expectation"] = $this->ExpectationMoney();
            $_SESSION["Contract"]= $this->getContractList();
            $_SESSION["ReductionMoney"]= $this->BuyerReduction();

            return array(
                 "result" => "success"
                ,"buyer_notification"=> $row["buyer_notification"]
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
                ,"phone" => $row["phone"]
            );
            $uuid = $_SESSION["login_info"]["uuid"];
            $_SESSION["totalSales"] = $this->getTotalSales($uuid);
            $_SESSION["expectationSales"] = $this->getexpectationSales($uuid);
            $_SESSION["completionContract"] = $this->getCompletionContract($uuid);
            $_SESSION["disabledCount"] = $this->getWorkerCount();
            $_SESSION["sellerinfo"] = $this->Sellerinfo();
            return array(
                 "result" => "success"
            ,"seller_notification"=> $row["seller_notification"]
            );
        }else{
            return array(
                 "result" => "failed"
                ,"type" => "Invalid"
            );
        }

    } //}}}

    public function Sellerinfo(){
        $uuid = $_SESSION["login_info"]["uuid"];
        $seller_info = [];
        $query = "
            select
                *
            from
              seller_company
            where uuid = '".$uuid."'
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $seller_info = $row;
        }
        return $seller_info;
    }

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
        select sum(product_price) as Expectation
        from contract_condition 
        where buyer_uuid = '".$uuid."'
        and contract_status = 1
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $Expectation = $row;
        }
        return $Expectation;
    }

    public function BuyerReduction(){
        $uuid = $_SESSION['login_info']['uuid'];
        $buyer_reduction =[];
        $query = "
            select
                sum(reduction_money) as buyer_reduction
            from
                contract_condition
            where 1=1
            and contract_status = '5'
            and   buyer_uuid = '".$uuid."'          
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $buyer_reduction= $row;
        }
        return $buyer_reduction;
    }

    public function getContractList(){
        $uuid = $_SESSION["login_info"]["uuid"];
        $contract = [];
        $query = "       
        select *,register_date as 'contract_regdt' 
        from contract_condition 
        where buyer_uuid = '".$uuid."'
        and contract_status = '2'
        and del_yn != 'Y'
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
              sum(product_price) as 'price'
            from
              contract_condition 
            where seller_uuid = '".$uuid."'
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
              sum(product_price) as 'price'
            from
              contract_condition 
            where seller_uuid = '$uuid'
              and contract_status = 5                   
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
            where seller_uuid = '".$uuid."'
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
            where uuid = '".$uuid."'            
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){
            $disabledCount = $row;
        }
        return $disabledCount;
    }
	
	// 근로자 수
	public function getWorkerCount($table_name = "seller_company_worker"){
		
		$seller_uuid = $_SESSION["login_info"]["uuid"];
		$seller_data = $this->getSellerInfo($seller_uuid);
		
		$query = "
            select
                count(*) as worker_cnt,count(case when disability_degree=1 then 1 end) as degree_1_cnt,
                count(case when disability_degree=2 then 1 end) as degree_2_cnt
            from ".$table_name." where 1=1
			 and register_id= '".$seller_data["uuid"]."'
			 and (del_yn != 'Y' or del_yn is null)
			 and (status = 5)
        ";

		$this->rodb->query($query);
		while($row = $this->rodb->next_row()){
			$data_cnt = $row;
		}
		return $data_cnt;
	}
}