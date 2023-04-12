<?php

namespace App\Controllers;

//Po tej klasie dziedziczą kontrolery które wymagają najpierw zalogowania

abstract class Authenticated extends \Core\Controller{
    protected function before(){
        $this->requireLogin();
    }

}


?>