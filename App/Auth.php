<?php

namespace App;

use \App\Models\User;
use \App\Models\RememberedLogin;

class Auth{

    public static function login($user, $remember_me){
        //nadaję nowe ID sesji
        session_regenerate_id(true);
        //ID zalogowanego usera robię globalnym bo się przyda
        $_SESSION['user_id'] = $user->id;

        //jeżeli przeglądarka ma zapamiętać usera
        if($remember_me){
            //jeżeli wygenerowałem nowy token 
            //i zapisałem go w DB remembered_logins
            //to ustawiam ciasteczko:
            if($user->rememberLogin()){
                //ustawiam ciasteczko
                setcookie(
                    //nazwa ciasteczka
                    'remember_me', 
                    //token zapamiętany w DB remembered_logins
                    $user->remember_token, 
                    //czas wygaśnięcia tokena w cookie
                    $user->expiry_timestamp,
                    '/' //dzieki temu ukośnikowi cookie 
                        //jest dostępne w całej domenie
                );
            }
        }

    }

    public static function logout(){
        //https://www.php.net/manual/en/function.session-destroy.php
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], 
                $params["domain"],
                $params["secure"], 
                $params["httponly"]
            );
        }
        session_destroy();
        //usuń ciasteczko z przeglądarki 
        //oraz z DB remembered_logins:
        static::forgetLogin();
    }
    /*
    public static function isLoggedIn(){
        return isset($_SESSION['user_id']);
    }
    */

    public static function rememberRequestedPage(){
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    }

    public static function getReturnPage(){
        //zwraca zapamiętany URL lub home-page jeżeli takiego URL nie ma:
        return $_SESSION['return_to'] ?? '/';
    }

    public static function getUser(){
        if(isset($_SESSION['user_id'])){
            return User::findByID($_SESSION['user_id']);
        } else{
            //czyli jak zalogować usera z zapamiętanego cookie
            return static::loginFromRememberCookie();
        }
    }

    protected static function loginFromRememberCookie(){
        //sprawdzam czy cookie istnieje
        //pobieram wartość tokena z cookie i przypisuję do zmiennej
        $cookie = $_COOKIE['remember_me'] ?? false;

        if($cookie){
            //sprawdzam czy jest taki token wśród zapamiętanych
            $remembered_login = RememberedLogin::findByToken($cookie);

            if($remembered_login && ! $remembered_login->hasExpired()){
                //do zalogowania niezbędny jest obiekt user
                //ktorego ID znalazłem dzięki DB remembered_login
                $user = $remembered_login->getUser();
                static::login($user, false);
                return $user;
            }
        }
    }

    protected static function forgetLogin(){
        $cookie = $_COOKIE['remember_me'] ?? false;

        if($cookie){
            $remembered_login = RememberedLogin::findByToken($cookie);

            //usuwam cookie z DB remembered_logins
            if($remembered_login){
                $remembered_login->delete();
            }

            //żeby usunąc cookie z przeglądarki należy ustawić
            //jego czas na przeszły
            setcookie(
                'remember_me',
                '',
                time()-3600
            );
        }
    }
}



?>