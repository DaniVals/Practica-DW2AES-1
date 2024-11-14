<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"])) {
    $email = $_POST["email"];
?>
<p>¿Estás seguro de que quieres cerrar tu cuenta?</p>
<form action="close_account.php" method="POST"> 
    <input type="hidden" name="email" value="<?= $email ?>">
    <input type="submit" value="Sí">
</form>
<form action="profile.php" method="GET">
    <input type="submit" value="No">
</form>
<?php
}
