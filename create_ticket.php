<html>
    <head>
        <title>Create Ticket</title>
    </head>
    <body>
        <h1>Create Ticket</h1>
        <form action="" method="post">
            <label for="subject">Asunto</label>
            <input type="text" name="subject" id="subject" required>
            <label for="description">Description</label>
            <intput type="text" name="description" id="description" required>
            <label for="priority">Priority</label>
            <input type="text" name="priority" id="priority" required>
        </form>
</html>

<?php
include "header.php";
include "func.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject = $_POST["subject"];
    $description = $_POST["description"];
    $priority = $_POST["priority"];
    $user = $_SESSION["email"];
    $ticket = create_ticket($subject, $description, $priority, $user);
    if ($ticket) {
        header("Location: ticket_list.php");
    } else {
        echo "Error al crear el ticket";
    }
}
?>
