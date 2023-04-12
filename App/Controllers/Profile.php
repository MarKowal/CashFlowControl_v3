<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;

//class Profile extends \Core\Controller{
//żeby user był już zalogowany to trzeba dziedziczyć po Athenticated
class Profile extends Authenticated{

    protected function before(){
        parent::before();
        $this->user = Auth::getUser();
    }

    public function showAction(){
        View::renderTemplate('Profile/show.html', [
            //pobieram dane usera z Auth i przekazuję do view:
            //'user' => Auth::getUser()
            'user' => $this->user
        ]);
    }

    public function editAction(){
        View::renderTemplate('Profile/edit.html', [
            'user' => $this->user
        ]);
    }

    public function updateAction(){
        //stawiam obiekt User żeby wykonywać jego metody
        //$user = Auth::getUser();

        if($this->user->updateProfile($_POST)){
            Flash::addMessages('Changes saved');
            $this->redirect('/profile/show');
            
        } else {
            View::renderTemplate('Profile/edit.html', [
                'user' => $this->user
            ]);
        }
    }

}


?>