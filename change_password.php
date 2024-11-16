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
    </head>

    <body>

        <div id="div-change">
            <?php
                // avisar de porque te esta llevando aqui
                if (isset($_SESSION["need_passwd_change"])) {
                    if ($_SESSION["need_passwd_change"] == true) {
                        echo "<p> Tienes que cambiar tu contraseña generada </p>";
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
                    echo "Las contraseñas no coinciden";
                } else {
                    if (change_password($_POST["newpassw"])) {
                        echo "Contraseña cambiada correctamente";   
                        set_passwd_change($email, 0);
                        session_destroy();
                        header("Location: login.php");
                    } else {
                        if (change_password($_POST["newpassw"])) {
                            echo "Contraseña cambiada correctamente";

                            // cambiar la BBDD
                            set_passwd_change($email, 0);

                            session_destroy();
                            header("Location: login.php");
                        } else {
                            echo "Error al cambiar la contraseña";
                        }
                    }
                }
            }
        ?>

    </body>

</html>
