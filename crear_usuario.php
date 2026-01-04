<?php
require_once 'includes/functions.php'; // o tu archivo de conexión a la DB

// Datos del usuario a crear
$nombre = "cristhian";
$apellido = "Garay";
$correo = "cristhian@gmail.com";
$contraseña = "123456"; // contraseña en texto plano, se encripta abajo
$rol = "cliente"; // opcional: admin o cliente

// Hashear la contraseña
$contraseñaHash = password_hash($contraseña, PASSWORD_DEFAULT);

try {
    $db = obtenerConexion(); // función que devuelve tu objeto PDO o mysqli
    
    $sql = "INSERT INTO Usuarios (nombre, apellido, correo, contraseña, rol)
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$nombre, $apellido, $correo, $contraseñaHash, $rol]);
    
    echo "Usuario creado correctamente con correo: $correo";
} catch (Exception $e) {
    echo "Error al crear usuario: " . $e->getMessage();
}
?>
