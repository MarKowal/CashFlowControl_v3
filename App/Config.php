<?php

namespace App;

class Config{

    const DB_HOST = 'localhost';
    const DB_NAME = 'mvc_registration_login';
    const DB_USER = 'mvc_user1';
    const DB_PASSWORD = 'qwerty1234';

    const SHOW_ERRORS = true;
    //false - do not show any error details on the screen, all is saved in logs/txt file
    //true - show all error details on the screen, nothing is saved in logs/txt file

    //https://randomkeygen.com/ 
    const SECRET_KEY = '.ZFQ:pN~c9vuXO0ak6hqpzT;Y=>&)G';

    const GMAIL_USERNAME = 'marcin.kowalski.programista@gmail.com';
    const GMAIL_PASSWORD = 'zwllkyfryoozjtnw';
}

?>