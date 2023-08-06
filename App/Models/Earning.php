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
    public $errorMessage; 

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

        if ($this->validate()){

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
            return false;
        }
    }

    private function validate(){

        if (! isset($this->amount)){
            $this->errorMessage = 'Amount is required.';
            return false;
        }
        elseif ((int)$this->amount <= 0){
            $this->errorMessage = 'Amount must be more than zero.';
            return false;
        }
        elseif  (! isset($this->date)){
            $this->errorMessage = 'Date is required.';
            return false;
        }
        elseif  (! isset($this->incomeCategory)){
            $this->errorMessage = 'Income category is required.';
            return false;
        }
        elseif (! strtotime($this->date)){
            $this->errorMessage = 'Date must be a date-type';
            return false;
        }
        return true;

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
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getIncomeCategoryNameAssignedToUser(){

        $sql = 'SELECT name FROM incomes_category_assigned_to_users WHERE user_id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function addNewIncomeCategory($newIncomeName){

        if ($this->validateNewIncomeCategoryName($newIncomeName)){

            echo "good name for the category";

        } else {
            return $this->errorMessage;
        }
        

    }

    private function validateNewIncomeCategoryName($newIncomeName){

        if (strlen($newIncomeName) < 2){
            $this->errorMessage = 'Category name must have at least 2 characters.';
            return false;
        }        

        if (preg_match('/([A-Z])+/', $newIncomeName) == 1){
            $this->errorMessage = 'Category cannot include big letters.';
            return false;
        }

        if (preg_match('/[\d]/', $newIncomeName) == 1){
            $this->errorMessage = 'Category cannot include numbers.';
            return false;
        }

        if (preg_match('/[\W+]/', $newIncomeName) == 1){
            $this->errorMessage = 'Category cannot include special characters.';
            return false;
        }

        return true;
    }


}