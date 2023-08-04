<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Earning;
use App\Models\Expenditure;

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

        //trzeba przenieść poniższą logikę do poszczególnych modeli

        echo "add income<br>";
        var_dump($_POST);
        echo "<br>";
       // $key = implode((array_keys($_POST)));
     //   echo "klucz = ".$key;
     //   echo "<br>";
       // $value = implode((array_values($_POST)));
      //  echo "wartość = ".$value;
      //  echo "<br>";
      //  echo "ID usera = ".$_SESSION['user_id'];
      

      // WALIDACJĘ TRZEBA ZROBIĆ TEGO CO USER WPISUJE
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
        echo "zmiana nazwy dla Income.<br>";
        var_dump($_POST);

    }

    public function deleteIncomeAction(){
        echo "delete income<br>";
        var_dump($_POST);
        echo "<br>";
    }

}