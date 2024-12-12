<?php

namespace app\Components;

use libs\View\Component;
use libs\View\HasDataTable;

class User extends Component{
    use HasDataTable;

    protected $view="user";
    protected $model=\app\Models\User::class;
    protected $middlewares=["auth"];

}

?>