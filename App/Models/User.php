<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Mail;
use Core\View;

class User extends \Core\Model{

    public $errors = []; 

    public function __construct($data = []){
        // $data to wartości z tablicy $_POST
        // wartości tablicy trzeba zamienić na atrybuty obiektu $user
         foreach($data as $key => $value){
            $this->$key = $value;
         };
    }

    public function save(){

        $this->validate();

        if (empty($this->errors)){
            //hashowanie hasła przed zapisem do bazy danych:
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $token = new Token();
            $hashed_token = $token->getHash();

            $this->activation_token = $token->getValue();

            $sql = 'INSERT INTO users (name, email, password_hash, activation_hash) 
                    VALUES (:name, :email, :password_hash, :activation_hash)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    public function validate(){

        if ($this->name == ''){
            $this->errors[] = 'Name is required.';
        }

        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) == false){
            $this->errors[] = 'Invalid email.';
        }

        if (static::emailExists($this->email, $this->id ?? null)){
            $this->errors[] = 'Email already exists in the data base.';
        }

        /*if ($this->password != $this->password_confirm){
            $this->errors[] = 'Password must match confirmation.';
        }*/
        if(isset($this->password)){
            if (strlen($this->password) < 6){
                $this->errors[] = 'Password must have at least 6 characters.';
            }        

            if (preg_match('/.*[a-z]+.*/i', $this->password) == 0){
                $this->errors[] = 'Password needs at least one letter.';
            }

            if (preg_match('/.*\d+.*/i', $this->password) == 0){
                $this->errors[] = 'Password needs at least one number.';
            }
        }
    }

    //dla walidacji w Account w AJAX trzeba było ustawić public static:
    //protected function emailExists($email){
    public static function emailExists($email, $ignore_id = null){
        /*$sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch() !== false;*/

        //return static::findByEmail($email) !== false;

        $user = static::findByEmail($email);

        if($user){
            if($user->id != $ignore_id){
                return true;
            }

            return false;
        }

    }
    
    public static function findByEmail($email){
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        
        //standardowo fetch zwraca tablicę
        //teraz chcę żeby zwróciło obiekt:
        //$stmt->setFetchMode(PDO::FETCH_CLASS, 'App\Models\User');

        //zamiast hard-coded App\Models\User używam funkcji:
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public static function authenticate($email, $password){

        //czy user z takim emailem istnieje?
        $user = static::findByEmail($email);
        //jeżeli tak to
        //czy user z takim hasłem istnieje?
        if($user && $user->is_active){
            if (password_verify($password, $user->password_hash)){
                return $user;
            }
        }
        return false;
    }

    public static function findByID($id){
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public function rememberLogin(){
        //tworzę nowy token ktory będzie w cookie
        $token = new Token();
        //hashuję token przed zapisaniem w DB
        $hashed_token = $token->getHash();

        //zapisuję w zmiennej czysty token, pójdzie do cookie
        $this->remember_token = $token->getValue();
        //ustawiam czas wygaśnięcia cookie np. 2 dni
        $this->expiry_timestamp = time() + 60 * 60 * 24 * 30;

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at) 
                VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

        return $stmt->execute();
    }

    public static function sendPasswordReset($email){

        $user = static::findByEmail($email);

        if($user){
            //generuję token i zapisuję do DB
            if($user->startPasswordReset()){
                $user->sendPasswordResetEmail();
            }
        }
    }

    protected function startPasswordReset(){
        
        //do resetu hasła generuję nowy token
        $token = new Token();
        $hashed_token = $token->getHash();
        
        //token będzie potrzebny do URL z resetem hasła 
        $this->password_reset_token = $token->getValue();

        //czas trwania linku
        $expiry_timestamp = time() + 60*60*2; //2 godziny

        $sql = 'UPDATE users SET
                password_reset_hash = :token_hash,
                password_reset_expiry = :expires_at
                WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();

    }

    protected function sendPasswordResetEmail(){

        //tworzę URL z tokenem
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/password/reset/'.$this->password_reset_token;

        //$text = "Please click on the following URL to reset your password: $url";
        //$html = "Please click on the following URL to reset your password: <a href=\"$url\">LINK</a>";
       
        $text = View::getTemplate('Password/reset_email.txt', ['url' => $url]);
        $html = View::getTemplate('Password/reset_email.html', ['url' => $url]);

        //wysyłam maila:
        //adres email został wczesniej pobrany z DB
        Mail::send($this->email, 'Password reset', $text, $html);
    }

    public static function findByPasswordReset($token){
        //robię hash z przesłanego tokena:
        $token = new Token($token);
        $hashed_token = $token->getHash();

        $sql = 'SELECT * FROM users WHERE password_reset_hash = :token_hash';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $user = $stmt->fetch();

        if($user){
            //spr czy token sie nie przedawnił:
            if(strtotime($user->password_reset_expiry) > time()){
                return $user;
            }            
        }

    }

    public function resetPassword($password){
        //trzeba spr poprawność nowego hasła z resetu:
        $this->password = $password;

        $this->validate();
        
        //jeżeli walidacja dobrze poszła: 
        if(empty($this->errors)){
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'UPDATE users SET
            password_hash = :password_hash,
            password_reset_hash = NULL,
            password_reset_expiry = NULL
            WHERE id = :id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':password_hash',  $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

            return $stmt->execute();
        }

        return false;

    }

    public function sendActivationEmail(){

        //tworzę URL z tokenem
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/signup/activate/'.$this->activation_token;

        //$text = "Please click on the following URL to reset your password: $url";
        //$html = "Please click on the following URL to reset your password: <a href=\"$url\">LINK</a>";
       
        $text = View::getTemplate('Signup/activation_email.txt', ['url' => $url]);
        $html = View::getTemplate('Signup/activation_email.html', ['url' => $url]);

        //wysyłam maila:
        //adres email został wczesniej pobrany z DB
        Mail::send($this->email, 'Account activation', $text, $html);
    }

    public static function activate($value){
        //najpierw trzeba przerobić token na hash:
        $token = new Token($value);
        $hashed_token = $token->getHash();

        $sql = 'UPDATE users SET
                is_active = 1,
                activation_hash = NULL
                WHERE activation_hash = :hashed_token';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':hashed_token',  $hashed_token, PDO::PARAM_STR);

        $stmt->execute();
    }

    public function updateProfile($data){
        $this->name = $data['name'];
        $this->email = $data['email'];
        //jeżeli w ogóle zmieniam hasło:
        if($data['password'] != ''){
            $this->password = $data['password'];
        }

        $this->validate();

        if (empty($this->errors)){
            $sql = 'UPDATE users SET
                name = :name,
                email = :email';
                //kombinowanie bo password jest opcjonalne:
                if(isset($this->password)){
                    $sql .= ', password_hash = :password_hash';
                }  
            $sql .= "\nWHERE id = :id";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name',  $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email',  $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            if(isset($this->password)){
                $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
                $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            }

            return $stmt->execute();
        }

        return false;
    }
}


?>