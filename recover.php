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
        <link rel="stylesheet" href="css/recover.css">
    </head>

    <body>
        <?php 
            if ($_SERVER["REQUEST_METHOD"] == "GET") { 
        ?>

        <div id="div-recover">

            <form method="POST">
                <label>Usuario:</label><br>
                <input type="text" name="email" id="input" required><br><br>
                <input type="submit" value="Recuperar contraseña" id="button-recover"><br><br>
            </form>

        </div>

        <?php 
            }else{
                require_once "func.php";
                if (recover_password($_POST["email"])) {
                    echo "Nueva contraseña enviada al correo";
                } else {
                    echo "Error al recuperar contraseña";
                }
            } 
        ?>
        
    </body>

</html>
