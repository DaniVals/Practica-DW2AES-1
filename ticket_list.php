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
    <?php
        // si no es tecnico, mostramos los tiquets del usuario
        if ($_SESSION["rol"] == 1) {
            echo '<title> Lista de tickets </title>';
        }else{
            echo '<title> Mis tickets </title>';
        }
    ?>
    <link rel="stylesheet" href="css/ticket_list.css">
</head>

<?php require_once "header.php"; ?>

<body>

    <?php
        require_once "conection.php";
        require_once "func.php";

        $bd = new PDO(
            "mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
            $bd_config["user"],
            $bd_config["password"]
        );

        // si no es tecnico, mostramos los tiquets del usuario
        // por lo que si no es tecnico (empleado o cualquier otro rol), que muestre solo los tickets de ese usuario
        switch ($_SESSION["rol"]) {
            case 1:
                // tecnicos
                $select = 'SELECT idTicket, email, subject, messBody, state FROM ticket';
                break;
            
            default:
                // cualquier otro rol
                $select = 'SELECT idTicket, email, subject, messBody, state FROM ticket WHERE email LIKE "'. $_SESSION["email"].'"';
                break;
        }

        // TODO order by priority, y fecha y hora (teniendo en cuenta que el ticket se ordena por la id, que va antes el que se guarda antes...)
        // ASK deberia ordenarlo por si esta completado o no?
        $tickets = $bd->query($select);

        if($tickets->rowCount() <= 0){

            // mensaje de error si no hay tiquets
            // esto hay que ponerle alguna id o clase para ponerlo mas bonito
            echo "<p id='not-found-message'> No tienes tiquets creados </p>";


        }else {

            // imprimir un ticket por cada ticket encontrado
            foreach ($tickets as $ticket) {

                echo    '<div class="ticket-preview">';

                echo        '<h2> <a href="ticket.php?id='.$ticket["idTicket"].'">'.$ticket["subject"].'</a> </h2>';
                echo        '<h3>'.$ticket["email"].'</h3>';
                
                printSVG($ticket["state"]);

                echo        '<div>'.$ticket["messBody"].'</div>'; // el cuerpo lo puse como un div
                echo    '</div>'; // cerrar el div del ticket
            }
        }
    ?>

    <!-- <div class="ticket-preview">
        <h2> <a href="#">|Asunto/Subject|</a> </h2>
        <h3>|Correo/Email|</h3> -->

        <!-- poner un svg distinto dependiendo del estado, o solo el color en el fill  -->
        <!-- <svg><circle r="10" cx="10" cy="10" fill="red"/></svg>

        <div> -->
            <!-- como hacemos el cuerpo? como un p o como un div? -->
            <!-- |Cuerpo/Body|
        </div>
        <a href="">|Adjunto descargar/Atchatment download|</a>
    </div> -->


</body>
</html>
