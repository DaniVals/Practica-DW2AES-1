<!-- Formulario para cambiar la contraseña -->
<?php
    session_start();
    if (!isset($_SESSION["email"])) {
        header("Location: login.php");
    }
    $email = $_SESSION["email"];
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <title>Cambiar contraseña</title>
        <link rel="stylesheet" href="css/change_password.css">
        <link rel="stylesheet" href="css/button_link.css">
    </head>

    <body>

        <div id="div-change">
            <?php
                // avisar de porque te esta llevando aqui
                if (isset($_SESSION["need_passwd_change"])) {
                    if ($_SESSION["need_passwd_change"] == true) {
                        echo "<p> Tu contraseña ha caducado </p>";
                    }
                }
            ?>

            <form method="POST">
                <label>Contraseña actual:</label><br>
                <input type="password" name="passw" class="inputs" required><br><br>
                <label>Nueva contraseña:</label><br>
                <input type="password" name="newpassw" class="inputs" required><br><br>
                <label>Repite la nueva contraseña:</label><br>
                <input type="password" name="rep_passw" class="inputs" required><br><br>
                <input type="submit" value="Cambiar contraseña" id="button-change">
            </form>

        </div>

        <?php
            require "func.php";
            if 
            (
                $_SERVER["REQUEST_METHOD"] === "POST" && 
                isset($_POST["passw"]) && 
                isset($_POST["rep_passw"]) && 
                isset($_POST["newpassw"])
            ){
                if($_POST["newpassw"] != $_POST["rep_passw"]){
                    echo "<p>Las contraseñas no coinciden</p>";
                } else {
                    if (change_password($_POST["newpassw"])) {
                        echo "<p>Contraseña cambiada correctamente</p>";   
                        set_passwd_change($email, 0);
                        session_destroy();
                        header("Location: login.php");
                    } else {
                        if (change_password($_POST["newpassw"])) {
                            echo "<p>Contraseña cambiada correctamente</p>";

                            // cambiar la BBDD
                            set_passwd_change($email, 0);

                            session_destroy();
                            header("Location: login.php");
                        } else {
                            echo "<p>Error al cambiar la contraseña</p>";
                        }
                    }
                }
                echo "<a href='change_password.php' class='button-link'> Reintentar <a>";
            }
        ?>

    </body>

</html>
