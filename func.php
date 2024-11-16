<?php
// Funcion para iniciar sesión en la cuenta 
function login($email, $passwd) {
    require "conection.php";

    //Reisar que el correo sea correcto
    $email_regex = "/^[a-z]+@[a-z]*\.?[a-z]+\.com$/";
    if (!preg_match($email_regex, $email)) {
        echo "<p>Escriba un correo valido</p>";
        return false;
    }
    // Revisar si la contraseña no esta vacía
    if ($passwd == "") {
        return false;
    }

    $bd = new PDO("mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]);

    $sel = "SELECT passwd, activated FROM AppUser WHERE email LIKE '$email'";
    $res = $bd->query($sel);
    foreach ($res as $row) {
        $passwd_crypt = $row['passwd'];
        $active = $row['activated'];
        
        if ($active == 0) {
            echo "<p>La cuenta no está activada. Por favor, revise su correo electronico para activarla.</p>";
            return false;
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
                echo "<p>Usuario no encontrado</p>";
                return false;
            }
        } else {
            echo "<p>Contraseña incorrecta</p>";
            return false;
        }
    }
    echo "<p>Correo incorrecto</p>";
    return false;
}

// Función que crea un ticket en la base de datos
function create_ticket($subject, $description, $attachment, $priority, $email) {
    require "conection.php";

    $bd = new PDO("mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]);
    
    if (!empty($attachment['name'])) {

        require "file_dir.php";

        // sacar la id del ultimo ticket
        $select_last_tickets = "SELECT idTicket from ticket ORDER BY idTicket DESC LIMIT 1";
        $last_tickets = $bd->query($select_last_tickets);
        $next_id;
        foreach ($last_tickets as $last_ticket) {
            $next_id = $last_ticket['idTicket'] + 1;
        }

        // subir el archivo y hacer insert si se sube correctamente
        $attach_id_name = "$next_id-" . $attachment['name'];
        if (uploadFile($attachment['tmp_name'], $attach_directory, $attach_id_name)) {
            $attachment_name = basename($attachment['name']);
            $ins = "INSERT INTO ticket (subject, messBody, priority, email, state, attachment) VALUES ('$subject', '$description', '$priority', '$email', 2, '$attach_id_name')";
            echo "<p>El archivo ha sido subido correctamente</p>";
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
function deleteFile($file) {

    // Verifica si el archivo existe
    if (file_exists($file)) {
        // Intenta eliminar el archivo
        if (unlink($file)) {
            return true;
        }
    }
    
    return false;
}
// generar string del nombre del archivo de la foto
function returnPPstring($name) {
    require "file_dir.php";
    if (file_exists($profile_picture_directory . $name . ".png")) {
        return      $profile_picture_directory . $name . ".png";
    } else {
        return $profile_picture_directory . "defaultPP.png";
    }
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

    $sql = "INSERT INTO AppUser(email,passwd,name,lastname,rol,openTickets, activated)
    VALUES('$email','$passwd_crypt','$name','$lastname',$rol,0,0)";

    try {
        $result = $bd->query($sql);
        // Enviar un correo de activación
        notifAccountActivation($email); 
        header("Location: login.php");
    }
    catch (Exception $e) {
        echo "<p>Problema al registrar al usuario, inténtelo de nuevo</p>";
        echo $e->getMessage();
    }

}

// Función para mostrar el SVG recibiendo de argumento el estado del ticket
function printSVG($state) {

    switch ($state) {
        case '1':
            // solved
            echo '<svg width="320px" height="320px" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12ZM16.0303 8.96967C16.3232 9.26256 16.3232 9.73744 16.0303 10.0303L11.0303 15.0303C10.7374 15.3232 10.2626 15.3232 9.96967 15.0303L7.96967 13.0303C7.67678 12.7374 7.67678 12.2626 7.96967 11.9697C8.26256 11.6768 8.73744 11.6768 9.03033 11.9697L10.5 13.4393L12.7348 11.2045L14.9697 8.96967C15.2626 8.67678 15.7374 8.67678 16.0303 8.96967Z" fill="#18dd1d"></path> </g></svg>';
            // echo '<svg><circle r="10" cx="10" cy="10" fill="lime"/></svg>';
            break;
        case '2':
            // in progress
            echo '<svg width="256px" height="256px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>arrow-right-circle</title> <desc>Created with Sketch Beta.</desc> <defs> </defs> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage"> <g id="Icon-Set-Filled" sketch:type="MSLayerGroup" transform="translate(-310.000000, -1089.000000)" fill="#ff7800"> <path d="M332.535,1105.88 L326.879,1111.54 C326.488,1111.93 325.855,1111.93 325.465,1111.54 C325.074,1111.15 325.074,1110.51 325.465,1110.12 L329.586,1106 L319,1106 C318.447,1106 318,1105.55 318,1105 C318,1104.45 318.447,1104 319,1104 L329.586,1104 L325.465,1099.88 C325.074,1099.49 325.074,1098.86 325.465,1098.46 C325.855,1098.07 326.488,1098.07 326.879,1098.46 L332.535,1104.12 C332.775,1104.36 332.85,1104.69 332.795,1105 C332.85,1105.31 332.775,1105.64 332.535,1105.88 L332.535,1105.88 Z M326,1089 C317.163,1089 310,1096.16 310,1105 C310,1113.84 317.163,1121 326,1121 C334.837,1121 342,1113.84 342,1105 C342,1096.16 334.837,1089 326,1089 L326,1089 Z" id="arrow-right-circle" sketch:type="MSShapeGroup"> </path> </g> </g> </g></svg>';
            break;
        case '3':
            // closed
            echo '<svg width="256px" height="256px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>cross-circle</title> <desc>Created with Sketch Beta.</desc> <defs> </defs> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage"> <g id="Icon-Set-Filled" sketch:type="MSLayerGroup" transform="translate(-570.000000, -1089.000000)" fill="#e01b24"> <path d="M591.657,1109.24 C592.048,1109.63 592.048,1110.27 591.657,1110.66 C591.267,1111.05 590.633,1111.05 590.242,1110.66 L586.006,1106.42 L581.74,1110.69 C581.346,1111.08 580.708,1111.08 580.314,1110.69 C579.921,1110.29 579.921,1109.65 580.314,1109.26 L584.58,1104.99 L580.344,1100.76 C579.953,1100.37 579.953,1099.73 580.344,1099.34 C580.733,1098.95 581.367,1098.95 581.758,1099.34 L585.994,1103.58 L590.292,1099.28 C590.686,1098.89 591.323,1098.89 591.717,1099.28 C592.11,1099.68 592.11,1100.31 591.717,1100.71 L587.42,1105.01 L591.657,1109.24 L591.657,1109.24 Z M586,1089 C577.163,1089 570,1096.16 570,1105 C570,1113.84 577.163,1121 586,1121 C594.837,1121 602,1113.84 602,1105 C602,1096.16 594.837,1089 586,1089 L586,1089 Z" id="cross-circle" sketch:type="MSShapeGroup"> </path> </g> </g> </g></svg>';
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

function printTicketParameters($subject, $messBody, $email, $state, $sentDate, ?int $id_ticket = -1, ?string $attachment_name = "", ?int $priority = -1) {
    
    // foto de perfil
    require "file_dir.php";
    echo '<a href="profile.php?email='. $email .'"> <img src="' . returnPPstring($email) . '" alt="foto de perfil"></a>';

    // h2 opcional usando ""
    if ($subject != "") {
        echo "<h2>";

        // imprimir H2 con enlace si le pasas un parametro de $id_ticket valido (0<id)
        if (0 < $id_ticket) { echo '<a href="ticket.php?id='.$id_ticket.'">';}
        echo $subject;
        echo "<span>";
        switch ($priority) {
            // very high
            case 1:
                echo "++";
                break;

            // high
            case 2:
                echo "+";
                break;

            // standar
            case 3:
                echo "•";
                break;
                
            // low
            case 4:
                echo "-";
                break;
            
            // no hace nada si es -1
            default:
                break;
        }
        echo "</span>";

        if (0 < $id_ticket) { echo '</a>';}
        
        echo "</h2>";
    }
    ?>

    <h3> <a href=<?="profile.php?email=$email" ?> class="linkemail"><?= $email ?> </a></h3><br>
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
function notifAccountActivation($email) {
    
    require "email.php";
    require "conection.php";
 
    $token = bin2hex(random_bytes(32)); //Token de 64 caracteres
    $expiration = date("Y-m-d H:i:s", strtotime("+1 day")); //Fecha de expiración del token
    
    $bd = new PDO("mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]);
    
    // Buscar el id del usuario
    $select = "SELECT idUser FROM AppUser WHERE email LIKE '$email'";
    $resul = $bd->query($select);
    foreach ($resul as $row) {
        $idUser = $row['idUser'];
    }
    $sql = "INSERT INTO accountactivation (idUser, token, expiration) VALUES ('$idUser', '$token', '$expiration')";
    $bd->query($sql);


    $host = "http://localhost/acc_activation.php";
    $url = $host."?token=".$token;

    //rellena el resto de campos necesarios para enviar el email
    $subject = "Activación de cuenta";
    $origin = "no-reply@soporte.empresa.com";
    $msgBody = "Pulse el siguiente enlace para activar su cuenta: \n\n $url";
    //envía el email
    enviarEmail($email, $origin, $subject, $msgBody);
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
    require "email.php";

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
        
        if (enviarEmail($email, $origin, $subject, $msgBody)) {
            $update = "UPDATE AppUser SET passwd = '$new_password_crypt' WHERE email LIKE '$email'";
            $resul = $bd->query($update);
            set_passwd_change($email, 1);
            return true;
        }
    }
    return false;
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

function set_passwd_change($email, $needChange) {
    require "conection.php";

    $bd = new PDO("mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]);

    $select = "SELECT email FROM need_passwd_change WHERE email LIKE '$email'";
    $resul = $bd->query($select);
    // si ya existe ese correo, hacer un update, sino un insert
    if ($resul->rowCount() <= 0) {

        $select = "SELECT idUser FROM AppUser WHERE email LIKE '$email'";
        $resul = $bd->query($select);
        foreach ($resul as $id) {
            $ins = "INSERT need_passwd_change VALUES (" . $id['idUser'] . " , '$email', $needChange)";
            $bd->query($ins);
        }

    }else {
        $upd = "UPDATE need_passwd_change SET needChange = $needChange WHERE email LIKE '$email'";
        $bd->query($upd);
    }
}

function check_passwd_change($email) {
    require "conection.php";

    $bd = new PDO("mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]);
    $sel = "SELECT needChange FROM need_passwd_change WHERE email LIKE '$email'";
    $resul = $bd->query($sel);
    foreach ($resul as $row) {
        $row['needChange'];
        if ($row['needChange'] == 1) {
            return true;
        }
    }
    return false;
}
