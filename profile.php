<!DOCTYPE html>
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
    $userid = $row['idUser'];
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Perfil</title>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <div id="div-profile">
        <div id="div-profile-picture">
            <?php // cambiar la foto de perfil antes de mostrar la imagen
            if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION['email'] == $user) {
                if(uploadFile(
                    $_FILES['new_profile_picture']['tmp_name'],
                    $profile_picture_directory,
                    $user . ".png"
                )){
                    echo "Imagen actualizada";
                }else {
                    echo "Error al cambiar la imagen";
                }
            } ?>

            <img src="<?= returnPPstring($user) ?>" alt="foto de perfil">

            <?php if ($_SESSION['email'] == $user) { ?>
                <form method="post" enctype="multipart/form-data">
                    <input type="file" name="new_profile_picture" id="new_pfp" required accept=".jpg, .jpeg, .png, .webp"><br>
                    <input type="submit" value="Cambiar foto" class="buttons-profile"  id="button-img">
                </form>
            <?php } ?>
        </div>

        <table id="table-user-data">
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
                // mostrar rating si es tecnico
                if ($rol == 1) {
            ?>
            <tr>
                <td>Valoracion:</td>
                <td>
                <?php
                    $ratingSel = "SELECT * FROM rating WHERE idTechnician = " . $userid;
                    $rating = $bd->query($ratingSel);
                    foreach ($rating as $rate) {
                        echo $rate['actualRating'];
                    }
                ?>
                </td>
            </tr>
            <?php 
                }
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
         
        <?php
        if ($rol == 1 && $_SESSION['rol'] == 1) {
        ?>
            <form action="add_review.php" method="post" id="stars">
                Estrellas:
                <input type="radio" name="stars" value="1">
                <input type="radio" name="stars" value="2">
                <input type="radio" name="stars" value="3">
                <input type="radio" name="stars" value="4">
                <input type="radio" name="stars" value="5">
                <input type="hidden" name="ratedId" value="<?= $userid?>">
                <input type="hidden" name="ratedEmail" value="<?= $email?>"><br>
                <input type="submit" value="valorar tecnico" class="buttons-profile" id="givestars">
            </form>
        <?php
        }
        ?>
        <br><br><br><br>
        <form action="confirmation_panel.php" method="post">
            <input type="hidden" name="email" value="<?= $email?>">
            <input type="submit" value="Cerrar cuenta" class="buttons-profile">
        </form>
        <form action="change_password.php" method="post">
            <input type="submit" value="Cambiar contraseña" name="change_password" class="buttons-profile">
        </form>
    </div>
</body>
</html>
