<?php 
include("connect.php");
include("./classes/SignClass.php");
$error = true;
$secret = '6Ld-Lr0pAAAAAKTjMSsh_-3MKMTd8G79XSz1bw81';
    
if (!empty($_POST['g-recaptcha-response'])) {
$curl = curl_init('https://www.google.com/recaptcha/api/siteverify');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, 'secret=' . $secret . '&response=' . $_POST['g-recaptcha-response']);
$out = curl_exec($curl);
curl_close($curl);
        
$out = json_decode($out);
if ($out->success == true) {
    $error = false;
    $signup=new SignUp($_POST["name"], $_POST["email"], $_POST["password"]);
    $pass_hash= password_hash($signup->get_Pass(), PASSWORD_DEFAULT);
    $signup->control();
    $sql="INSERT INTO Users (name, email, pass_hash) VALUES('{$signup->get_Name()}', '{$signup->get_Email()}', '{$pass_hash}')";
    $query=mysqli_query($db->mysqli, $sql)or die("ughhhhh did not conenct");
    header("Location:signup_success.html");
    exit;
    } 
}
        
if ($error) {
    echo 'Capcha did not go through';
}


