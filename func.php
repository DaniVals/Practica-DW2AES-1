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
function create_ticket($subject, $description, $priority, $email) {
    require "conection.php";

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

    $sql = "INSERT INTO AppUser(email,passwd,name,lastname,rol)
            VALUES('$email','$passwd_crypt','$name','$lastname',$rol)";
    
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
function printTickets(?int $id_ticket = -1) {
    
    require "conection.php";

    $bd = new PDO(
        "mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]
    );
    

    $select = 'SELECT idTicket, email, subject, messBody, state, sentDate FROM ticket WHERE 1 = 1';

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
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["busqueda"])) {
        $select = $select . " AND subject LIKE '%" . $_POST["busqueda"] . "%'";
    }

    if ($id_ticket < 0) {
        // ASK deberia ordenarlo por si esta completado o no?
        $select = $select . " ORDER BY priority, sentDate";
    }


    // ==== hacer querry ====
    $tickets = $bd->query($select);
    if($tickets->rowCount() <= 0){

        if (0 <= $id_ticket) {
            // mensaje de error si no encuentra el ticket
            echo "<p id='not-found-message'> No existe ese ticket </p>";
            
        }else{
            // mensaje de error si no hay tickets
            echo "<p id='not-found-message'> No tienes tickets creados </p>";
        }

        return; // si no encuentra nada, acabar la funcion (aunque tampoco entraria el el bucle for)
    }
    

    // ==== imprimir un ticket por cada ticket encontrado ====
    foreach ($tickets as $ticket) {

        echo    '<div class="ticket">';

        if (0 <= $id_ticket) {
            echo        '<h2> '.$ticket["subject"].' </h2>';
        }else{
            echo        '<h2> <a href="ticket.php?id='.$ticket["idTicket"].'">'.$ticket["subject"].'</a> </h2>';
        }
        echo        '<h3>'.$ticket["email"].'</h3>';
        echo        '<h4>'.$ticket["sentDate"].'</h4>';
        
        printSVG($ticket["state"]);

        echo        '<div>'.$ticket["messBody"].'</div>';

        
        // cuando estas en el ticket y no en la preview `ticket.php`
        if (0 <= $id_ticket) {
            // cualquier otro rol
            $select = 'SELECT email, messBody, ansDate FROM answer WHERE idTicket =' . $_GET["id"];
            $respuestas = $bd->query($select);
            
            foreach ($respuestas as $respuesta) {
                echo '<hr>';
                echo '<h3>'.$respuesta["email"].'</h3>';
                echo '<h4>'.$respuesta["ansDate"].'</h4>';
                echo '<div>'.$respuesta["messBody"].'</div>';
            }
            
            // ==== añadir el textarea para escribir un comentario ====
            // TODO posibilidad de cambiar el estado del ticket
            ?>
            <hr>
            <form action="" method="post">
            <textarea name="ans" placeholder="Respuesta..." required></textarea>
            
            <?php
            // cambiar estado si es tecnico
            if ($_SESSION["rol"] == 1) {
            ?>

                <select name="changeStatus">
                    <option value="0">-- Cambiar estado --</option>
                    <option value="1">Resolver</option>
                    <option value="2">En proceso</option>
                    <option value="3">Cerrar</option>
                </select>

            <?php
            }
            ?>

            <br>
            <input type="submit" value="responder">
            </form>

            <?php
        }
        echo    '</div>'; // cerrar el div del ticket
    }
}

//Función que cuenta cuántos tickets tiene una persona
function tooManyTickets($email) {
    
    require "conection.php";

    $bd = new PDO(
        "mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]);
    
    $select = "SELECT openTickets FROM AppUser WHERE email LIKE '$email'";
    $tickets = $bd->query($select);

    foreach ($tickets as $ticketNum) {
        
        if ($ticketNum>=3) { return TRUE }
        else { return FALSE };
    }

}