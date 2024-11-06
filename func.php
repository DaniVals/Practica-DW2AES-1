<?php
// Funcion para iniciar sesión en la cuenta 
function login($email, $passw) {
    require_once "conection.php";

    $bd = new PDO("mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]);
    $ins = "select * from AppUser where email = '$email' and passwd = '$passw'";
    $resul = $bd->query($ins);
    foreach ($resul as $row) {
        // alamacenar el rol en la sesión
        $_SESSION['rol'] = $row['rol'];
        $SESSION['user'] = $row['email'];
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
    $ins = "insert into tickets values (subject, description, priority, user) values ('$subject', '$description', '$priority', '$email')";
    $resul = $bd->query($ins);    
    if($resul->rowCount() === 1){        
        return $resul->fetch();        
    }else{
        return FALSE;
    }
}

// Función que mira si ya existe ese email en la base de datos
function checkUser($user) {
    
    require_once "conection.php";
    $bd = new PDO("mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]);

    $query = "SELECT email FROM appUser WHERE email='$user'";
    $resul = $bd->query($query);

    if($resul->rowCount()<1) { return TRUE; }
    else { return FALSE; }
}
