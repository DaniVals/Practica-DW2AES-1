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
            Usuario: <input type="text" name="email"><br>
            Contraseña: <input type="password" name="passw">
        </form>
    </div>

    <?php 
        }else{
            // si entramos por POST
        session_start();
        include "func.php";
        // TODO seguir cuando tengamos BBDD
        // Incluir la funcion para iniciar sesion
        if (login($_POST["email"], $_POST["passw"])) {
            header("Location: ticket_list.php");
        } else {
            echo "Error al iniciar sesión";
        }
    } 
    ?>
</body>
</html>
