<!DOCTYPE html>
<?php
    // REVISIONES BASICAS
    session_start();

    if (!isset($_SESSION["email"]) || !isset($_SESSION["rol"])) {
        
        // si no se ha iniciado sesion, volver a ./login.php
        header("Location: login.php");
    }
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/ticket_list.css">
    <?php
        // si no es tecnico, mostramos los tickets del usuario
        if ($_SESSION["rol"] == 1) {
            echo '<title> Lista de tickets </title>';
        }else{
            echo '<title> Mis tickets </title>';
        }
    ?>
</head>

<?php require_once "header.php"; ?>

<body>

    <?php
        require_once "func.php";

        // BUSCADOR
        ?>
        <form method="get">
            <input type="text" name="search" id="search" value= <?php if (isset($_GET["search"])) {
                echo $_GET["search"];
            } ?>>
            <input type="submit" value="Buscar" id="button-search">
        </form>
        <?php


        // mostrar tickets
        $tickets = querryTickets();

        if($tickets->rowCount() <= 0){
            echo "<p id='not-found-message'> No tienes tickets creados </p>";

        }else {
            // ==== imprimir un ticket por cada ticket encontrado ====
            foreach ($tickets as $ticket) {
                echo    '<div class="ticket">';
                printTicketParameters($ticket["subject"], $ticket["messBody"], $ticket["email"], $ticket["state"], $ticket["sentDate"], $ticket["idTicket"], "", $ticket["priority"]);
                echo    '</div>'; // cerrar el div del ticket
            }
        }
        
    

    ?>
</body>
</html>
