<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Earning;
use App\Models\Expenditure;
use App\Flash;

class Edit extends Authenticated{

    private $incomesCategories;
    private $expensesCategories;

    public function __construct(){
        $this->incomesCategories = new Earning();
        $this->expensesCategories = new Expenditure();
   }

    public function newAction(){

        View::renderTemplate('Edit/new.html', [
            'incomesCategories' => Earning::getIncomeCategoryNameAssignedToUser(),
            'expensesCategories' => Expenditure::getExpenseCategoryNameAssignedToUser(),
            'paymentsCategories' => Expenditure::getPaymentCategoryNameAssignedToUser()
        ]);
    }

    public function addIncomeAction(){
       
        if($this->incomesCategories->addNewIncomeCategory($_POST["add-income"]) === true){
            Flash::addMessages('New income category has been saved in data base.', 'success');
            $this->redirect('/Edit/new');
        } else {
            Flash::addMessages($this->incomesCategories->errorMessage, 'warning');
            $this->redirect('/Edit/new');
        }
    }

    public function addExpenseAction(){
       
        if($this->expensesCategories->addNewExpenseCategory($_POST["add-expense"]) === true){
            Flash::addMessages('New expense category has been saved in data base.', 'success');
            $this->redirect('/Edit/new');
        } else {
            Flash::addMessages($this->expensesCategories->errorMessage, 'warning');
            $this->redirect('/Edit/new');
        }
    }

    public function addPaymentAction(){
       
        if($this->expensesCategories->addNewPaymentCategory($_POST["add-payment"]) === true){
            Flash::addMessages('New payment category has been saved in data base.', 'success');
            $this->redirect('/Edit/new');
        } else {
            Flash::addMessages($this->expensesCategories->errorMessage, 'warning');
            $this->redirect('/Edit/new');
        }
    }

    public function renameAction(){
    
        $typeOfRename = implode(array_keys($_POST));
        $oldName = implode(array_values($_POST));

        if($typeOfRename == "rename-income"){
            View::renderTemplate('Edit/rename.html', [
                'renameType' => 'renameIncome',
                'oldName' => $oldName
            ]);
        } 
        elseif($typeOfRename == "rename-expense"){
            View::renderTemplate('Edit/rename.html', [
                'renameType' => 'renameExpense',
                'oldName' => $oldName
            ]);
        } 
        elseif($typeOfRename == "rename-payment"){
            View::renderTemplate('Edit/rename.html', [
                'renameType' => 'renamePayment',
                'oldName' => $oldName
            ]);
        }
    }

    public function renameIncomeAction(){
      
        if($this->incomesCategories->renameIncomeCategory($_POST["rename-old"], $_POST["rename-new"]) === true){
            Flash::addMessages('Renamed income category saved in data base.', 'success');
            $this->redirect('/Edit/new');
        } else {
            Flash::addMessages($this->incomesCategories->errorMessage, 'warning');
            $this->redirect('/Edit/new');
        }
    }

    public function renameExpenseAction(){

        if($this->expensesCategories->renameExpenseCategory($_POST["rename-old"], $_POST["rename-new"]) === true){
            Flash::addMessages('Renamed expense category saved in data base.', 'success');
            $this->redirect('/Edit/new');
        } else {
            Flash::addMessages($this->expensesCategories->errorMessage, 'warning');
            $this->redirect('/Edit/new');
        }
    }

    public function renamePaymentAction(){

        if($this->expensesCategories->renamePaymentCategory($_POST["rename-old"], $_POST["rename-new"]) === true){
            Flash::addMessages('Renamed payment category saved in data base.', 'success');
            $this->redirect('/Edit/new');
        } else {
            Flash::addMessages($this->expensesCategories->errorMessage, 'warning');
            $this->redirect('/Edit/new');
        }
    }

    public function deleteAction(){
    
        $typeOfDelete = implode(array_keys($_POST));
        
        if($typeOfDelete == "delete-income"){
            View::renderTemplate('Edit/delete.html', [
                'deleteType' => 'deleteIncome',
                'categoryToDelete' => $_POST['delete-income']
            ]);
        } 
        elseif($typeOfDelete == "delete-expense"){
            View::renderTemplate('Edit/delete.html', [
                'deleteType' => 'deleteExpense',
                'categoryToDelete' => $_POST['delete-expense']
            ]);
        } 
        elseif($typeOfDelete == "delete-payment"){
            View::renderTemplate('Edit/delete.html', [
                'deleteType' => 'deletePayment',
                'categoryToDelete' => $_POST['delete-payment']
            ]);
        }
    }

    public function deleteIncomeAction(){
        
        if($this->incomesCategories->deleteIncomeCategory($_POST['delete']) === true){
            Flash::addMessages('Income category has been deleted.', 'success');
            $this->redirect('/Edit/new');
        } else {
            Flash::addMessages('Income category cannot be deleted', 'warning');
            $this->redirect('/Edit/new');
        }
    }

    public function deleteExpenseAction(){

        if($this->expensesCategories->deleteExpenseCategory($_POST['delete']) === true){
            Flash::addMessages('Expense category has been deleted.', 'success');
            $this->redirect('/Edit/new');
        } else {
            Flash::addMessages('Expense category cannot be deleted', 'warning');
            $this->redirect('/Edit/new');
        }
    }

    public function deletePaymentAction(){
       

    }












}