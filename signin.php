<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> Sign-in </title>

    </head>

    <body>

    <?php

        if ($_SERVER['REQUEST_METHOD']=="POST") {

            require_once 'func.php';
            
            $user = $_POST['user'];
            if (checkUser($user)) {
                echo "Ya existe este email";
            }
        }

    ?>

        <form method="post">

            <h2> Introduzca sus datos </h2>

            <label for="name"> Nombre </label>
            <input type="text" name="name" id="name" required><br>

            <label for="apps"> Apellido/s </label>
            <input type="text" name="apps" id="apps" required><br>

            <label for="user"> Correo electrónico </label>
            <input type="email" name="user" required><br>

            <label for="passwd"> Contraseña </label>
            <input type="password" name="passwd" id="passwd" required><br>

            <label for="repPasswd"> Repita la contraseña </label>
            <input type="password" name="repPasswd" id="repPasswd" required><br>

            <input type="submit" value="Registrarse">

        </form>
        
    </body>

</html>