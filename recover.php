<!-- Pagina para la recuperacion de la contraseña -->
<?php
    session_start();
    if (isset($_SESSION["email"])) {
        header("Location: ticket_list.php");
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <title>Recuperar contraseña</title>
</head>
<body>
    <?php 
        if ($_SERVER["REQUEST_METHOD"] == "GET") { 
    ?>
    <div id="div-recover">
        <form method="POST">
            <label>Usuario:</label><br>
            <input type="text" name="email" require><br><br>
            <input type="submit" value="Recuperar contraseña"><br><br>
        </form>
    </div>
    <?php 
        }else{
        include "func.php";
        if (recover_password($_POST["email"])) {
            echo "Nueva contraseña enviada al correo";
        } else {
            echo "Error al recuperar contraseña";
        }
    } 
    ?>
</body>
</html>
