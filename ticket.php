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
    <link rel="stylesheet" href="css/ticket.css">
    <title>Ticket - <?= $_GET["id"] ?></title>
</head>

<?php require_once "header.php"; ?>

<body>

    <?php
        require_once "conection.php";
        require_once "func.php";

        // CODIGO DE ESCRIBIR UN COMENTARIO
        if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST["ans"]) || isset($_POST["changeStatus"]))) {
            try {

                $bd = new PDO(
                    "mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
                    $bd_config["user"],
                    $bd_config["password"]
                );
                
                // comprobar que no haya un ticket igual
                $select = 'SELECT email, messBody FROM answer WHERE idTicket =' . $_GET["id"]
                     . ' AND email LIKE "' . $_SESSION['email'] . '"'
                     . ' AND messBody LIKE "%' . $_POST['ans'] . '"' 
                ;
                $respuestas = $bd->query($select);
                $check = true;
                foreach ($respuestas as $respuesta) {
                    $check = false;
                }
                if ($check) { // si no encuentra ninguna respuesta igual
                            
                    // añadir archivo
                    $fileStringForInsert = "NULL";
                    if (!empty($_FILES['attachment']['name'])) {
                        require "file_dir.php";
                        $attachment = $_FILES['attachment'];

                        if (uploadFile($attachment['tmp_name'], $attach_directory, $_GET['id'] . "-" . $attachment['name'])) {
                            // guardar el nombre en un string para hacer el insert, solo si todo sale bien
                            $fileStringForInsert = "'" . $_GET['id'] . "-" . basename($attachment['name']) . "'";
                        }
                    }

                    if (!isset($_POST['changeStatus']) || $_POST['changeStatus'] == 0) {

                        // solamente añadir el comentario
                        $insert = "INSERT INTO answer(idTicket,email,messBody, attachment) VALUES ("
                        . $_GET['id'] . ",'" . $_SESSION['email'] . "','" . $_POST['ans'] . "',". $fileStringForInsert .")";
                        $bd->query($insert);
                        
                    }else {
                        // añadir aclaracion de quien y cuando cerro el tiquet
                        $pre_text;
                        switch ($_POST['changeStatus']) {
                            case 1:
                                $pre_text = "[Resuelto] ";
                                break;
                            case 2:
                                $pre_text = "[En proceso] ";
                                break;
                            case 3:
                                $pre_text = "[Cerrar] ";
                                break;
                        }
                        $insert = "INSERT INTO answer(idTicket,email,messBody, attachment) VALUES ("
                        . $_GET['id'] . ",'" . $_SESSION['email'] . "','" . $pre_text . $_POST['ans'] . "',". $fileStringForInsert .")";
                        $bd->query($insert);
                        
                        // cambiar tambien el estado
                        $update = 'UPDATE ticket SET state = '.$_POST['changeStatus'].' WHERE idTicket = '.$_GET['id'];
                        $bd->query($update);


                        // sacar nombre del autor
                        $sql = "SELECT email, subject FROM ticket WHERE idTicket = ".$_GET['id'];
                        $emailEmployee = $bd->query($sql);

                        foreach ($emailEmployee as $email) {
                            $userEmail = $email['email'];
                            $ticketSubject = $email['subject'];
                        }

                        // bajar el numero de tickets abiertos
                        if ($_POST['changeStatus']!=2) {
                            oneLessOpenTicket($userEmail);
                        }

                        // enviar notificacion
                        notifChangedState($userEmail, $ticketSubject, $_POST['changeStatus']);
                    }

                    // avisar de que todo funciono correctamente
                    echo '<p id="ans-done-message"> Respuesta añadida </p>';
                }
            } catch (PDOException $e) {
                echo 'Al añadir el comentario: \n' . $e->getMessage();
            }
        }

        // ===== MOSTRAR HILO =====

        // hacer select
        $tickets = querryTickets($_GET["id"]);

        if($tickets->rowCount() <= 0){
            echo "<p id='not-found-message'> No existe ese ticket </p>";
            
        }else {

            // ==== imprimir el ticket ====
            echo    '<div class="ticket">';
            foreach ($tickets as $ticket) {
        
                printTicketParameters($ticket["subject"], $ticket["messBody"], $ticket["email"], $ticket["state"], $ticket["sentDate"], -1, $ticket["attachment"], $ticket["priority"]);
                
                
                $bd = new PDO(
                    "mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
                    $bd_config["user"],
                    $bd_config["password"]
                );
                
                $select = 'SELECT email, messBody, ansDate, attachment FROM answer WHERE idTicket =' . $_GET["id"];
                $respuestas = $bd->query($select);
                
                foreach ($respuestas as $respuesta) {
                    echo '<hr>';
                    printTicketParameters("", $respuesta["messBody"], $respuesta["email"], 0, $respuesta["ansDate"], -1, $respuesta["attachment"]);
                }
                
                // ==== añadir el textarea para escribir un comentario ====
                
                if ($_SESSION["rol"] == 1 || $ticket["state"] == 2) {
                ?>
                    <hr>
                    <form action="" method="post" enctype="multipart/form-data">
                    <textarea name="ans" placeholder="Respuesta..." id="ans" required></textarea><br><br>
                    <label for="attachment">Adjunto: </label>
                    <input type="file" name="attachment" id="attachment"><br><br>
                    <?php
                    // cambiar estado si es tecnico
                    if ($_SESSION["rol"] == 1) {
                    ?>
        
                        <select name="changeStatus" id="changestatus">
                            <option value="0">-- Cambiar estado --</option>
                            <option value="1">Resolver</option>
                            <option value="2">En proceso</option>
                            <option value="3">Cerrar</option>
                        </select>
        
                    <?php
                    }
                    ?>
        
                    <br>
                    <input type="submit" value="Responder" id="button-ans">
                    </form>

                <?php
                }
                
            }
            echo    '</div>'; // cerrar el div del ticket
        }
    ?>

</body>
</html>
