<?php

namespace controllers;

use models\User;

class UserController{

    public function index(){
        $users=User::all();
        return view("user.index",compact('users'));
    }

    public function store($request){
        $user=User::create($request->post());
        $user->save();
    }

}

?>