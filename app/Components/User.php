<?php

namespace app\Components;

use libs\View\Component;
use libs\View\HashDataTable;

class User extends Component{
    use HashDataTable;

    protected $view="user";
    protected $model=\app\Models\User::class;

    public $nombre="Julio";

    public function __construct(){

    }

}

?>