<?php

namespace App\Controllers;

use \Core\View;
use App\Controllers\Authenticated;
use App\Models\Earning;
use App\Flash;

class Income extends Authenticated{
    
    public $incomeCategories = [];

    public function newAction(){
      
        //$incomeCategories = [];
        $this->incomeCategories = Earning::getDefaultIncomeCategories();
        View::renderTemplate('Income/new.html', [
            'categories' => $this->incomeCategories,
            'presentDate' => $this->getPresentDate()
        ]);
    }

    //zapisanie danych z formularza income.html do bazy danych
    public function createAction(){

        $income = new Earning($_POST);
     
        $this->incomeCategories = Earning::getDefaultIncomeCategories();

        if(! Earning::checkIfUserHasDefaultCategories()){
            $income->saveToAssignedCategories($this->incomeCategories);
        } 

        if($income->saveToIncomes()){
            Flash::addMessages('Superb!', 'success');
            $this->redirect('/income/success');
        } else {
            Flash::addMessages('Sorry, try again.', 'info');
            View::renderTemplate('Income/new.html', [
                'categories' => $this->incomeCategories,
                'presentDate' => $this->getPresentDate()
            ]);
        }

    }

    public function successAction(){
        View::renderTemplate('income/success.html');
    }

    protected function getPresentDate(){
        return date("Y-m-d"); 
    }

}

?>