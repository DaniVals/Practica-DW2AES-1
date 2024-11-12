<?php
    // Iniciar sesion
    session_start();
    // Comprobar si el usuario ya esta logueado
    if (isset($_SESSION["email"])) {
        header("Location: ticket_list.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <!-- Imprimir formulario si entramos por GET -->
    <?php 
        if ($_SERVER["REQUEST_METHOD"] == "GET") { 
    ?>
    <div id="div-login">
        <form method="POST">
            <label>Usuario:</label><br>
            <input type="text" name="email"><br><br>
            <label>Contraseña:</label><br>
            <input type="password" name="passw"><br><br>
            <input type="submit" value="Iniciar sesión"><br><br>
        </form>
        <div>
            <button type="button"><a href="signin.php">Registrarse</a></button>
        </div>
    </div>
    <?php 
        }else{
        include "func.php";
        // comprobar si el usuario y la contraseña son correctos
        if (login($_POST["email"], $_POST["passw"])) {
            header("Location: ticket_list.php");
        } else {
            echo "Error al iniciar sesión";
        }
    } 
    ?>
</body>
</html>
