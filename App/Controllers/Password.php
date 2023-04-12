<?php

namespace App\Controllers;

use \Core\Controller;
use \Core\View;
use App\Models\User;

class Password extends Controller{

    public function forgotAction(){
        View::renderTemplate('Password/forgot.html');
    }

    public function requestResetAction(){
        User::sendPasswordReset($_POST['email']);
        View::renderTemplate('Password/reset_requested.html');

    }

    public function resetAction(){
        //mam specjalnego routa ustawionego w index.php
        //więc korzystam z tabeli route_params[]
        $token = $this->route_params['token'];
        //echo 'token = '.$token.'<br>';
     
        /*
        $user = User::findByPasswordReset($token);

        if($user){
            View::renderTemplate('Password/reset.html', [
                //poprzez formularz przekażę schowany token do resetPasswordAction()
                'token' => $token
            ]);

        } else {
            echo 'password reset token invalid';
        }
        */


        $user = $this->getUserOrExit($token);
        View::renderTemplate('Password/reset.html', [
            //poprzez formularz przekażę schowany token do resetPasswordAction()
            'token' => $token
        ]);

    }

    public function resetPasswordAction(){

        $token = $_POST['token'];

        /*
        $user = User::findByPasswordReset($token);

        if($user){
           
            echo "reset user's password here";

        } else {
            echo 'password reset token invalid';
        }
        */

        $user = $this->getUserOrExit($token);

        //wrzucam nowe hasło z resetu w walidację:
        if($user->resetPassword($_POST['password'])){
            View::renderTemplate('Password/reset_success.html');
        } else {
            View::renderTemplate('Password/reset.html', [
                'token' => $token,
                'user' => $user
            ]);
        }
    }

    protected function getUserOrExit($token){
        $user = User::findByPasswordReset($token);
        if($user){
           
            return $user;

        } else {
            View::renderTemplate('Password/token_expired.html');
            exit; 
        }
    }
}

    


?>