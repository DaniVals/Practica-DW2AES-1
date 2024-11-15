<?php
require 'conection.php'; // Archivo de conexión a la base de datos

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $bd = new PDO(
        "mysql:dbname=".$bd_config["bd_name"].";host=".$bd_config["ip"], 
        $bd_config["user"],
        $bd_config["password"]
    );
    
    // Buscar el token en la base de datos
    $sql = "SELECT idUser, expiration FROM accountactivation WHERE token = '$token' LIMIT 1";
    $result = $bd->query($sql);

    if ($result) {
        foreach ($result as $row) {
            $user_id = $row['idUser'];
            $expiracion = $row['expiration'];
        }

        // Verificar si el token no ha expirado
        if (new DateTime() < new DateTime($expiracion)) {
            // Actualizar la cuenta del usuario
            $update_sql = "UPDATE appuser SET activated = 1 WHERE idUser = $user_id";
            $bd->query($update_sql);

            // Eliminar el token después de usarlo
            $delete_sql = "DELETE FROM accountactivation WHERE token = '$token'";
            $bd->query($delete_sql);

            echo "Tu cuenta ha sido activada con éxito.";
        } else {
            echo "El enlace de activación ha expirado.";
        }
    } else {
        echo "No se encontró el token.";
    }
} else {
    echo "No se proporcionó un token.";
}