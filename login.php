<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

    <!-- Imprimir formulario si entramos por GET -->
    <?php if ($_SERVER["REQUEST_METHOD"] == "GET") { ?>

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
            // TODO seguir cuando tengamos BBDD
        } 
    ?>

</body>
</html>