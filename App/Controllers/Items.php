<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

//class Items extends \Core\Controller{
class Items extends Authenticated{

    /*
    protected function before(){
        //ACTION FILTER
        //restricted EVERY ACTION in this class:
        $this->requireLogin();
    }
    */


    public function indexAction(){
        /*
        //page is restricted for logged-in users only:
        if(Auth::isLoggedIn() == false){
            //zapamiętuje wklepany URL, żeby wrócić do niego po zalogowaniu:
            Auth::rememberRequestedPage();
            
            $this->redirect('/login');
        }
        */

        //restricted ONLY for this ONE action:
        //$this->requireLogin();
        View::renderTemplate('Items/index.html');
    }
}


?>