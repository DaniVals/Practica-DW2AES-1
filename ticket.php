<!DOCTYPE html>
<?php
    // REVISIONES BASICAS
    session_start();

    if (!isset($_SESSION["email"]) || !isset($_SESSION["rol"])) {
        
        // si no se ha iniciado sesion, volver a ./login.php
        header("Location: login.php");
    }

    // comprobar que se ha pasado un ticket en primer lugar
    if (!isset($_GET["id"])) {

        // si se mete al enlace directamente, redirige a ticket_list
        header("Location: ticket_list.php");
    }
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket - <?= $_GET["id"] ?></title>
    <link rel="stylesheet" href="css/ticket.css">
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

        switch ($_SESSION["rol"]) {
            case 1:
                // tecnicos
        $select = 'SELECT idTicket, email, subject, messBody, state FROM ticket WHERE idTicket =' . $_GET["id"];
                break;
            
            default:
                // cualquier otro rol
        $select = 'SELECT idTicket, email, subject, messBody, state FROM ticket WHERE idTicket =' . $_GET["id"] . ' AND email LIKE "'. $_SESSION["email"].'"';
                break;
        }
        $tickets = $bd->query($select);

        // si es empleado y no es dueño, no encuentra el ticket y entra aqui
        if($tickets->rowCount() <= 0){

            // mensaje de error si no encuentra el ticket
            echo "<p id='not-found-message'> No existe ese ticket </p>";


        }else {

            // imprimir un ticket por cada ticket encontrado
            // deberia cambiar esto por un fetch o algo asi, pero no se como funciona, y hay un where con una clave primaria, asi que no deberia
            foreach ($tickets as $ticket) {

                echo    '<div class="ticket-view">';

                echo        '<h2>'.$ticket["subject"].'<span> #'.$ticket["idTicket"].'</span></h2>';
                echo        '<h3>'.$ticket["email"].'</h3>';
                
                printSVG($ticket["state"]);

                echo        '<div>'.$ticket["messBody"].'</div>'; // el cuerpo lo puse como un div
                
                // cualquier otro rol
                $select = 'SELECT email, messBody FROM answer WHERE idTicket =' . $_GET["id"];
                $respuestas = $bd->query($select);
                
                foreach ($respuestas as $respuesta) {
                    echo '<hr>';
                    echo '<h3>'.$respuesta["email"].'</h3>';
                    echo '<div>'.$respuesta["messBody"].'</div>'; // el cuerpo lo puse como un div
                }
                
                echo    '</div>'; // cerrar el div del ticket
            }
        }
    ?>


</body>
</html>
