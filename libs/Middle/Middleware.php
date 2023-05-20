<?php

namespace libs\Middle;

abstract class Middleware{

    public function handle($request){}

    public function redirectTo($request){}

    public function terminate($request){}

}

?>