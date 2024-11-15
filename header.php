<header>
    
    <div id="menu">

        <a href="profile.php">Mi perfil</a>
        
        <?php if ($_SESSION['rol'] == 1) {
            echo "<a href='ticket_list.php'>Lista de tickets</a>";
        } else {
            echo "<a href='ticket_list.php'>Mis tickets</a>";
        } ?>
        
        
        <?php if ($_SESSION['rol'] != 1) {
            // poner el 'crear ticket' si NO es tecnico
            echo "<a href='create_ticket.php'>Crear un ticket</a>"; 
        } ?>
        <a href="logout.php">Cerrar sesión</a>
        
    </div>

    <link rel="stylesheet" href="css/header.css">
    <p> Usuario: <?= $_SESSION['email']?> </p>

    <hr>

</header>
