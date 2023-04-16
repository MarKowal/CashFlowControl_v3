<?php

namespace App\Controllers;

use \Core\View;
use App\Controllers\Authenticated;
use App\Models\Earning;

class Income extends Authenticated{

    public function newAction(){
       // $incomeCategories = [];

        /*foreach(Earning::getDefaultIncomeCategories() as category){
           $incomeCategories[] => category;
        }*/
       // $incomeCategories = Earning::getDefaultIncomeCategories();
        //$incomeCategories = Earning::getDefaultIncomeCategories();
        //var_dump($_SESSION);
        //var_dump($_POST);

        echo "<pre>";
        //var_dump($incomeCategories);
        //echo "<br><br><br>";
       // foreach($incomeCategories as $value){
       //     print_r($value); 
       // }
       // print_r(Earning::getDefaultIncomeCategories());

        $incomeCategories = [];
        $incomeCategories = Earning::getDefaultIncomeCategories();

        var_dump($incomeCategories);

        echo '<br>'.$incomeCategories[1];

        View::renderTemplate('Income/new.html');
    }

    //metoda ktora pobierze kategorie z DB jako tabelę i prześle je do html do wyświetlenia
    //public czy protected?
    /*public function getDefaultIncomeCategories(){
        
        //$earning = new Earning();
        $incomeCategories = [];

        $incomeCategories[] = Earning::$earning->getDefaultIncomeCategories();


    }*/


    //zapisanie danych z formularza income.html do bazy danych
    public function createAction(){

    }
}

?>