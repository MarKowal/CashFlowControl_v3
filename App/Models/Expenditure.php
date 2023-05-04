<?php

namespace App\Models;

use PDO;

class Expenditure extends \Core\Model{

    public $errors = []; 

    public function __construct($data = []){
         foreach($data as $key => $value){
            $this->$key = $value;
         };
    }

    public static function getDefaultExpenseCategories(){

        $sql = 'SELECT * FROM expenses_category_default';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 1);
    }

    
    public static function getDefaultPaymentCategories(){

        $sql = 'SELECT * FROM payment_methods_default';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 1);
    }

    public static function checkIfUserHasDefaultExpenseCategories(){

        $sql = 'SELECT * FROM expenses_category_assigned_to_users WHERE user_id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        return $stmt->fetch();
    }

    public function saveExpensesToAssignedCategories($categories){

        for($i=0; $i<count($categories); $i++){
            $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name) 
                    VALUES (:user_id, :name)';
            
            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':name', $categories[$i], PDO::PARAM_STR);
            $stmt->execute();
        }
    }

    public static function checkIfUserHasDefaultPaymentCategories(){

        $sql = 'SELECT * FROM payment_methods_assigned_to_users WHERE user_id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        return $stmt->fetch();
    }

    public function savePaymentsToAssignedCategories($categories){

        for($i=0; $i<count($categories); $i++){
            $sql = 'INSERT INTO payment_methods_assigned_to_users (user_id, name) 
                    VALUES (:user_id, :name)';
            
            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':name', $categories[$i], PDO::PARAM_STR);
            $stmt->execute();
        }
    }
}

?>