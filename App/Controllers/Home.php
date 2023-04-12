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
        /*
        tak nie muszę przekazywać Usera do view, 
        bo jest zrobiona w Twigu globalna zmienna current_user
        View::renderTemplate('Home/index.html', [
            'user' => Auth::getUser()
        ]);
        */

        View::renderTemplate('Home/index.html');
        /*
        Mail::send(
            'mr_kowalski@interia.pl', 
            'Test maila 3', 
            'To jest wiadomość testowa', 
            '<h1>To jest wiadomość testowa</h1><p>ą ę ł ś ć ż ź ń ó</p>'
        );
        */
    }
}

?>