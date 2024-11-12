<!DOCTYPE html>
<?php
    // REVISIONES BASICAS
    session_start();
    include "header.php";

    if (!isset($_SESSION["email"]) || !isset($_SESSION["rol"])) {
        
        // si no se ha iniciado sesion, volver a ./login.php
        header("Location: login.php");
    }
    
    // bloquear el acceso a los tecnicos
    if ($_SESSION['rol'] == 1) {
        header("Location: ticket_list.php");
    }
?>
<html lang="en">
    <head>
        <title>Create Ticket</title>
    </head>
    <body>
        <h1>Create Ticket</h1>
        <form action="" method="post">
            <label for="subject">Asunto</label><br>
            <input type="text" name="subject" id="subject" required><br><br>
            <label for="description">Descripcion</label><br>
            <textarea name="description" id="description" required></textarea><br><br>
            <label for="attachment">Adjunto</label>
            <input type="file" name="attachment" id="attachment"><br><br>
            <label for="priority">Priority</label>
            <select name="priority" id="priority">
                <option value="4">Low</option>
                <option value="3">Normal</option>
                <option value="2">High</option>
                <option value="1">Very High</option>
            </select><br><br>
            <input type="submit" value="Create Ticket">
        </form>
</html>
<?php
    include "func.php";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $subject = $_POST["subject"];
        $description = $_POST["description"];
        $attachment = $_POST["attachment"];
        $priority = $_POST["priority"];
        $email = $_SESSION["email"];

        if (tooManyTickets($email)) {
            echo "Demasiados tickets abiertos";
        }
        else {
            
            $ticket = create_ticket($subject, $description, $attachment, $priority, $email);
            if ($ticket) {
                
                header("Location: ticket.php?id=$ticket");
            } else {
                echo "Error al crear el ticket";
            }
        }
    }
?>
