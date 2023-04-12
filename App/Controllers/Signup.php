<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

class Signup extends \Core\Controller{

    public function newAction(){
        View::renderTemplate('Signup/new.html');
    }

    //jak zapisać dane z formularza do bazy danych
    public function createAction(){
        //tworzę nowego usera i przekauję mu wszystkie dane
        //skoro przekazuję dane podczas tworzenia obiektu
        //to są one odbierane przez __construct($data) w User
        $user = new User($_POST);
        if ($user->save()){
            //View::renderTemplate('Signup/success.html');

            //przeniesienie na stronę sukces, żeby drugi raz nie przesyłać danych z formularza
            //header('Location: http://'.$_SERVER['HTTP_HOST'].'/signup/success', true, 303);
            //exit;

            $user->sendActivationEmail();

            $this->redirect('/signup/success');
        } else {
            //przesyłam spowrotem obiekt $user żeby wyświetlać error messages
            View::renderTemplate('Signup/new.html', [
                'user' => $user
            ]);
        }
    }

    public function successAction(){
        View::renderTemplate('Signup/success.html');
    }

    public function activateAction(){
        //token z maila siedzi w tabeli route_params[]
        User::activate($this->route_params['token']);
        $this->redirect('/signup/activated');
    }

    public function activatedAction(){
        View::renderTemplate('Signup/activated.html');

    }
}