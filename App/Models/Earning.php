<?php

namespace App\Models;

use PDO;

class Earning extends \Core\Model{

    public $errors = []; 

    public function __construct($data = []){
         foreach($data as $key => $value){
            $this->$key = $value;
         };
    }

    public static function getDefaultIncomeCategories(){

        $sql = 'SELECT * FROM incomes_category_default';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 1);
    }

    public function saveToAssignedCategories($categories){

        for($i=0; $i<count($categories); $i++){
            $sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name) 
                    VALUES (:user_id, :name)';
            
            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':name', $categories[$i], PDO::PARAM_STR);
            $stmt->execute();
        }
    }

    public static function checkIfUserHasDefaultCategories(){

        $sql = 'SELECT * FROM incomes_category_assigned_to_users WHERE user_id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        return $stmt->fetch();
    }

    public function getIncomeCategoryIdAssignedToUser(){

        $sql = 'SELECT * FROM incomes_category_assigned_to_users WHERE user_id = :id AND name = :incomeCategory';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':incomeCategory', $this->incomeCategory, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_COLUMN, 0);
    }


    public function saveToIncomes(){

        $this->validate();

        if (empty($this->errors)){

            //robocze:
            echo $_SESSION['user_id'].'<br>';
            echo $this->incomeCategory;  //z tego wyciągam: amount / date / category / comment


        } else {
            var_dump($this->errors);
            //jakiegoś returna z błędem trzeba zakodować
        }
    }

    public function validate(){
        
        if ($this->amount == ''){
            $this->errors[] = 'Amount is required.';
        }

        if ($this->date == ''){
            $this->errors[] = 'Date is required.';
        }

        if ($this->incomeCategory == ''){
            $this->errors[] = 'Income category is required.';
        }

        if ((int)$this->amount <= 0){
            $this->errors[] = 'Amount must be more than zero.';
        }

        if (! strtotime($this->date)){
            $this->errors[] = 'Date must be a date-type';
        }

    }


}