<?php

namespace controllers;

class HomeController{

    public function index($request){
        return view('home.index');
    }

}

?>