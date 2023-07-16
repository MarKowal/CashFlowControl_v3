<?php

namespace App\Controllers;

use DateTime; 
use App\Flash;
use \Core\View;

class TimeAndDate extends Authenticated{

    private $timePeriod;

    public function __construct(){
        $this->setTheTimePeriod();
    }

    private function setTheTimePeriod() {
        if(isset($_POST['timePeriod'])) {
            $this->timePeriod = $_POST['timePeriod'];
        } else {
            $this->timePeriod = NULL;
        }
    }

    public static function getPresentDate(){
        return date("Y-m-d"); 
    }

    public function getStartDate(){

        $dateStart;

        if(isset($_POST['dateStartFromUser'])){
            if ($this->validateDatesFromUser()){
                $dateStart = $_POST['dateStartFromUser'];
            }
        } else {
            $dateStart = $this->indicateStartDate();
        }
        return $dateStart;
    }


    public function getEndDate(){

        $dateEnd;

        if(isset($_POST['dateEndFromUser'])){
            if ($this->validateDatesFromUser()){
                $dateEnd = $_POST['dateEndFromUser'];
            }
        } else {
            $dateEnd = $this->indicateEndDate();
        }
        return $dateEnd;
    }


    
    private function indicateStartDate(){
              
        $startDate;

        if($this->timePeriod != NULL){
            $startDate = $this->transferTimePeriodIntoStartDate();
            return $startDate;
        } else {
            return NULL;
        }
    }

    private function indicateEndDate(){
              
        $endtDate;

        if($this->timePeriod != NULL){
            $endtDate = $this->transferTimePeriodIntoEndDate();
            return $endtDate;
        } else {
            return NULL;
        }
    }

    private function transferTimePeriodIntoStartDate(){

        $startDate = new DateTime(); 
        $startDateString;

        if($this->timePeriod == 'presentMonth'){

            $startDate->modify('first day of this month');

        } elseif($this->timePeriod == 'previousMonth') {

            $startDate->modify('first day of last month');

        } elseif($this->timePeriod == 'presentYear'){

            $startDate->modify('1 January this year');

        } elseif($this->timePeriod == 'otherTime'){

            $this->redirect('/TimeAndDate/getTheOtherTimeChoosenByUser');
           
        }

        $startDateString = $this->transferDateTypeIntoString($startDate);

        return $startDateString;
    }

    private function transferTimePeriodIntoEndDate(){

        $endDate = new DateTime();
        $endDateString;

        if($this->timePeriod == 'presentMonth' || $this->timePeriod == 'presentYear'){

            $endDate->modify('now');

        } elseif($this->timePeriod == 'previousMonth') {

            $endDate->modify('last day of last month');

        } elseif($this->timePeriod == 'otherTime'){

            $this->redirect('/TimeAndDate/getTheOtherTimeChoosenByUser');
           
        }

        $endDateString = $this->transferDateTypeIntoString($endDate);

        return $endDateString;
    }

    private function transferDateTypeIntoString($date){

        $dateString = NULL;
        $dateString = $date->format('Y-m-d');

        return  $dateString;
    }

    public function getTheOtherTimeChoosenByUser(){
        View::renderTemplate('BalanceSheet/new.html', [
            'choosenTheOtherTime' => 1
        ]);
    }

    private function validateDatesFromUser(){
        if($_POST['dateStartFromUser'] < $_POST['dateEndFromUser']){
            return true;
        } else {
            Flash::addMessages('Start date cannot be later than End date. Try again.', 'warning');
            $this->redirect('/BalanceSheet/new');
        }
    }
}

?>