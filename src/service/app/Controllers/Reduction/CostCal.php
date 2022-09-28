<?php
	
	namespace App\Controllers\Reduction;
	
	use App\Controllers\BaseController;
	
	class CostCal extends BaseController
	{
		
		public function index()
		{
			echo view('Reduction/CostCal.html');
		}
	}