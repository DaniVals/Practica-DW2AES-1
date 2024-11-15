<?php
require 'conexion.php'; // Archivo de conexión a la base de datos

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Buscar el token en la base de datos
    $sql = "SELECT idUser, expiration FROM accountactivation WHERE token = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();

    foreach ($result as $row) {
        $user_id = $row['idUser'];
        $expiracion = $row['expiration'];
    }

    // Verificar si el token no ha expirado
    if (new DateTime() < new DateTime($expiracion)) {
        // Actualizar la cuenta del usuario
        $update_sql = "UPDATE appuser SET activated = 1 WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param('i', $user_id);
        $update_stmt->execute();

        // Eliminar el token después de usarlo
        $delete_sql = "DELETE FROM accountactivation WHERE token = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param('s', $token);
        $delete_stmt->execute();

        echo "Tu cuenta ha sido activada con éxito.";
    } else {
        echo "El enlace de activación ha expirado.";
    }
} else {
    echo "No se proporcionó un token.";
}
