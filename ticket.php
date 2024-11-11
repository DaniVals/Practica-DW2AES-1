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
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            try {

                $bd = new PDO(
                    "mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
                    $bd_config["user"],
                    $bd_config["password"]
                );

                if ($_POST['changeStatus'] == 0) {

                    // solamente añadir el comentario
                    $insert = 'INSERT INTO answer(idTicket,email,messBody) VALUES ('.$_GET['id'].',"'.$_SESSION['email'].'","'.$_POST['ans'].'")';
                    $bd->query($insert);
                    
                }else {
                    
                    // cambiar tambien el estado
                    $update = 'UPDATE ticket SET state = '.$_POST['changeStatus'].' WHERE idTicket = '.$_GET['id'];
                    $bd->query($update);

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

                    $insert = 'INSERT INTO answer(idTicket,email,messBody) VALUES ('.$_GET['id'].',"'.$_SESSION['email'].'","'.$pre_text . $_POST['ans'].'")';
                    $bd->query($insert);
                }



                // header("Location: ticket.php?id=".$_GET['id']);
                echo '<p id="ans-done-message"> Respuesta añadida </p>';

                // ASK se puede quitar o modificar una variable de POST para que solo ocurra una vez?

            } catch (PDOException $e) {
                echo 'Al añadir el comentario: \n' . $e->getMessage();
            }
        }

        // MOSTRAR HILO
        $tickets = printTickets($_GET["id"]);

    ?>

</body>
</html>
