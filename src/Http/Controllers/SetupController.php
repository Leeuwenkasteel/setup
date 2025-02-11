<?php

namespace Leeuwenkasteel\Setup\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SetupController extends Controller
{  
	public function index(){
		return view('setup::package');
	}
}