<?php
session_start();
include "header.php";

if (isset($_SESSION["email"])) {
    $user = $_SESSION["email"];
}

if (isset($_GET["email"])) {
    $user = $_GET["email"];
}

if (!isset($user)) {
    header("Location: login.php");
}

require "func.php";
require "conection.php";

$bd = new PDO("mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
    $bd_config["user"],
    $bd_config["password"]);

$sel = "select * from AppUser where email like '".$user."'";
$res = $bd->query($sel);
foreach ($res as $row) {
    $email = $row['email'];
    $rol = $row['rol'];
    $name = $row['name'];
    $surname = $row['lastname'];
    $openTickets = $row['openTickets'];
}

if ($openTickets == 0) {
    $openTickets = false;
}

// para la foto de perfil
require "file_dir.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Perfil</title>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <div id="div-profile">
        <?php // cambiar la foto de perfil antes de mostrar la imagen
        if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION['email'] == $user) {
            if(uploadFile(
                $_FILES['new_profile_picture']['tmp_name'],
                $profile_picture_directory,
                $user . ".png"
            );){
                echo "Imagen actualizada";
            }else {
                echo "Error al cambiar la imagen";
            }
        } ?>

        <img src="<?= $profile_picture_directory . $user . ".png" ?>" alt="foto de perfil">

        <?php if ($_SESSION['email'] == $user) { ?>
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="new_profile_picture" required accept=".jpg, .jpeg, .png, .webp">
                <input type="submit" value="cambiar foto">
            </form>
        <?php } ?>

        <table>
            <tr>
                <td>Usuario:</td>
                <td><?= $email?></td>
            </tr>
            <tr>
                <td>Nombre:</td>
                <td><?= $name?></td>
            </tr>
            <tr>
                <td>Apellido:</td>
                <td><?= $surname?></td>
            </tr>
            <?php
                if ($_SESSION['rol'] == 1 || $_SESSION['email'] == $user) {
                if ($rol != 1) {
            ?>
            <tr>
                <td>Tickets abiertos:</td>
                <?php
                if (!$openTickets) {
                    ?>
                    <td>No hay tickets abiertos</td>
                    <?php
                } else {
                    ?>
                    <td><a href="ticket_list.php?search=user:<?=$email?>"><?= $openTickets?></a></td>
                    <?php
                }
                ?>
            </tr>
            <?php } ?>
            <tr>
                <td>Rol:</td>
                <td>
                    <?php
                        if ($rol == 1) {
                            echo "Técnico";
                        } else {
                            echo "Usuario";
                        }
                    ?>
                </td>
            </tr>
            <?php
            }
            ?>
        </table>
        <br>
        <br>
        <br>
        <br>
        <!-- <a href="edit_profile.php">Editar perfil</a> -->
        <!-- Eliminar cuenta -->
        <form action="confirmation_panel.php" method="post">
            <input type="hidden" name="email" value="<?= $email?>">
            <input type="submit" value="CerrarCuenta">
        </form>
        <form action="change_password.php" method="post">
            <input type="submit" value="Cambiar Contraseña" name="change_password">
        </form>
    </div>
</body>
</html>
