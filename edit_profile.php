<!DOCTYPE html>
<?php
session_start();
include "header.php";

if (isset($_SESSION["email"])) {
    $user = $_SESSION["email"];
}

if (!isset($user)) {
    header("Location: login.php");
}

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <?php 
    require "conection.php";

    $bd = new PDO("mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $newName = $_POST['name'];
        $newSurname = $_POST['surname'];    
        // Buscar el token en la base de datos
        $update = "UPDATE AppUser SET name = '$newName', lastname = '$newSurname' WHERE email LIKE '$user'";
        $bd->query($update);

    }

    $sel = "SELECT name, lastname FROM AppUser WHERE email LIKE '".$user."'";
    $res = $bd->query($sel);
    foreach ($res as $row) {
        $name = $row['name'];
        $surname = $row['lastname'];
    }
    ?>
    <form method="post">
        <table>
            <tr>
                <td>Nombre:</td>
                <td><input type="text" name="name" value='<?= $name?>'></td>
            </tr>
            <tr>
                <td>Apellido:</td>
                <td><input type="text" name="surname" value='<?= $surname?>'></td>
            </tr>
        </table>
        <input type="submit" value="Guardar cambios" class="buttons-profile">
    </form>
</body>
</html>
