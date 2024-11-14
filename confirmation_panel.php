<?php
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
}
$email = $_SESSION["email"];
?>
<p>¿Estás seguro de que quieres cerrar la cuenta <?= $email?>?</p>
<form method="POST"> 
    <input type="hidden" name="email" value="<?= $email ?>">
    <input type="submit" value="Sí" name="Si">
</form>
<form action="profile.php" method="post">
    <input type="submit" value="No" name="No">
</form>
<?php
    include "func.php";
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["Si"])) {
        close_account($email);
        header("Location: login.php");
    }
    if ($_SERVER["REQUEST_METHOD"] === "post" && isset($_POST["No"])) {
        header("Location: profile.php");
    }
?>
