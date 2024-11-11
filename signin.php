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

            $name = str_replace(" ","",$_POST['name']);
            if (!isChar($name)) {
                echo "El nombre contiene caractéres inválidos<br>";
                $continue = FALSE;
            }

            $surname = $_POST['surname'];
            if (!isChar(str_replace(" ","",$surname))) {
                echo "El apellido 1 contienen caractéres inválidos<br>";
                $continue = FALSE;
            }

            $lastname = $_POST['lastname'];
            if (!isChar(str_replace(" ","",$lastname))) {
                echo "El apellido 2 contienen caractéres inválidos<br>";
                $continue = FALSE;
            }

            if ($_POST['passwd']!=$_POST['repPasswd']) {
                echo "Las contraseñas no son iguales";
               $continue = FALSE;
            }

            if ($_POST['rol']==0) {
                echo "Escoja su rol";
                $continue = FALSE;
            }

            if ($continue) {
                
                $email = setEmail($name,$_POST['surname'],$_POST['lastname'],$_POST['rol']);

                if (checkEmail($email)) {
                    echo "Este usuario ya existe";
                }
                else {
                    signUserIn($email,$_POST['passwd'],$_POST['name'],$_POST['surname'],$_POST['lastname'],$_POST['rol']);
                }
            }
        }

    ?>

        <form method="post">

            <h2> Introduzca sus datos </h2>

            <label for="name"> Nombre </label>
            <input type="text" name="name" id="name" required><br>

            <label for="surname"> Apellido 1 </label>
            <input type="text" name="surname" id="surname"><br>
            
            <label for="lastname"> Apellido 2 </label>
            <input type="text" name="lastname" id="lastname" required><br>

            <label for="passwd"> Contraseña </label>
            <input type="password" name="passwd" id="passwd" required><br>

            <label for="repPasswd"> Repita la contraseña </label>
            <input type="password" name="repPasswd" id="repPasswd" required><br>

            <select name="rol" id="rol">
                <option default value="0"> Escoja su rol </option>
                <option value=1> Técnico </option>
                <option value=2> Empleado </option>
            </select><br>

            <input type="submit" value="Registrarse">

        </form>
        
    </body>

</html>