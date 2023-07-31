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

    public function groupAction(){

        //trzeba przenieść poniższą logikę do poszczególnych modeli


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




}