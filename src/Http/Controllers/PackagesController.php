<?php

namespace Leeuwenkasteel\Setup\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PackagesController extends Controller
{  
	
	public function index(){
		return view('setup::packages.index');
	}
	
	
	
}