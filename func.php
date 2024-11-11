<?php
// Funcion para iniciar sesión en la cuenta 
function login($email, $passw) {
    require_once "conection.php";

    $bd = new PDO("mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]);
    $ins = "select * from AppUser where email like '$email' and passwd like '$passw'";
    $resul = $bd->query($ins);
    foreach ($resul as $row) {
        // alamacenar el rol en la sesión
        $_SESSION['rol'] = $row['rol'];
        $_SESSION['email'] = $row['email'];
        return TRUE;
    }   
    if($resul->rowCount() === 1){        
        return $resul->fetch();        
    }else{
        return FALSE;
    }
}

// Función que crea un ticket en la base de datos
function create_ticket($subject, $description, $priority, $email) {
    require_once "conection.php";

    $bd = new PDO("mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]);
    $ins = "insert into ticket (subject, messBody, priority, email, state) values ('$subject', '$description', '$priority', '$email', 2)";
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

// Función que mira si ya existe ese email en la base de datos
function checkEmail($email) {
    
    require_once "conection.php";
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

    $lastname = $surname." ".$lastname;
    
    require_once "conection.php";
    $bd = new PDO("mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]);

    $sql = "INSERT INTO AppUser(email,passwd,name,lastname,rol)
            VALUES('$email','$passwd','$name','$lastname',$rol)";
    
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
<<<<<<< HEAD

//Función para establecer el email del usuario
function setEmail($name,$surname,$lastname,$rol) {
    
    $userName = strtolower($name).strtolower(substr($surname,0,1)).strtolower(substr($lastname,0,1));

    if ($rol==1) { return $userName."@soporte.empresa.com"; }
    else { return $userName."@empresa.com"; }
}
=======
>>>>>>> bdbe680 (fix: query, add status)
