<?php

namespace App\Controllers;

use \Core\View;
use App\Controllers\Authenticated;
use App\Models\Earning;

class Income extends Authenticated{

    public function newAction(){
      
        $incomeCategories = [];
        $incomeCategories = Earning::getDefaultIncomeCategories();

        View::renderTemplate('Income/new.html', [
            'categories' => $incomeCategories
        ]);
    }

    //zapisanie danych z formularza income.html do bazy danych
    public function createAction(){
        echo '<pre>';
        var_dump($_POST);
    }

    //wyświetla info że przychód został pomyślnie zapisany w DB
    public function successAction(){
        //View::renderTemplate('Signup/success.html');
    }
}

?>