<?php
require_once 'includes/functions.php';
redirigirSiNoAutenticado('login.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$metodo_pago = $_POST['metodo_pago'] ?? 'yape';
$total = floatval($_POST['total'] ?? 0);
$cliente_numero = trim($_POST['cliente_numero'] ?? '');
$cliente_nombre = trim($_POST['cliente_nombre'] ?? '');

// Validaciones básicas
$errores = [];
if (empty($cliente_numero) || empty($cliente_nombre)) {
    $errores[] = 'Completa tus datos de contacto.';
}
if (!isset($_FILES['evidence']) || $_FILES['evidence']['error'] !== UPLOAD_ERR_OK) {
    $errores[] = 'Debes subir la captura del pago.';
}

if (!empty($errores)) {
    $_SESSION['mensaje'] = implode(' ', $errores);
    $_SESSION['mensaje_tipo'] = 'danger';
    header('Location: checkout.php');
    exit;
}

// Manejo de subida de archivo
$uploadDir = __DIR__ . '/uploads/payments/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$file = $_FILES['evidence'];
$allowed = ['image/jpeg','image/png','image/jpg'];
if (!in_array($file['type'], $allowed)) {
    $_SESSION['mensaje'] = 'Formato de imagen no permitido. Usa JPG o PNG.';
    $_SESSION['mensaje_tipo'] = 'danger';
    header('Location: checkout.php');
    exit;
}

if ($file['size'] > 3 * 1024 * 1024) { // 3MB
    $_SESSION['mensaje'] = 'Archivo demasiado grande. Máx 3MB.';
    $_SESSION['mensaje_tipo'] = 'danger';
    header('Location: checkout.php');
    exit;
}

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'pay_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
$target = $uploadDir . $filename;
if (!move_uploaded_file($file['tmp_name'], $target)) {
    $_SESSION['mensaje'] = 'Error al subir el archivo.';
    $_SESSION['mensaje_tipo'] = 'danger';
    header('Location: checkout.php');
    exit;
}

// Guardar pedido (tabla Pedidos)
try {
    $conn = obtenerConexion(); // PDO
    $conn->beginTransaction();

    // 1) Crear pedido
    $sqlPedido = "INSERT INTO Pedidos (id_usuario, total, direccion_entrega, metodo_pago) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlPedido);
    $direccion = null;
    $stmt->execute([$usuario_id, $total, $direccion, $metodo_pago]);
    $id_pedido = $conn->lastInsertId();

    // 2) Crear detalle pedido desde carrito
    $items = obtenerItemsCarrito($usuario_id);
    $sqlDetalle = "INSERT INTO DetallePedido (id_pedido, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
    $stmtDetalle = $conn->prepare($sqlDetalle);

    foreach ($items as $it) {
        $stmtDetalle->execute([$id_pedido, $it['id_producto'], $it['cantidad'], $it['precio']]);

        // Opcional: reducir stock
        $sqlStock = "UPDATE Productos SET stock = stock - ? WHERE id_producto = ?";
        $stmtStock = $conn->prepare($sqlStock);
        $stmtStock->execute([$it['cantidad'], $it['id_producto']]);
    }

    // 3) Guardar pago pendiente con evidencia
    $sqlPago = "INSERT INTO Pagos (id_pedido, monto, metodo_pago, estado_pago, evidence_url, fecha_pago) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmtPago = $conn->prepare($sqlPago);
    $evidence_url = 'uploads/payments/' . $filename; // ruta pública relativa
    $stmtPago->execute([$id_pedido, $total, $metodo_pago, 'pendiente', $evidence_url]);

    // 4) Vaciar carrito
    vaciarCarrito($usuario_id);

    $conn->commit();

    $_SESSION['mensaje'] = 'Comprobante enviado. Un administrador verificará el pago.';
    $_SESSION['mensaje_tipo'] = 'success';
    header('Location: index.php');
    exit;

} catch (Exception $e) {
    if ($conn && $conn->inTransaction()) $conn->rollBack();
    error_log("Error procesar_pago: " . $e->getMessage());
    $_SESSION['mensaje'] = 'Error al procesar el pago. Intenta más tarde.';
    $_SESSION['mensaje_tipo'] = 'danger';
    header('Location: checkout.php');
    exit;
}
