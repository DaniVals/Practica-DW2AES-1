<?php
// Funcion para iniciar sesión en la cuenta 
function login($email, $passwd) {
    require "conection.php";

    //Reisar que el correo sea correcto
    $email_regex = "/^[a-z]+@[a-z]*\.?[a-z]+\.com$/";
    if (!preg_match($email_regex, $email)) {
        return false;
    }
    // Revisar si la contraseña no esta vacía
    if ($passwd == "") {
        return false;
    }

    $bd = new PDO("mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]);

    $sel = "select passwd from AppUser where email like '$email'";
    $res = $bd->query($sel);
    foreach ($res as $row) {
        $passwd_crypt = $row['passwd'];
    }

    if (password_verify($passwd, $passwd_crypt)) {
        $ins = "select * from AppUser where email like '$email'";
        $resul = $bd->query($ins);
        foreach ($resul as $row) {
            // alamacenar el rol en la sesión
            $_SESSION['rol'] = $row['rol'];
            $_SESSION['email'] = $row['email'];
            return true;
        }   
        if($resul->rowCount() === 1){        
            return $resul->fetch();        
        } else {
            return false;
        }
    } else {
        return false;
    }
}

// Función que crea un ticket en la base de datos
function create_ticket($subject, $description, $attachment, $priority, $email) {
    require "conection.php";

    $bd = new PDO("mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]);
    
    if (!empty($_FILES['attachment']['name'])) {

        require "file_dir.php";

        // sacar la id del ultimo ticket
        $select_last_tickets = "SELECT idTicket from ticket ORDER BY idTicket DESC LIMIT 1";
        $last_tickets = $bd->query($select_last_tickets);
        $next_id;
        foreach ($last_tickets as $last_ticket) {
            $next_id = $last_ticket['idTicket'] + 1;
        }

        // subir el archivo y hacer insert si se sube correctamente
        if (uploadFile($attachment['tmp_name'], $attach_directory, "$next_id-" . $attachment['name'])) {
            $attachment_name = basename($attachment['name']);
            $ins = "INSERT INTO ticket (subject, messBody, priority, email, state, attachment) VALUES ('$subject', '$description', '$priority', '$email', 2, '$next_id-$attachment_name')";
            echo "El archivo ha sido subido correctamente";
        } else {
            return FALSE;
        }
    } else {
        $ins = "INSERT INTO ticket (subject, messBody, priority, email, state) VALUES ('$subject', '$description', '$priority', '$email', 2)";
    }

    // buscar el ticket creado, sacar la id y redirigir a la pagina de ese ticket
    $resul = $bd->query($ins); 
    if($resul){
        $sel_qry = "select idTicket from ticket where subject like '$subject' and messBody like '$description' and priority like '$priority' and email like '$email'";
        $res_sel = $bd->query($sel_qry);
        foreach ($res_sel as $row) {
            return $row['idTicket'];
        }
        return TRUE;
    }else{
        return FALSE;
    }
}
function uploadFile($attachment_tmpname, $attach_directory, $attach_name) {

    // generar ruta del archivo
    $uploadFilePath = $attach_directory . $attach_name;

    // Asegurrarse de que la carpeta uplodas existe
    if (!is_dir($attach_directory)) {
        if (!mkdir($attach_directory, 0775, true)) {
            // Error al crear el directorio
            return FALSE;
        }
    }

    // devuelve true o false si ha funcionado
    return move_uploaded_file($attachment_tmpname, $uploadFilePath);
}

// Función que mira si ya existe ese email en la base de datos
function checkEmail($email) {
    
    require "conection.php";
    
    $bd = new PDO("mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]);

    $query = "SELECT email FROM appUser WHERE email='$email'";
    $resul = $bd->query($query);

    if($resul->rowCount()==1) { return TRUE; }
    else { return FALSE; }
}

// Función que mira si una cadena son solo letras
function isChar($text) {

    $regex = "/^[a-zñáéíóúÑÁÉÍÓÚ]+$/i";
    if (preg_match($regex,$text)) { return TRUE; }
    else { return FALSE; }
}

// Función para dar de alta usuarios tras ver que los datos pasados por el formulario son correctos
function signUserIn($email,$passwd,$name,$surname,$lastname,$rol) {

    require "conection.php";
    
    $lastname = $surname." ".$lastname;

    $passwd_crypt = password_hash($passwd, PASSWORD_DEFAULT);
    
    $bd = new PDO("mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]);

    $sql = "INSERT INTO AppUser(email,passwd,name,lastname,rol,openTickets)
            VALUES('$email','$passwd_crypt','$name','$lastname',$rol,0)";
    
    try {
        $result = $bd->query($sql);
        header("Location: login.php");
        // Enviar un correo de activación
        // notifAccountActivation($email); 
    }
    catch (Exception $e) {
        echo "Problema al registrar al usuario, inténtelo de nuevo";
        echo $e->getMessage();
    }
    
}

// Función para mostrar el SVG recibiendo de argumento el estado del ticket
function printSVG($state) {

    switch ($state) {
        case '1':
            // solved
            echo '<svg><circle r="10" cx="10" cy="10" fill="lime"/></svg>';
            break;
        case '2':
            // in progress
            echo '<svg><circle r="10" cx="10" cy="10" fill="yellow"/></svg>';
            break;
        case '3':
            // closed
            echo '<svg><circle r="10" cx="10" cy="10" fill="red"/></svg>';
            break;
    }
}
//Función para establecer el email del usuario
function setEmail($name,$surname,$lastname,$rol) {
    
    $userName = strtolower($name).strtolower(substr($surname,0,1)).strtolower(substr($lastname,0,1));

    if ($rol==1) { return $userName."@soporte.empresa.com"; }
    else { return $userName."@empresa.com"; }
}


// Función para generar el select de ticket
// recibe numeros negativos en $id_ticket si quieres que no se tenga en cuenta
function querryTickets(?int $id_ticket = -1) {
    
    require "conection.php";

    $bd = new PDO(
        "mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]
    );
    

    $select = 'SELECT * FROM ticket WHERE 1 = 1';

    // sacar solo ese ticket, para la pagina `ticket.php`
    if (0 <= $id_ticket) {
        $select = $select . ' AND idTicket =' . $id_ticket;
    }
    
    // si no es tecnico, mostramos los tickets del usuario
    // por lo que si no es tecnico (empleado o cualquier otro rol), que muestre solo los tickets de ese usuario
    if ($_SESSION["rol"] != 1) {
        $select = $select . ' AND email LIKE "'. $_SESSION["email"].'"';
    }
    
    // busqueda
    if (isset($_GET["search"])) {
        $busqueda = $_GET["search"];
        if (strpos($busqueda, "user:") === 0) {
            $select = $select . " AND email LIKE '%" . substr($busqueda, 5) . "%'";
        }else if (strpos($busqueda, "body:") === 0) {
            $select = $select . " AND messBody LIKE '%" . substr($busqueda, 5) . "%'";
        } else {
            $select = $select . " AND (subject LIKE '%" . $busqueda . "%')";
        }
    }

    if ($id_ticket < 0) {
        // ASK deberia ordenarlo por si esta completado o no?
        $select = $select . " ORDER BY priority, sentDate";
    }

    // ==== hacer querry ====
    return $bd->query($select);
}

function printTicketParameters($subject, $messBody, $email, $state, $sentDate, ?int $id_ticket = -1, ?string $attachment_name = "") {
    
    // foto de perfil
    require "file_dir.php";
    echo '<a href="profile.php?email='. $email .'"> <img src="' . $profile_picture_directory . $email . '.png" alt="foto de perfil"></a>';

    // h2 opcional usando ""
    if ($subject != "") {
        echo "<h2>";

        // imprimir H2 con enlace si le pasas un parametro de $id_ticket valido (0<id)
        if (0 < $id_ticket) { echo '<a href="ticket.php?id='.$id_ticket.'">';}
        echo $subject;
        if (0 < $id_ticket) { echo '</a>';}
        
        echo "</h2>";
    }
    ?>

    <h3> <a href=<?="profile.php?email=$email" ?>><?= $email ?> </a></h3>
    <h4> <?= $sentDate ?> </h4>
    
    <?php    printSVG($state); ?>

    <div> <?= nl2br($messBody)?></div>

    <?php

    if ($attachment_name != "") {
        require "file_dir.php";
        echo "<a href='" . $attach_directory . $attachment_name . "' class='file-open-box' download>" . $attachment_name . "<a>";
    }

}


//Función que cuenta cuántos tickets tiene una persona
function tooManyOpenTickets($email) {
    
    $tickets = howManyOpenTickets($email);

    if ($tickets>=3) { return TRUE; }
    else { return FALSE; }
}

//Función que suma un ticket abierto al contador de tickets abiertos del usuario
function oneMoreOpenTicket($email) {
    
    $ticket = howManyOpenTickets($email);
    $ticket ++;

    require "conection.php";

    $bd = new PDO(
        "mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]);

    $alter = "UPDATE AppUser SET openTickets = $ticket WHERE email LIKE '$email'";
    $result = $bd->query($alter);
}

function oneLessOpenTicket($email) {
    
    $ticket = howManyOpenTickets($email);
    $ticket --;

    require "conection.php";

    $bd = new PDO(
        "mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]);

    $alter = "UPDATE AppUser SET openTickets = $ticket WHERE email LIKE '$email'";
    $result = $bd->query($alter);
}

//Función que devuelve el valor de la celda openTickets (cuántos tickets tiene abierto el usuario)
function howManyOpenTickets($email) {
    
    require "conection.php";

    $bd = new PDO(
        "mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]);
    
    $select = "SELECT openTickets FROM AppUser WHERE email LIKE '$email'";
    $tickets = $bd->query($select);

    foreach ($tickets as $ticketNum) {
        return $ticketNum["openTickets"];
    }
}

//EMAILS
//recibe el email del usuario (destinatary) y el asunto del ticket (ticketSubject)
function notifOpenTicket($destinatary,$ticketSubject) {
    
    require "email.php";
    //rellena el resto de campos necesarios para enviar el email
    $subject = "Ticket creado";
    $origin = "no-reply@soporte.empresa.com";
    $msgBody = "Su ticket '".$ticketSubject."' ha sido creado.";
    //envía el email
    enviarEmail($destinatary, $origin, $subject, $msgBody);
}

function notifChangedState($destinatary,$ticketSubject,$changedState) {
    
    require "email.php";

    switch ($changedState) {

        case 1:
            $state = "Cerrado";
            break;
        case 2:
            $state = "En progreso";
            break;

        case 3:
            $state = "Resuelto";
            break;
    }

    $subject = "Estado de ticket modificado";
    $origin = "no-reply@soporte.empresa.com";
    $msgBody = "El estado de su ticket '".$ticketSubject."' ha sido modificado a '".$state."'.";

    enviarEmail($destinatary, $origin, $subject, $msgBody);
}
function notifAccountActivation($destinatary) {
    
    require "email.php";
    //rellena el resto de campos necesarios para enviar el email
    $subject = "Activación de cuenta";
    $origin = "";
    $msgBody = "Pulse el siguiente enlace para activar su cuenta: \n\n http://localhost/acc_activation.php";
    //envía el email
    enviarEmail($destinatary, $origin, $subject, $msgBody);
}
//FIN EMAILS

// Función que cierra la cuenta de un usuario
function close_account($email) {
        require "conection.php";
    
        $bd = new PDO(
            "mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
            $bd_config["user"],
            $bd_config["password"]);
    
        $delete = "DELETE FROM AppUser WHERE email LIKE '$email'";
        $result = $bd->query($delete);
    
        if ($result) { return true; }
        else { return false; }
}

function recover_password($email) {
    require "conection.php";

    $bd = new PDO(
        "mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]);

    $select = "SELECT email FROM AppUser WHERE email LIKE '$email'";
    $resul = $bd->query($select);

    if ($resul->rowCount() == 1) {
        $new_password = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
        $new_password_crypt = password_hash($new_password, PASSWORD_DEFAULT);

        $subject = "Recuperación de contraseña";
        $origin = "no-reply@soporte.empresa.com";
        $msgBody = "Su nueva contraseña es: $new_password \n\n Recuerde cambiarla en su próximo inicio de sesión desde su perfil. \n\n Atentamente Equipo de Soporte.";
        
        if (enviarEmail($destinatary, $origin, $subject, $msgBody)) {
            $update = "UPDATE AppUser SET passwd = '$new_password_crypt' WHERE email LIKE '$email'";
            $resul = $bd->query($update);
        }
    }
}

function change_password($new_password) {
    require "conection.php";

    $bd = new PDO(
        "mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]);
    
    $new_password_crypt = password_hash($new_password, PASSWORD_DEFAULT);

    $update = "UPDATE AppUser SET passwd = '$new_password_crypt' WHERE email LIKE '".$_SESSION["email"]."'";
    $resul = $bd->query($update);
    if ($resul) {
        return true;
    } else {
        return false;
    }   
}   
