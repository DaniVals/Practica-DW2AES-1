<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> Sign-in </title>
        <link rel="stylesheet" href="css/signin.css">

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

        <form method="post" id="div-signin">

            <h2> Introduzca sus datos </h2>

            <table>

                <tr>
                    <td> <label for="name"> Nombre </label> </td>
                    <td> <input type="text" name="name" id="name" class="inputs" required> </td>
                </tr>

                <tr>
                    <td> <label for="surname"> Apellido 1 </label> </td>
                    <td> <input type="text" name="surname" id="surname" class="inputs"> </td>
                </tr>

                <tr>
                    <td> <label for="lastname"> Apellido 2 </label> </td>
                    <td> <input type="text" name="lastname" id="lastname" class="inputs" required> </td>
                </tr>

                <tr>
                    <td> <label for="passwd"> Contraseña </label> </td>
                    <td> <input type="password" name="passwd" id="passwd" class="inputs" required> </td>
                </tr>

                <tr>
                    <td> <label for="repPasswd"> Repita la contraseña </label> </td>
                    <td> <input type="password" name="repPasswd" id="repPasswd" class="inputs" required> </td>
                </tr>

                <tr rowspan="2">
                    <td>
                        <select name="rol" id="rol">
                            <option default value="0"> Escoja su rol </option>
                            <option value=1> Técnico </option>
                            <option value=2> Empleado </option>
                        </select><br>
                    </td>
                </tr>

                <tr>
                    <td>  </td>
                    <td> <input type="submit" value="Registrarse" id="register-button"> </td>
                </tr>

            </table>
            
        </form>
        
    </body>

</html>