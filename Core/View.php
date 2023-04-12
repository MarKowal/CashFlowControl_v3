<?php

namespace Core;

class View{

    public static function render($view, $args = []){

        extract($args, EXTR_SKIP);
        
        $file = "../App/Views/$view"; 
        
        if(is_readable($file)){
            require $file;
        } else{
            throw new \Exception("$file not found");
        }
    }

    /*public static function renderTemplate(string $template, array $args = []){
        static $twig = null;

        if($twig === null){
            $loader = new \Twig\Loader\FilesystemLoader('../App/Views');
            $twig = new \Twig\Environment($loader);
            //add Session to Twig:
            //$twig->addGlobal('session', $_SESSION);
            //dodawanie funkcji isLoggedIn() do Twiga:
            //$twig->addGlobal('is_logged_in', \App\Auth::isLoggedIn());
            
            //jeśli chcę żeby coś było dostępne pomiędzy view-templatkami 
            //to wrzucam do Twiga globalnie:
            $twig->addGlobal('current_user', \App\Auth::getUser());
            $twig->addGlobal('flash_messages', \App\Flash::getMessages());


        }

        echo $twig->render($template, $args);
    }*/

    //pod wysyłanie emaila password-reset trzeba poniższe zmienić:
    public static function getTemplate(string $template, array $args = []){
        static $twig = null;

        if($twig === null){
            $loader = new \Twig\Loader\FilesystemLoader('../App/Views');
            $twig = new \Twig\Environment($loader);
            //add Session to Twig:
            //$twig->addGlobal('session', $_SESSION);
            //dodawanie funkcji isLoggedIn() do Twiga:
            //$twig->addGlobal('is_logged_in', \App\Auth::isLoggedIn());
            
            //jeśli chcę żeby coś było dostępne pomiędzy view-templatkami 
            //to wrzucam do Twiga globalnie:
            $twig->addGlobal('current_user', \App\Auth::getUser());
            $twig->addGlobal('flash_messages', \App\Flash::getMessages());


        }
        //zwraca zawartość templatki
        return $twig->render($template, $args);
    }

    public static function renderTemplate(string $template, array $args = []){
        //wyświetla templatkę
        echo static::getTemplate($template, $args);
        
    }

}


?>