<?php

namespace controllers;

use models\User;
use models\Group;
use libs\Secure;

class UserController{

    public function index($request){
        $user_found=null;
        if($request->id!=null){
            $user_found=User::find($request->id);
        }
        $users=User::all();
        $groups=Group::all();
        return view("user.index",compact('users','groups','user_found'));
    }

    public function store($request){
        $request->post('password',Secure::encodePassword($request->post('password')));
        $user=User::create($request->post());
        $user->save();
        return back();
    }

    public function delete($request){
        User::find($request->id)->delete();
        return back();
    }

}

?>