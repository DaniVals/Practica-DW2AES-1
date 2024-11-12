<?php
session_start();
include "header.php";

if (!isset($_SESSION["email"])) {
    header("Location: login.php");
}

require "func.php";
require "conection.php";

$bd = new PDO("mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
    $bd_config["user"],
    $bd_config["password"]);

$sel = "select * from AppUser where email like '".$_SESSION['email']."'";
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

if ($_SESSION['rol'] == 1 || $_SESSION['email'] == $email) {

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
            <tr>
                <?php
                    if ($_SESSION['rol'] == 1) {
                    
                    } else {    
                ?>
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
        </table>
        
    </div>
</body>
</html>
<?php
    }
} else {
    header("Location: index.php");
}
?>
