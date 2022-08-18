<?PHP
namespace App\Models\dbClasses;
use App\Models\dbClasses\dbQueryClass;

class ro_conn_db extends dbQueryClass {

    public function __construct(){

		$db_host = "133.186.218.7";
		$db_name = "kfo";	
		$db_user = "kfoitlab";	
		$db_pass = "Kfoitlab1!43735002";	

        /*
		$db_host = "private.server.fs-engine.com";
		$db_name = "kfo";	
		$db_user = "kfo";	
		$db_pass = "9Pq2O}tpmXlgL%H";	
         */

        $this->connectDB( $db_host, $db_user, $db_pass, $db_name );
    }
}
