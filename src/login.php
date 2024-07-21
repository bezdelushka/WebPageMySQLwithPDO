<?php
require 'connect.php';
require './classes/User.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userClass = new User($db);
    $user = $userClass->fetchByEmail($_POST["email"]);
    if ($user) {
        if (password_verify($_POST["password"], $user["pass_hash"])) {
            session_start();
            $_SESSION["id"] = $user["id"];
            $_SESSION["email"] = $user["email"];
            header("Location: index.php");
            exit;
        } else {
            die("email sau parola gresita");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>L O G I N</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <link rel="stylesheet" href="style.css?version40">
</head>
<body>
<div class="form" id="form-login">
    <form action="login.php" method="post" novalidate>
        <div>
            <label for="email"> Email: </label>
            <input type="email" id="email" name="email" class="form-input";>
        </div>
        <div>
            <label for="password"> Parola:</label>
            <input type="password" id="password" name="password" class="form-input">
        </div>
        <div>
            <label for="remember_me">
                <input type="checkbox" name="remember_me" id="remember_me" value="checked"> Remember me
            </label>
        </div>
        <input type="submit">
    </form>
</div>
</body>
</html>