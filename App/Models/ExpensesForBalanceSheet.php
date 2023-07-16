<?php

namespace App\Models;

use App\Models\Expenditure;
use App\Controllers\TimeAndDate;


class ExpensesForBalanceSheet extends \Core\Model{

    private $dateStart;
    private $dateEnd;
    private $expenseBalanceSheet;

    public function __construct(){
        $dateStart = new TimeAndDate();
        $this->dateStart = $dateStart->getStartDate();

        $dateEnd = new TimeAndDate();
        $this->dateEnd = $dateEnd->getEndDate();
        
        $expenseBalanceSheet = [];
    }

    private function makeSumOfEachExpense(){
        $expenses = new Expenditure();
        $sumOfEachExpense = $expenses->getExpenseResult($this->dateStart, $this->dateEnd);
        return $sumOfEachExpense;
    }

    private function makeAllExpenseCategoryNames(){
        $expenses = new Expenditure();
        $allExpenseCategoryNames = $expenses->getExpenseCategoryNames();
        return $allExpenseCategoryNames;
    }

    private function getNamesOfExpenses(){

        $namesOfExpenses;

        $sumOfExpenses = $this->makeSumOfEachExpense();
        $allExpenseCategoryNames = $this->makeAllExpenseCategoryNames();
                
        for($i = 0; $i<count($sumOfExpenses); $i++){
            for($n = 0; $n<count($allExpenseCategoryNames); $n++){ 
                if($sumOfExpenses[$i]['exp_cat_assigned_user_id'] == $allExpenseCategoryNames[$n]['id']){
                    $namesOfExpenses[$i] = $allExpenseCategoryNames[$n]['name'];
                    break;
                } 
            }
        }
        return $namesOfExpenses;
    }

    private function getAmountOfExpenses(){
       
        $amountOfExpenses;

        $sumOfExpenses = $this->makeSumOfEachExpense();
        $allExpenseCategoryNames = $this->makeAllExpenseCategoryNames();
               
        for($i = 0; $i<count($sumOfExpenses); $i++){
            for($n = 0; $n<count($allExpenseCategoryNames); $n++){ 
                if($sumOfExpenses[$i]['exp_cat_assigned_user_id'] == $allExpenseCategoryNames[$n]['id']){
                    $amountOfExpenses[$i] = $sumOfExpenses[$i]['amountOfExpensesByCategoryAndPeriodOfTime'];
                    break;
                } 
            }
        }
        return $amountOfExpenses;
    }


    private function countNumberOfItems($names){ 

        $i = NULL;
        $numberOfItems = [];

        for($i = 0; $i<count($names); $i++){
            $numberOfItems[$i] = $i+1;
        }

        return  $numberOfItems;
    }

    private function createExpensesTable($numbers, $names, $amounts){

        $balance = [];
        $x = 0;
        $y = 0;

        while($y < count($numbers)){
      
             $balance[$x] = $numbers[$y];
             $balance[$x+1] = $names[$y];
             $balance[$x+2] = $amounts[$y];
             $y++;
             $x = $x + 3;
        }          

        return  $balance;
    }

    public function makeExpensesBalanceSheet(){
        $names;
        $numbers;
        $amounts;

        if(! empty($this->makeSumOfEachExpense())){
            
            $names = $this->getNamesOfExpenses();
            $amounts = $this->getAmountOfExpenses();
            $numbers = $this->countNumberOfItems($names);
            $this->expenseBalanceSheet = $this->createExpensesTable($numbers, $names, $amounts);
            return  $this->expenseBalanceSheet;

        }
        return ["-","none","-"];
    }

    public function sumUpExpenses(){
        $amounts = $this->getAmountOfExpenses();
        $sumUp = [];
        $sumUp = array_sum($amounts);
        return $sumUp;
    }

}

?>