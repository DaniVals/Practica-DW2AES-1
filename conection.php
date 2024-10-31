<?php
    $cadena_conexion = 'mysql:dbname=projectdwes_1term;host=127.0.0.1';
    $usuario = 'root';
    $clave = '';

    // El try-catch es opcional
    try {
        $bd = new PDO($cadena_conexion, $usuario, $clave);
    } catch (PDOException $e) {
        echo 'Error con la base de datos: ' . $e->getMessage();
    } 
