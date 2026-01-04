<?php
// crear_admin.php

// --- CONFIGURACIÓN DE CONEXIÓN ---
$host = "localhost";
$dbname = "boticaolivosdb"; // tu base de datos
$username = "root";
$password = ""; // deja  vacío si no tienes contraseña en XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- DATOS DEL ADMINISTRADOR ---
    $nombre = "Administrador";
    $apellido = "Principal";
    $correo = "admin@boticaolivos.com";
    $clave_plana = "admin123"; // cámbiala luego
    $clave_hash = password_hash($clave_plana, PASSWORD_BCRYPT);
    $rol = "admin";

    // --- CREAR ADMIN ---
    $sql = "INSERT INTO usuarios (nombre, apellido, correo, contraseña, rol, fecha_registro)
            VALUES (:nombre, :apellido, :correo, :clave, :rol, NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':clave', $clave_hash);
    $stmt->bindParam(':rol', $rol);

    $stmt->execute();

    echo "<h2>✅ Usuario administrador creado correctamente.</h2>";
    echo "<p>Usuario: <b>$correo</b></p>"; 
    echo "<p>Contraseña: <b>$clave_plana</b></p>";

} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo "<h2>⚠️ El usuario administrador ya existe.</h2>";
    } else {
        echo "❌ Error: " . $e->getMessage();
    }
}
?>
