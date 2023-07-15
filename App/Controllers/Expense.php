<?php

namespace App\Controllers;

use \Core\View;
use App\Controllers\Authenticated;
use App\Models\Expenditure;
use App\Flash;
use App\TimeAndDate;


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

        $expense = new Expenditure($_POST);
     
        $this->saveCategoriesAssignedToUser($expense);

        if($expense->saveToExpenses() === true){
            Flash::addMessages('Superb!', 'success');
            $this->redirect('/Expense/success');
        } else {
            $errorMessage = implode(" ", $expense->saveToExpenses());
            Flash::addMessages($errorMessage, 'warning');
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
        View::renderTemplate('Expense/success.html');
    }
}

?>