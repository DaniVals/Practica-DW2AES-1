<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> Sign-in </title>

    </head>

    <body>

        <form action="signin_procesa.php" method="post">

            <h2> Introduzca sus datos </h2>

            <label for="name"> Nombre </label>
            <input type="text" name="name" id="name"><br>

            <label for="apps"> Apellido/s </label>
            <input type="text" name="apps" id="apps"><br>

            <label for="gmail"> Correo electrónico </label>
            <input type="email" name="gmail" id="gmail"><br>

            <label for="passwd"> Contraseña </label>
            <input type="password" name="passwd" id="passwd"><br>

            <label for="repPasswd"> Repita la contraseña </label>
            <input type="password" name="repPasswd" id="repPasswd"><br>

            <input type="submit" value="Registrarse">

        </form>
        
    </body>

</html>