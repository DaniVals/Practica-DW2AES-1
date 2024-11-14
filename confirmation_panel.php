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
<form action="profile.php" method="GET">
    <input type="submit" value="No">
</form>
<?php
    include "func.php";
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["Si"])) {
        close_account($email);
        header("Location: login.php");
    }
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        header("Location: profile.php");
    }
?>
