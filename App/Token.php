<?php

namespace App;

use \App\Config;

class Token {

    protected $token;

    //od razu przy powstaniu obiektu Token generuje nowy token
    public function __construct($token_value = null){

        if($token_value != null){
            $this->token = $token_value;
        } else {
            //16 bajtów = 128 bitów = 32 hex znaki
            $this->token = bin2hex(random_bytes(16));
        }
    }

    public function getValue(){
        return $this->token;
    }

    //zahashowanie tokena przed wrzuceniem do DB
    public function getHash(){
        //ten "secret" jest w pliku Config dla bezpieczeństwa przechowywany
        return hash_hmac('sha256', $this->token, Config::SECRET_KEY);
    }
}


?>