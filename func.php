<?php
function create_ticket($subject, $description, $priority, $user) {
    require_once "conection.php";

    $bd = new PDO("mysql:dbname=".$bd_config["nombrebd"].";host=".$bd_config["ip"], 
                    $bd_config["usuario"],
                    $bd_config["clave"]);
    $ins = "insert into tickets (subject, description, priority, user) values ('$subject', '$description', '$priority', '$user')";
    $resul = $bd->query($ins);    
    if($resul->rowCount() === 1){        
        return $resul->fetch();        
    }else{
        return FALSE;
    }
}
