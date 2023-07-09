<?php

namespace App\Controllers;

use \Core\View;
use App\Controllers\Authenticated;
use App\Models\Expenditure;
use App\Flash;
use App\Controllers\TimeAndDate;


class Expense extends Authenticated{

    public $expenseCategories = [];
    public $paymentCategories = [];

    public function newAction(){
      
        $this->expenseCategories = Expenditure::getDefaultExpenseCategories();
        $this->paymentCategories = Expenditure::getDefaultPaymentCategories();

        View::renderTemplate('Expense/new.html', [
            'categories' => $this->expenseCategories,
            'presentDate' => TimeAndDate::getPresentDate(),
            'payments' => $this->paymentCategories
        ]);
    }

    public function createAction(){

        echo '<pre>';
        var_dump($_POST).'<br>';
        echo 'user ID = '.$_SESSION['user_id'];

        $expense = new Expenditure($_POST);
     
        $this->saveCategoriesAssignedToUser($expense);

        if($expense->saveToExpenses()){
            Flash::addMessages('Superb!', 'success');
            $this->redirect('/expense/success');
        } else {
            Flash::addMessages('Sorry, try again.', 'info');
            View::renderTemplate('Expense/new.html', [
                'categories' => $this->expenseCategories,
                'presentDate' => TimeAndDate::getPresentDate(),
                'payments' => $this->paymentCategories
            ]);
        }
    }

    protected function saveCategoriesAssignedToUser($expense){

        $this->expenseCategories = Expenditure::getDefaultExpenseCategories();
        $this->paymentCategories = Expenditure::getDefaultPaymentCategories();

        if(! Expenditure::checkIfUserHasDefaultExpenseCategories()){
            $expense->saveExpensesToAssignedCategories($this->expenseCategories);
        } 
        if(! Expenditure::checkIfUserHasDefaultPaymentCategories()){
            $expense->savePaymentsToAssignedCategories($this->paymentCategories);
        } 
    }
    
    public function successAction(){
        View::renderTemplate('expense/success.html');
    }
}

?>