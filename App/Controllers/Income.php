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
       
        View::renderTemplate('Income/new.html', [
            'categories' => $this->incomeCategories
        ]);
    }

    //zapisanie danych z formularza income.html do bazy danych
    public function createAction(){

        $income = new Earning($_POST);
     
        $this->incomeCategories = Earning::getDefaultIncomeCategories();

        if(! Earning::checkIfUserHasDefaultCategories()){
            $income->saveToAssignedCategories($this->incomeCategories);
        } 

        //robocze:
        echo '<pre>';
        var_dump($income);
        echo '<br>IncomeCategoryIdAssignedToUser = ';
        var_dump($income->getIncomeCategoryIdAssignedToUser());



        $income->saveToIncomes();

        

        //$this->redirect('/income/success');

        //jeszcze if i templatka jezeli save się nie powiodł

    }

    public function successAction(){
        View::renderTemplate('income/success.html');
    }


}

?>