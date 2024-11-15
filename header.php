<header>
    <link rel="stylesheet" href="css/header.css">
    Usuario: <?= $_SESSION['email']?>
    <a href="profile.php">Mi perfil</a>
    <a href="ticket_list.php">
    <?php if ($_SESSION['rol'] == 1) {
        echo "Lista de tickets";
    } else {
        echo "Mis tickets";
    } ?>
    </a>
    <?php if ($_SESSION['rol'] != 1) {
    // poner el 'crear ticket' si NO es tecnico
    echo "<a href='create_ticket.php'>Crear un ticket</a>"; 
    } ?>
    <a href="logout.php">Cerrar sesión</a>
    <hr>
</header>
