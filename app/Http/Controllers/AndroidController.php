<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AndroidController extends Controller
{
    //

        public function authenticate(Request $request)
    {

        if (Auth::attempt(['email' => $request->username, 'password' => $request->password])) {
            // Authentication passed...
            $response["value"] = 1;
       		$response["message"] = "Login Berhasil";
       		return  json_encode($response);
        }
        else {
        	$response["value"] = 2;
       		$response["message"] = "Login Gagal";
       		return  json_encode($response);
        }
    }


    
}
