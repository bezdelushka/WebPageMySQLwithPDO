<?php 
require_once 'connect.php';
session_start();
include("connect.php");
$sql = "SELECT * FROM Img ORDER BY id DESC"; 
$result = mysqli_query($db->mysqli, $sql)or die(mysqli_error($db->mysqli));
?>

<!DOCTYPE html>
<html lang="en">
    
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="style.css?version200">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>W E L C O M E</title>
</head>
<body>
<h1>PROIECT 1 BAZE DE DATE</h1>

<?php 

if (isset($_SESSION["id"])):

include("mainpage.php");

else: ?>

<div id="signuplogin">
<a href="login.php"> LOGIN </a>
&#9733
<a href="signup.html"> SIGNUP</a>
</div>
<?php endif; ?>

</body>
</html>

