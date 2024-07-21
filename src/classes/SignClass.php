<?

class SignUp{
    private $name;
    private $email;
    private $password;
    // private $pass_hash;

    function __construct($name,$email,$password){
        $this->name=$name;
        $this->email=$email;
        $this->password=$password;
    }

    function control(){
        if (empty($this->name)){
            die("Introduceti numele!");
        }
        
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            die("Emailul introdus nu este valid");
        }
        
        if (strlen($this->password < 8 )){
            die("parola prea scurta");
        }
        
        if(!($_POST["password"]==$_POST["password_confirm"])){
            die("Parolele nu sunt la fel");
        }
    }
    function get_Name(){
        return $this->name;
    }

    function get_Pass(){
        return $this->password;
    }
    function get_Email(){
        return $this->email;
    }
}