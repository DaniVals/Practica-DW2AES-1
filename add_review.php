<?php
session_start();
include "header.php";

if (!isset($_SESSION["email"]) || !isset($_SESSION["rol"])) {
    // si no se ha iniciado sesion, volver a ./login.php
    header("Location: login.php");
}

if ($_SESSION["rol"] == 1) {
    // los tecnicos no pueden escribir reviews
    header("Location: ticket_list.php");
}
if (!isset($_POST["ratedId"])) {
    // si se mete al enlace directamente, redirige a ticket_list
    header("Location: ticket_list.php");
}

if ($_POST['stars'] < 1 || 5 < $_POST['stars']) {
    // si mete un valor no admitido, redirecciona
    header("Location: profile.php?email=" . $_POST["ratedEmail"]);
    return;
}
echo $_POST['stars'];


require "func.php";
require "conection.php";

$bd = new PDO(
    "mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
    $bd_config["user"],
    $bd_config["password"]
);

$sel = "SELECT * FROM rating WHERE idTechnician = " . $_POST["ratedId"];
$res = $bd->query($sel);
foreach ($res as $row) {
    $rating = $row['actualRating'];
    $numberRating = $row['numOfRatings'];
}

if (!isset($rating)) {
    // si no encuentra el usuario
    header("Location: profile.php?email=" . $_POST["ratedId"]);
}
$new_rating = ($_POST['stars'] * 1 / ($numberRating + 1)) + ($rating * $numberRating / ($numberRating + 1));

$upd = "UPDATE rating SET actualRating = '$new_rating', numOfRatings = ". ($numberRating + 1) ." WHERE idTechnician = " . $_POST["ratedId"];
$update = $bd->query($upd);

if ($update) {
    echo "true";
} else {
    echo "false";
}


header("Location: profile.php?email=" . $_POST["ratedEmail"]);