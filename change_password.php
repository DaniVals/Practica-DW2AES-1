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
<<<<<<< HEAD

    <head>
        <title>Cambiar contraseña</title>
        <link rel="stylesheet" href="css/change_password.css">
    </head>

    <body>

        <div id="div-change">

            <form method="POST">

                <label>Contraseña actual:</label><br>
                <input type="password" name="passw" class="inputs" required><br><br>

                <label>Nueva contraseña:</label><br>
                <input type="password" name="newpassw" class="inputs" required><br><br>

                <label>Repite la nueva contraseña:</label><br>
                <input type="password" name="rep_passw" class="inputs" required><br><br>

                <input type="submit" value="Cambiar contraseña" id="button-change"><br>

            </form>

        </div>

        <?php
            require_once "func.php";
            if 
            (
                $_SERVER["REQUEST_METHOD"] === "POST" && 
                isset($_POST["passw"]) && 
                isset($_POST["rep_passw"]) && 
                isset($_POST["newpassw"])
            ){
                if($_POST["newpassw"] != $_POST["rep_passw"]){
                    echo "Las contraseñas no coinciden";
=======
<head>
    <title>Cambiar contraseña</title>
</head>
<body>
    <div id="div-change">
        <form method="POST">
            <label>Contraseña actual:</label><br>
            <input type="password" name="passw" require><br><br>
            <label>Repite la contraseña actual:</label><br>
            <input type="password" name="rep_passw" require><br><br>
            <label>Nueva contraseña:</label><br>
            <input type="password" name="newpassw" require><br><br>
            <input type="submit" value="Cambiar contraseña"><br><br>
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
            if($_POST["passw"] != $_POST["rep_passw"]){
                echo "Las contraseñas no coinciden";
            } else {
                if (change_password($_POST["newpassw"])) {
                    echo "Contraseña cambiada correctamente";   
                    set_passwd_change($email, 0);
                    session_destroy();
                    header("Location: login.php");
>>>>>>> 3a7ef71a344dbc20af8bdb9ddaa7e18cff9b443a
                } else {
                    if (change_password($_POST["newpassw"])) {
                        echo "Contraseña cambiada correctamente";   
                        session_destroy();
                        header("Location: login.php");
                    } else {
                        echo "Error al cambiar la contraseña";
                    }
                }
            }
        ?>

    </body>

</html>
