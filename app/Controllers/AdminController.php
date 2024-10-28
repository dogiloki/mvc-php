<?php

namespace app\Controllers;

use app\Controllers\Controller;
use libs\HTTP\Request;
use app\Models\User;

class AdminController extends Controller{
    
    public function module(Request $request){
        view('admin.home',[
            'module'=>$request->module
        ]);
    }

}

?>