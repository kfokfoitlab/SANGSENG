<?php
	
	namespace App\Controllers;
	use App\Models\CommonModel as model;
	use App\Models\DatabaseModel;
	
	class Download extends BaseController
	{
		
		private $model;
		private $database_model;
		
		public function __construct()
		{ //{{{
			$this->model = new model;
			$this->database_model = new DatabaseModel;
		} //}}}
		
		public function downloadFileNew()
		{
			$this->model->downloadFileNew();
		}
	}
