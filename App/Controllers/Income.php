<?php

namespace App\Controllers;

use \Core\View;
use App\Controllers\Authenticated;
use App\Models\Earning;

class Income extends Authenticated{
    
    public $incomeCategories = [];

    public function newAction(){
      
        //$incomeCategories = [];
        $this->incomeCategories = Earning::getDefaultIncomeCategories();
        var_dump($this->incomeCategories);

        View::renderTemplate('Income/new.html', [
            'categories' => $this->incomeCategories
        ]);
    }

    //zapisanie danych z formularza income.html do bazy danych
    public function createAction(){

        $income = new Earning($_POST);

        echo '<pre>';
        var_dump($income);
        var_dump($_SESSION);
        echo 'KATEGORIE DEFAULT: ';
        $this->incomeCategories = Earning::getDefaultIncomeCategories();
        var_dump($this->incomeCategories);

        $income->saveToAssignedCategories($this->incomeCategories);
        //$this->redirect('/income/success');

        //jeszcze if i templatka jezeli save się nie powiodł

    }

    public function successAction(){
        View::renderTemplate('income/success.html');
    }


}

?>