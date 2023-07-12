<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Token;
use \App\Mail;


class Home extends \Core\Controller{

    protected function before(){

    }

    protected function after(){

    }
    
    public function indexAction(){
      
        View::renderTemplate('Home/index.html');
      
    }
}

?>