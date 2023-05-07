<?php

namespace controllers;

use models\Group;

class GroupController{

    public function index($request){
        $group_found=null;
        if(isset($request->id)){
            $group_found=Group::find($request->id);
        }
        $groups=Group::all();
        return view('group.index',compact('groups','group_found'));
    }

    public function store($request){
        $group=new Group();
        $group->name=$request->name;
        $group->description=$request->description;
        $group->save();
        return back();
    }

    public function update($request){
        $group=Group::find($request->id);
        $group->name=$request->name;
        $group->description=$request->description;
        $group->save();
        return redirect(route('group.index'));
    }

    public function delete($request){
        $group=Group::find($request->id);
        if($group!=null){
            $group->delete();
        }
        return back();
    }

}

?>