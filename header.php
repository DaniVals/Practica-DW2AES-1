<header>
    <link rel="stylesheet" href="css/header.css">
    Usuario: <?= $_SESSION['email']?>
    <a href="ticket_list.php">Mis tickets</a>
    <?php if ($_SESSION['rol'] != 1) {
    // poner el 'crear ticket' si NO es tecnico
    echo '<a href="create_ticket.php">Crear un ticket</a>'; 
    } ?>
    <a href="logout.php">Cerrar sesión</a>
    <hr>
</header>
