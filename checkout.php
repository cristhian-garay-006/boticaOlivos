<?php
require_once 'includes/functions.php';

// Verificar autenticación
redirigirSiNoAutenticado('login.php');

$usuario_id = $_SESSION['usuario_id'];
$items_carrito = obtenerItemsCarrito($usuario_id);
$total_carrito = calcularTotalCarrito($usuario_id);

$titulo = 'Checkout';
$breadcrumbs = [
    ['texto' => 'Checkout']
];

// Si el carrito está vacío, redirigir
if (empty($items_carrito)) {
    header("Location: carrito.php");
    exit;
}

// Generar token único para esta orden (puedes guardar en BD si quieres)
$token = bin2hex(random_bytes(6));
$monto = number_format($total_carrito, 2, '.', '');
$pay_url = "http://{$_SERVER['HTTP_HOST']}/BOTICAolivos/pay.php?m={$monto}&t={$token}";

// Opcional: guardar el token en BD para validar pago luego
// guardarOrdenTemporal($usuario_id, $items_carrito, $total_carrito, $token);

require_once 'includes/header.php';
?>

<div class="container py-5">
    <h2 class="mb-4"><i class="fas fa-credit-card me-2"></i>Checkout</h2>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Resumen de tu pedido</h5>
                    <ul class="list-group mb-3">
                        <?php foreach ($items_carrito as $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($item['nombre']); ?> x <?php echo $item['cantidad']; ?>
                                <span>S/ <?php echo number_format($item['subtotal'], 2); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong>S/ <?php echo $monto; ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Pagar con Yape</h5>
                    <p>Monto exacto: <strong>S/ <?php echo $monto; ?></strong></p>

                    <!-- Botón / QR dinámico -->
                    <a href="<?php echo $pay_url; ?>" class="btn btn-success btn-lg mb-3" target="_blank">
                        <i class="fas fa-qrcode me-2"></i>Generar QR de Pago
                    </a>

                    <p class="small text-muted">
                        Al abrir el enlace, verás el número de Yape de la botica y un QR para escanear.
                        También podrás subir tu comprobante de pago.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
