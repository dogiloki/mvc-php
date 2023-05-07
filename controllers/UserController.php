<?php

namespace controllers;

use models\User;
use models\Group;
use libs\Secure;

class UserController{

    public function index(){
        $users=User::all();
        $groups=Group::all();
        return view("user.index",compact('users','groups'));
    }

    public function store($request){
        $request->post('password',Secure::encodePassword($request->post('password')));
        $user=User::create($request->post());
        $user->save();
        return back();
    }

}

?>