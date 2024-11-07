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

            $continue = TRUE;

            $name = $_POST['name'];
            if (!isChar($name)) {
                echo "El nombre contiene caractéres inválidos<br>";
                $continue = FALSE;
            }

            $lastname = $_POST['lastname'];
            if (!isChar($lastname)) {
                echo "El/los apellido/s contienen caractéres inválidos<br>";
                $continue = FALSE;
            }
            
            $user = $_POST['user'];
            if (checkUser($user)) {
                echo "Ya existe este email<br>";
                $continue = FALSE;
            }

            if ($_POST['passwd']!=$_POST['repPasswd']) {
                echo "Las contraseñas no son iguales";
               $continue = FALSE;
            }

            if ($continue) {
                
                signUserIn();
            }
        }

    ?>

        <form method="post">

            <h2> Introduzca sus datos </h2>

            <label for="name"> Nombre </label>
            <input type="text" name="name" id="name" required><br>

            <label for="lastname"> Apellido/s </label>
            <input type="text" name="lastname" id="lastname" required><br>

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