<?php

class Login {
    
    private $email;
    
    function __construct($email){
        $this->email=$email;
    }

    function isSetEmail(){
        if(empty($this->email)){
            die("nu ati introdus emailul");
            
        }
    }

    function FetchEmail($mysqli){
        $sql=sprintf("SELECT * FROM Users
        WHERE email = '%s'",
        $mysqli->real_escape_string($this->email));
        $query=mysqli_query($mysqli, $sql);
        return $query->fetch_assoc();
    }
}