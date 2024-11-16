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
        <title>Crear un ticket</title>
    </head>
    <body>
        <h1>Create Ticket</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="subject">Asunto</label><br>
            <input type="text" name="subject" id="subject" required><br><br>
            <label for="description">Descripcion</label><br>
            <textarea name="description" id="description" required></textarea><br><br>
            <label for="attachment">Adjunto</label>
            <input type="file" name="attachment" id="attachment"><br><br>
            <label for="priority">Priority</label>
            <select name="priority" id="priority">
                <option value="4">Baja</option>
                <option value="3">Estándar</option>
                <option value="2">Alta</option>
                <option value="1">Muy alta</option>
            </select><br><br>
            <input type="submit" value="Create Ticket">
        </form>
</html>
<?php
    include "func.php";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $subject = $_POST["subject"];
        $description = $_POST["description"];
        $attachment = "";
        if (!empty($_FILES['attachment']['name'])) {
            $attachment = $_FILES["attachment"];
        }
        $priority = $_POST["priority"];
        $email = $_SESSION["email"];

        if (tooManyOpenTickets($email)) {
            echo "<p>Demasiados tickets abiertos</p>";
        }
        else {
            
            $ticket = create_ticket($subject, $description, $attachment, $priority, $email);
            notifOpenTicket($email,$subject);
            if ($ticket) {
                oneMoreOpenTicket($email);
                header("Location: ticket.php?id=$ticket");
            } else {
                echo "<p>Error al crear el ticket</p>";
            }
        }
    }
?>
