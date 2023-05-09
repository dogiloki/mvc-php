<?php

namespace controllers;

use models\User;
use models\Group;
use libs\Secure;

class UserController{

    public function index($request){
        $user_found=null;
        if(isset($request->id)){
            $user_found=User::find($request->id);
        }
        $users=User::all();
        $groups=Group::all();
        return view("user.index",compact('users','groups','user_found'));
    }

    public function store($request){
        try{
            $request->password=Secure::encodePassword($request->password);
            $user=new User();
            $user->name=$request->name;
            $user->email=$request->email;
            $user->password=$request->password;
            $user->group=$request->id_group;
            $user->save();
            return back();
        }catch(\Exception $ex){
            echo $ex->getMessage();
        }
    }

    public function update($request){
        $user=User::find($request->id);
        if($user!=null){
            $user->name=$request->name;
            $user->email=$request->email;
            $user->password=$request->password;
            $user->group=$request->id_group;
            $user->save();
        }
        return redirect(route('user.index'));
    }

    public function delete($request){
        User::find($request->id)->delete();
        return back();
    }

}

?>