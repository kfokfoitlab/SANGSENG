<?php
namespace App\Models;
use App\Models\dbClasses\dbModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

use function mkdir;
class CommonModel extends dbModel
{
    /**
     * 환경설정
     */
    public function getConfiguration($class)
    { //{{{
        $query = "
            select
                *
            from
                config_".$class."
            order by
                idx
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();

        return $row;
    } //}}}

    /**
     * 파일 업로드 공통 모듈
     * - 대상을 DB에 BLOB로 저장함.
     * - uuid가 있으면 replace
     *
     * @return string uuid
     */
    public function uploadFiles($files, $uuid = null)
    { // {{{
        helper("specialchars");
        helper("uuid_v4");


        if($files["error"] == 0){

            if(!$uuid){
                $uuid = gen_uuid_v4();
            }

            # file info
            $origin_file_name = $files["name"];
            $file_type = $files["type"];
            $file_tmp_name = $files["tmp_name"];
            $file_size = round($files["size"] / 1024);
            $file_ext = pathinfo($files["name"], PATHINFO_EXTENSION);
            $file_new_name = uniqid().".".$file_ext;

            # image size
            $file_image_info = @getimagesize($file_tmp_name);
            if($file_image_info){
                $file_type = $file_image_info["mime"]; # 좀 더 정확한 정보로
                $image_size_width = $file_image_info[0];
                $image_size_height = $file_image_info[1];

                $image_size_width_query = ",image_size_width = ".(int)$image_size_width;
                $image_size_height_query = ",image_size_height = ".(int)$image_size_height;
            }

            # binary
            $imageblob = addslashes(fread(fopen($files["tmp_name"], "r"), filesize($files["tmp_name"])));

            # referer info
            $referer_controller = $this->router->controllerName();
            $referer_method = $this->router->methodName();

            # insert DB
            $query = "
                replace into
                    upload_files
                set
                     uuid = '".$uuid."'
                    ,origin_file_name = '".specialchars($origin_file_name)."'
                    ,file_name = '".$file_new_name."'
                    ,file_extension = '".$file_ext."'
                    ,file_path = null
                    ,fullpath = null
                    ,webpath = null
                    ,file_type = '".$file_type."'
                    ,file_size = ".(int)@$file_size."
                    ".@$image_size_width_query."
                    ".@$image_size_height_query."
                    ,referer_controller = '".$referer_controller."'
                    ,referer_method = '".$referer_method."'
                    ,binary_data = '".$imageblob."'
                    ,registration_date = '".date("Y-m-d H:i:s")."'
            ";
            $idx = $this->wrdb->insert($query);

            return $uuid;
        } 
        else {
            return false;
        }

    } // }}}


    /**
     * 파일 업로드 공통 모듈
     * - 대상을 특정 디렉토리에 파일로 저장함.
     * - uuid가 있으면 replace
     *
     * @return string uuid
     */
    public function uploadFilesDirectory($files, $uuid = null)
    { // {{{
        helper("specialchars");
        helper("uuid_v4");


        if($files["error"] == 0){

            if(!$uuid){
                $uuid = gen_uuid_v4();
            }

            # file info
            $origin_file_name = $files["name"];
            $file_type = $files["type"];
            $file_tmp_name = $files["tmp_name"];
            $file_size = round($files["size"] / 1024);
            $file_ext = pathinfo($files["name"], PATHINFO_EXTENSION);
            $file_new_name = uniqid().".".$file_ext;

            # image size
            $file_image_info = @getimagesize($file_tmp_name);
            if($file_image_info){
                $file_type = $file_image_info["mime"]; # 좀 더 정확한 정보로
                $image_size_width = $file_image_info[0];
                $image_size_height = $file_image_info[1];

                $image_size_width_query = ",image_size_width = ".(int)$image_size_width;
                $image_size_height_query = ",image_size_height = ".(int)$image_size_height;
            }

            # referer info
            $referer_controller = $this->router->controllerName();
            $referer_method = $this->router->methodName();

            /**
             * 디렉토리에 파일 저장 방식
             */
            # target directory (해당년도 주차)
            $seed = date("Y/W");
            $target_dir = ROOTPATH."/public/uploads/upload_files/".$seed;
            $web_path = "/uploads/upload_files/".$seed."/".$file_new_name;

            exec("mkdir -p ".$target_dir);

            # upload
            move_uploaded_file($file_tmp_name, $target_dir."/".$file_new_name);

            # insert DB
            $query = "
                insert into
                    upload_files
                set
                     uuid = '".$uuid."'
                    ,origin_file_name = '".specialchars($origin_file_name)."'
                    ,file_name = '".$file_new_name."'
                    ,file_extension = '".$file_ext."'
                    ,file_path = 'uploads/upload_files/".$seed."'
                    ,fullpath = '".$target_dir."/".$file_new_name."'
                    ,webpath = '".$web_path."'
                    ,file_type = '".$file_type."'
                    ,file_size = ".(int)@$file_size."
                    ".@$image_size_width_query."
                    ".@$image_size_height_query."
                    ,referer_controller = '".$referer_controller."'
                    ,referer_method = '".$referer_method."'
                    ,binary_data = null
                    ,registration_date = '".date("Y-m-d H:i:s")."'
            ";
            $idx = $this->wrdb->insert($query);

            return $uuid;
        } 
        else {
            return false;
        }

    } // }}}


    /**
     * 파일 처리 공통 모듈
     * - 신규 업로드 & 기존 업로드 대상 처리 등
     * 
     * 1. 업로드 파일이 있으면 업로드 후 DB 저장
     *      1.1 기존에 업로드 된 파일은 삭제함.
     * 2. 업로드 파일이 없고, 기존에 등록된 파일 있으면 DB유지
     * 3. 삭제 목록이 있으면, 파일 삭제 후 DB 삭제.
     *
     * @param array $file_items     // 업로드 대상
     * @param array $pre_file       // 기존에 업로드한 대상
     * @param array $remove_file    // 삭제 대상
     * 
     * @return array files_array    // DB에 저장될 신규 배열값
     *
     */
    public function fileHandle($file_items, $pre_file, $remove_file)
    { //{{{
        $file_lists = [];
        $files_array = $pre_file;

        if(@is_array($file_items) && count($file_items)){
            foreach($file_items["name"] as $key => $val){
                $file_lists[] = array(
                     "name" =>      $file_items["name"][$key]
                    ,"type" =>      $file_items["type"][$key]
                    ,"tmp_name" =>  $file_items["tmp_name"][$key]
                    ,"error" =>     $file_items["error"][$key]
                    ,"size" =>      $file_items["size"][$key]
                );

            }

            foreach($file_lists as $key => $file){
                if($file["error"] == 0){
                    // 해당키에 업로드한 파일 있으면 기존 파일은 삭제 대상으로 포함.
                    if($pre_file[$key]){
                        $remove_file[$key] = $pre_file[$key];
                    }

                    $files_uuid = $this->uploadFiles($file);
                    $files_array[$key] = $files_uuid;
                }
            }
        }

        // remove file
        $result =  $this->removeBinaryFiles($remove_file);

        return $files_array;

    } //}}}


    /**
     * 파일 삭제 공통 모듈 - 바이너리 파일 삭제용
     *
     * @param array $uuids     // 삭제 대상 file uuid 배열
     *
     * @return boolean
     */
    public function removeBinaryFiles(array $uuid)
    { //{{{

        if(!isset($uuid)){
            return 0;
        }
        else if(!is_array($uuid)){
            $uuid = array($uuid);
        }
        else if(count($uuid) > 0){
            return 0;
        }

        $query = "
            delete from
                upload_files
            where
                uuid IN ('".join("','", $uuid)."')
        ";
        $this->wrdb->query($query);

        return 1;
          
    } //}}}


    /**
     * 이미지 불러오기
     * - 바이너리 형태로 불러와서 base64 encoding
     */
    public function getImage($uuid)
    { //{{{
        $query = "
            select
                *
            from
                upload_files
            where
                uuid = '".$uuid."'
            limit 1
        "; 
        $this->rodb->query($query);
        $row = $this->rodb->next_row();

        $binary_data = $row["binary_data"];
        //$binary_data = base64_encode($binary_data);
        $binary_data = ($binary_data);

        return array(
             "mime" => $row["file_type"]
            ,"data" => $binary_data
        );


    } //}}}
	
	public function getSellerInfo($uuid)
	{ //{{{
		$data = [];
		$query = "
            select
                *
            from
                seller_company
            where
                uuid = '".$uuid."'
            limit 1
        ";
		$this->rodb->query($query);
		while($row = $this->rodb->next_row()) {
			$data = $row;
		}
		return $data;
	} //}}}
	
	public function uploadFileNEW($files,$fileName,$allowed_ext,$fileName_ori){
		$error = $files["$fileName_ori"]['error'];
		$name = $files["$fileName_ori"]['name'];
		$exploded_file = explode(".",$name);
		$ext = array_pop($exploded_file);
		$target_dir = UPLOADPATH."/service/public/uploads/";
		$target_dir_admin = UPLOADPATH."/admin/public/uploads/";
		$file_tmp_name = $files["$fileName_ori"]["tmp_name"];




        if(!is_dir($target_dir)){
            mkdir($target_dir,0777,true);
        }
		if(!is_dir($target_dir_admin)){
			mkdir($target_dir_admin,0777,true);
		}
		
		if( !in_array($ext, $allowed_ext) ) {
			echo "허용되지 않는 확장자입니다.";
			exit;
		}
		if( $error != UPLOAD_ERR_OK ) {
			switch( $error ) {
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					echo "파일이 너무 큽니다. ($error)";
					break;
				case UPLOAD_ERR_NO_FILE:
					echo "파일이 첨부되지 않았습니다. ($error)";
					break;
				default:
					echo "파일이 제대로 업로드되지 않았습니다. ($error)";
			}
			exit;
		}
		move_uploaded_file($file_tmp_name,$target_dir.$fileName);
		copy($target_dir.$fileName,$target_dir_admin.$fileName);
	}
	
	public function downloadFileNew(){
		$target_Dir = ROOTPATH."/public/uploads/";
		$file = $_GET["fileName"];
		$file_ori = $_GET["fileNameOri"];
		$down = $target_Dir . $file;
		//	$filesize = filesize($down);
		
		if (file_exists($down)) {
			header("Content-Type:application/octet-stream");
			header("Content-Disposition:attachment;filename=$file_ori");
			header("Content-Transfer-Encoding:binary");
			header("Content-Length:" . filesize($target_Dir . $file));
			header("Cache-Control:cache,must-revalidate");
			header("Pragma:no-cache");
			header("Expires:0");
			if (is_file($down)) {
				$fp = fopen($down, "r");
				while (!feof($fp)) {
					$buf = fread($fp, 8096);
					$read = strlen($buf);
					print($buf);
					flush();
				}
				fclose($fp);
			}
		} else {
			echo "
				<script>alert('존재하지 않는 파일입니다.');
				history.back();</script>
				
				";
		}
	}

    public function getRegExcel(){
        $excel = [];
        $query = "
            select
                *
            from
                workers_excel
         order by idx desc
            limit 1
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()) {
            $excel = $row;
        }
        return $excel;
    }


    public function excelRead($files){
        require_once('PhpOffice/Psr/autoloader.php');
        require_once('PhpOffice/PhpSpreadsheet/autoloader.php');
        $allowed_ext = array('Xlsx','xlsx');
        if($files["excelupload"]["name"] != "") {
            $excelupload_ori = $files["excelupload"]["name"];
            $upload_excelupload_ori = "excelupload";
            $upload_excelupload_image = uniqid() . "." . pathinfo($files["excelupload"]["name"], PATHINFO_EXTENSION);
            $this->uploadFileNew($files, $upload_excelupload_image, $allowed_ext, $upload_excelupload_ori);
        }
        $inputFileName = ROOTPATH."/public/uploads/".$upload_excelupload_image;
        $spreadsheet = IOFactory::load($inputFileName);
        $Rows = $spreadsheet->getSheetByName('Sheet1')->toArray(null, true, true, true);
        $test =[];
        $data = [];
        for($i =6; $i <=count($Rows); $i++){
            $test[$i]["name"] = $Rows[$i]['A'];;
            $test[$i]["sdate"] = $Rows[$i]['B'];
            $test[$i]["edate"] = $Rows[$i]['C'];
            $test[$i]["birth"] = $Rows[$i]['D'];
            $test[$i]["status"] = $Rows[$i]['E'];
            $test[$i]["dis"] = $Rows[$i]['F'];
            $data[] = $test[$i];
         }
        $data[]['file'] = $upload_excelupload_image;
        return $data;
    }

    public function WorkersReg($data)
    {
        require_once('PhpOffice/Psr/autoloader.php');
        require_once('PhpOffice/PhpSpreadsheet/autoloader.php');

            $register_file = $data['register_file'];

        $inputFileName = ROOTPATH . "/public/uploads/" . $register_file;
        $spreadsheet = IOFactory::load($inputFileName);
        $Rows = $spreadsheet->getSheetByName('Sheet1')->toArray(null, true, true, true);
        $seller_uuid = $_SESSION["login_info"]["uuid"];
        $seller_data = $this->getSellerInfo($seller_uuid);
        for ($i = 6; $i <= count($Rows); $i++) {
            if($Rows[$i]['A'] == "" || $Rows[$i]['B'] == "" || $Rows[$i]['D'] == ""|| $Rows[$i]['E'] == ""|| $Rows[$i]['F'] == ""){
                return 3;
            }
            $working_status = "";
            if ($Rows[$i]['E'] == "근무") {
                $working_status = '1';
            }
            if ($Rows[$i]['E'] == "퇴직") {
                $working_status = '2';
            }
            if ($Rows[$i]['E'] == "휴직") {
                $working_status = '3';
            }
            $disability_degree = "";
            if ($Rows[$i]['F'] == "중증") {
                $disability_degree = '1';
            }if ($Rows[$i]['F'] == "경증") {
                $disability_degree = '2';
            }
            $sdate= date("Y-m-d", strtotime($Rows[$i]['B']));
           // $edate= date("Y-m-d", strtotime($Rows[$i]['C']));
            if($Rows[$i]['C'] != ""){
                $edate= date("Y-m-d", strtotime($Rows[$i]['C']));
            }else{
                $edate= "";
            }

            $query = "
            insert into
                seller_company_worker
            set
                 status = '5'
                ,company_name = '".$seller_data["company_name"]."'
				,company_code = '".$seller_data["company_code"]."'
				,worker_name = '".$Rows[$i]['A']."'
				,worker_term_start = '".$sdate."'
				,worker_term_end = '".$edate."'
				,worker_birth = '".$Rows[$i]['D']."'
				,working_status = '".$working_status."'
				,disability_degree = '".$disability_degree."'
                ,register_date = '".date("Y-m-d H:i:s")."'
                ,register_id = '".$seller_uuid."'
        ";
            $idx = $this->wrdb->insert($query);
              }
          if($idx){
              return "1";
          }
          else {
              return null;
          }
        }
    }
