<?php
// Funcion para iniciar sesión en la cuenta 
function login($email, $passwd) {
    require "conection.php";

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
            return TRUE;
        }   
        if($resul->rowCount() === 1){        
            return $resul->fetch();        
        } else {
            return FALSE;
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
        $attachment = $_FILES['attachment'];
        $uploadFilePath = "uploads/" . basename($attachment);
        // Asegurrarse de que la carpeta uplodas existe 
        if (!is_dir("C:/xampp/htdocs/12-tickets-tecnicos/Practica-Ev-1/uploads")) {
            mkdir("C:/xampp/htdocs/12-tickets-tecnicos/Practica-Ev-1/uploads", 0777, true);
        }

        // Decomentar esto si queremos que solo se suban determinado archivos:
        // $allowedTypes  = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        // if (!in_array($attachment['type'], $allowedTypes)) {
        //     return FALSE;
        // }

        $attachment_name = basename($attachment['name']);
        if (move_uploaded_file($attachment['tmp_name'], $uploadFilePath)) {
            $ins = "insert into ticket (subject, messBody, priority, email, state, attachment) values ('$subject', '$description', '$priority', '$email', 2, '$attachment_name')";
            echo "El archivo ha sido subido correctamente";
        } else {
            return FALSE;
        }
    } else {
        $ins = "insert into ticket (subject, messBody, priority, email, state) values ('$subject', '$description', '$priority', '$email', 2)";
        $resul = $bd->query($ins); 
    }
    if($resul){
        
        $select = "SELECT openTickets FROM AppUser WHERE email LIKE '$email'";
        $tickets = $bd->query($select);

        foreach ($tickets as $ticketNum) {
            $ticketNum["openTickets"] += 1;
        }
        
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
        }else{
            $select = $select . " AND subject LIKE '%" . $busqueda . "%'";
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

    <h3> <?= $email ?> </h3>
    <h4> <?= $sentDate ?> </h4>
    
    <?php    printSVG($state); ?>

    <div> <?= nl2br($messBody)?></div>

    <?php

if ($attachment_name != "") {
        require "file_dir.php";
        
        echo "<a href='" . $attach_directory . $attachment_name . "' class='file-open-box' target='_blank' >" . $attachment_name . "<a>";
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

// TODO: Probar la funcion
// Función que descarga un archivo adjunto
// Ej: Llamas al funcion si te entra un archivo por POST/GET y lo descargar llamando al funcion
function download_attachment($fileName) {
    // Incluir el archivo de configuración
    $filePath = "uploads/".$fileName;

    if (file_exists($filePath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        // Leer el archivo y enviarlo al navegador
        readfile($filePath);
        exit;
    } else {
        echo "El archivo no existe";
    }
}
