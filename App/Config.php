<?php

namespace App;

class Config{
    /*
    const DB_HOST = 'localhost';
    const DB_NAME = 'cashflowcontrol_v3';
    const DB_USER = 'root';
    const DB_PASSWORD = '';
    */
    const DB_HOST = 'budget.marcin-kowalski.profesjonalnyprogramista.pl.mysql.dhosting.pl';
    const DB_NAME = 'tha3im_budgetma';
    const DB_USER = 'teiqu3_budgetma';
    const DB_PASSWORD = 'uqu7dooSheef';
    

    const SHOW_ERRORS = true;
    //false - do not show any error details on the screen, all is saved in logs/txt file
    //true - show all error details on the screen, nothing is saved in logs/txt file

    //https://randomkeygen.com/ 
    const SECRET_KEY = '.ZFQ:pN~c9vuXO0ak6hqpzT;Y=>&)G';

    const GMAIL_USERNAME = 'marcin.kowalski.programista@gmail.com';
    const GMAIL_PASSWORD = 'zwllkyfryoozjtnw';
}

?>