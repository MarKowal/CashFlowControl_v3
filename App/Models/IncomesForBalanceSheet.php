<?php

namespace App\Models;

use App\Models\Earning;
use App\Controllers\TimeAndDate;


class IncomesForBalanceSheet extends \Core\Model{

    private $dateStart;
    private $dateEnd;
    private $incomesBalanceSheet;

    public function __construct(){
        $dateStart = new TimeAndDate();
        $this->dateStart = $dateStart->getStartDate();

        $dateEnd = new TimeAndDate();
        $this->dateEnd = $dateEnd->getEndDate();
        
        $expenseBalanceSheet = [];
    }

    private function makeSumOfEachIncome(){
        $incomes = new Earning();
        $sumOfEachIncome = $incomes->getIncomesResult($this->dateStart, $this->dateEnd);
        return $sumOfEachIncome;
    }

    private function makeAllIncomeCategoryNames(){
        $incomes = new Earning();
        $allIncomeCategoryNames = $incomes->getIncomeCategoryNames();
        return $allIncomeCategoryNames;
    }

    private function getNamesOfIncomes(){

        $namesOfIncomes;

        $sumOfIncomes = $this->makeSumOfEachIncome();
        $allIncomeCategoryNames = $this->makeAllIncomeCategoryNames();
                
        for($i = 0; $i<count($sumOfIncomes); $i++){
            for($n = 0; $n<count($allIncomeCategoryNames); $n++){ 
                if($sumOfIncomes[$i]['inc_cat_assigned_user_id'] == $allIncomeCategoryNames[$n]['id']){
                    $namesOfIncomes[$i] = $allIncomeCategoryNames[$n]['name'];
                    break;
                } 
            }
        }
        return $namesOfIncomes;
    }

    private function getAmountOfIncomes(){
        $amountOfIncomes = [];

        $sumOfIncomes = $this->makeSumOfEachIncome();
        $allIncomeCategoryNames = $this->makeAllIncomeCategoryNames();
               
        for($i = 0; $i<count($sumOfIncomes); $i++){
            for($n = 0; $n<count($allIncomeCategoryNames); $n++){ 
                if($sumOfIncomes[$i]['inc_cat_assigned_user_id'] == $allIncomeCategoryNames[$n]['id']){
                    $amountOfIncomes[$i] = $sumOfIncomes[$i]['amountOfIncomesByCategoryAndPeriodOfTime'];
                    break;
                } 
            }
        }
        return $amountOfIncomes;
    }


    private function countNumberOfItems($names){ 

        $i = NULL;
        $numberOfItems = [];

        for($i = 0; $i<count($names); $i++){
            $numberOfItems[$i] = $i+1;
        }

        return  $numberOfItems;
    }

    private function createIncomesTable($numbers, $names, $amounts){

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

    public function makeIncomesBalanceSheet(){
        $names;
        $numbers;
        $amounts;

        if(! empty($this->makeSumOfEachIncome())){
            
            $names = $this->getNamesOfIncomes();
            $amounts = $this->getAmountOfIncomes();
            $numbers = $this->countNumberOfItems($names);
            $this->incomesBalanceSheet = $this->createIncomesTable($numbers, $names, $amounts);
            return  $this->incomesBalanceSheet;

        } else {
            return ["-","none","-"];
        }
    }

    public function sumUpIncomes(){
        $amounts = $this->getAmountOfIncomes();
        $sumUp = [];
        $sumUp = array_sum($amounts);
        return $sumUp;
    }

}

?>