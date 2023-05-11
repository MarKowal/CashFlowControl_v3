<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Earning;
use App\Models\Expenditure;
use DateTime; //używam wbudowanej klasy PHP DateTime()


class BalanceSheet extends Authenticated{
    
    public $timePeriod;
    protected $selectedStartDate;
    protected $selectedEndDate;
    public $selectedStartDateString;
    public $selectedEndDateString;

    public function newAction(){
        View::renderTemplate('BalanceSheet/new.html');
        
        $this->timePeriod = $_POST['timePeriod'] ?? NULL;
       
        if($this->timePeriod != NULL){
            $this->transferTimePeriodIntoDate();
            echo  $this->selectedStartDateString.'<br>';
            echo $this->selectedEndDateString;
        }
    }

    protected function transferTimePeriodIntoDate(){

        $this->selectedStartDate = new DateTime(); 
        $this->selectedEndDate = new DateTime(); 
        $this->selectedEndDate->modify('now');

        if($this->timePeriod == 'presentMonth'){

            $this->selectedStartDate->modify('first day of this month');

        } elseif($this->timePeriod == 'previousMonth') {

            $this->selectedStartDate->modify('first day of last month');
            $this->selectedEndDate->modify('last day of last month');

        } elseif($this->timePeriod == 'presentYear'){

            $this->selectedStartDate->modify('1 January this year');
        }

        $this->selectedStartDateString = $this->selectedStartDate->format('Y-m-d');
        $this->selectedEndDateString = $this->selectedEndDate->format('Y-m-d');
    }
    
}

?>