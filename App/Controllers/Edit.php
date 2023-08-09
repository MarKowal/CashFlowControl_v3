<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Earning;
use App\Models\Expenditure;
use App\Flash;

class Edit extends Authenticated{

    private $incomesCategories;
    private $expensesCategories;
    private $paymentsCategories;

    public function __construct(){
        $this->incomesCategories = new Earning();
        $this->expensesCategories = new Expenditure();
        $this->paymentsCategories = new Expenditure();
   }

    public function newAction(){

        View::renderTemplate('Edit/new.html', [
            'incomesCategories' => $this->incomesCategories->getIncomeCategoryNameAssignedToUser(),
            'expensesCategories' => $this->expensesCategories->getExpenseCategoryNameAssignedToUser(),
            'paymentsCategories' => $this->paymentsCategories->getPaymentCategoryNameAssignedToUser()
        ]);
    }

    public function addIncomeAction(){
       
        if($this->incomesCategories->addNewIncomeCategory($_POST["add-income"]) === true){
            Flash::addMessages('New income category saved in data base.', 'success');
            $this->redirect('/Edit/new');
        } else {
            Flash::addMessages($this->incomesCategories->errorMessage, 'warning');
            $this->redirect('/Edit/new');
        }
    }

    public function renameAction(){
    
        $typeOfRename = implode(array_keys($_POST));
        $oldName = $_POST['rename-income'];
        
        if($typeOfRename == "rename-income"){
            View::renderTemplate('Edit/rename.html', [
                'renameType' => 'renameIncome',
                'oldName' => $oldName
            ]);
        } 
        elseif($typeOfRename == "rename-expense"){
            View::renderTemplate('Edit/rename.html', [
                'renameType' => 'renameExpense',
                'old-name' => $oldName
            ]);
        } 
        elseif($typeOfRename == "rename-payment"){
            View::renderTemplate('Edit/rename.html', [
                'renameType' => 'renamePayment',
                'old-name' => $oldName
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

    public function deleteIncomeAction(){
        
        //może jakiś modal we View czy na pewno chce go usunąć?
        if($this->incomesCategories->deleteIncomeCategory($_POST['delete-income']) === true){
            Flash::addMessages('Income category has been deleted.', 'success');
            $this->redirect('/Edit/new');
        } else {
            Flash::addMessages('Income category cannot be deleted', 'warning');
            $this->redirect('/Edit/new');
        }
    }

    public function renameExpenseAction(){
        echo "zmiana nazwy dla Expense:<br>";
        var_dump($_POST);

    }

    public function deleteExpenseAction(){
        echo "delete Expense<br>";
        var_dump($_POST);
        echo "<br>";
    }

}