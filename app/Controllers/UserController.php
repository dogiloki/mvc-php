<?php

namespace app\Controllers;

use app\Controllers\Controller;
use libs\HTTP\Request;
use libs\Validator\UserValidator;
use app\Models\User;

class UserController extends Controller{
    
    public function store(Request $request){
        try{
            $data=$request->only(User::class);
            UserValidator::store($data);
            User::create($data);
        }catch(Exception $ex){

        }
    }

    public function show(Request $request){
        return $this->setData(
            User::paginate($request->per_page??10,$request->page)
        )->response();
    }

}

?>