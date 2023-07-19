<?php

namespace App\Models;

use PDO;

class Earning extends \Core\Model{

    
    private $amount;
    private $date;
    private $incomeCategory;
    private $incomeComment;
    private $id;
    private $user_id;
    private $name;
    public $errors; 

    public function __construct($data = []){
         foreach($data as $key => $value){
            $this->$key = $value;
         };
    }

    public static function getDefaultIncomeCategories(){

        $sql = 'SELECT name_income FROM incomes_category_default ORDER BY name_income ASC';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    private function saveToAssignedCategories(){

        $db = static::getDB();
        $sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name) 
                SELECT :user_id, name_income FROM incomes_category_default ORDER BY name_income ASC';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
    }

    private function checkIfUserHasDefaultCategories(){

        $sql = 'SELECT name FROM incomes_category_assigned_to_users WHERE user_id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        if($stmt->fetch(PDO::FETCH_COLUMN, 1)){
            return true;
        }
        return false;
    }

    private function getIncomeCategoryIdAssignedToUser(){

        $sql = 'SELECT id FROM incomes_category_assigned_to_users WHERE user_id = :id AND name = :incomeCategory';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':incomeCategory', $this->incomeCategory, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_COLUMN, 0);
    }


    public function saveToIncomes(){

        if($this->checkIfUserHasDefaultCategories() == false){
            $this->saveToAssignedCategories();
        } 

        $this->validate();

        if (empty($this->errors)){

            $sql = 'INSERT INTO incomes (user_id, inc_cat_assigned_user_id, amount, date_of_income, income_comment) 
                    VALUES (:user_id, :inc_cat_assigned_user_id, :amount, :date_of_income, :income_comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':inc_cat_assigned_user_id', $this->getIncomeCategoryIdAssignedToUser(), PDO::PARAM_INT);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_INT);
            $stmt->bindValue(':date_of_income', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':income_comment', $this->incomeComment, PDO::PARAM_STR);

            return $stmt->execute();

        } else {
            return $this->errors;
        }
    }

    private function validate(){
        
        $this->errors = [];

        if (empty($this->amount) || $this->amount == " "){
            $this->errors[] = 'Amount is required.';
        }

        if (empty($this->date) || $this->date == ''){
            $this->errors[] = 'Date is required.';
        }

        if (empty($this->incomeCategory) || $this->incomeCategory == ''){
            $this->errors[] = 'Income category is required.';
        }

        if ((int)$this->amount <= 0){
            $this->errors[] = 'Amount must be more than zero.';
        }

        if (! strtotime($this->date)){
            $this->errors[] = 'Date must be a date-type';
        }

    }

    public function getIncomesResult($startDate, $endDate){

        $user_id = $_SESSION['user_id'];

        $sql = "SELECT incomes.inc_cat_assigned_user_id, SUM(incomes.amount) 
                AS amountOfIncomesByCategoryAndPeriodOfTime FROM incomes, incomes_category_assigned_to_users 
                WHERE incomes.user_id = :user_id AND incomes.date_of_income BETWEEN :startDate AND :endDate 
                AND incomes.inc_cat_assigned_user_id = incomes_category_assigned_to_users.id 
                GROUP BY incomes.inc_cat_assigned_user_id ORDER BY amountOfIncomesByCategoryAndPeriodOfTime DESC"; 

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $stmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getIncomeCategoryNames(){

        $sql = 'SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



}