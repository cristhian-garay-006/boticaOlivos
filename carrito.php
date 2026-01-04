<?php
require_once 'includes/functions.php';

// Verificar autenticación
redirigirSiNoAutenticado('login.php');

$usuario_id = $_SESSION['usuario_id'];
$items_carrito = obtenerItemsCarrito($usuario_id);
$total_carrito = calcularTotalCarrito($usuario_id);

$titulo = 'Carrito de Compras';
$breadcrumbs = [
    ['texto' => 'Carrito de Compras']
];

require_once 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Header del Carrito -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 fw-bold"><i class="fas fa-shopping-cart me-2"></i>Mi Carrito</h1>
                    <p class="text-muted mb-0">Revisa tus productos antes de continuar con la compra</p>
                </div>
                <div>
                    <a href="productos.php" class="btn btn-outline-success">
                        <i class="fas fa-arrow-left me-2"></i>Seguir Comprando
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (!empty($items_carrito)): ?>
    <div class="row">
        <!-- Items del Carrito -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Productos en tu Carrito</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="carrito-items">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th width="120">Precio</th>
                                    <th width="120">Cantidad</th>
                                    <th width="120">Subtotal</th>
                                    <th width="80">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items_carrito as $item): ?>
                                <tr data-producto-id="<?php echo $item['id_producto']; ?>">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                 style="width: 60px; height: 60px;">
                                                <i class="fas fa-pills text-muted"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($item['nombre']); ?></h6>
                                                <small class="text-muted">
                                                    Stock disponible: <?php echo $item['stock']; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="fw-bold" data-precio="<?php echo $item['precio']; ?>">
                                            <?php echo formatearPrecio($item['precio']); ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="input-group input-group-sm" style="width: 100px;">
                                            <button class="btn btn-outline-secondary" type="button" 
                                                    onclick="cambiarCantidad(<?php echo $item['id_producto']; ?>, -1)">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" class="form-control text-center cantidad-input" 
                                                   value="<?php echo $item['cantidad']; ?>" 
                                                   min="1" max="<?php echo $item['stock']; ?>"
                                                   data-producto-id="<?php echo $item['id_producto']; ?>">
                                            <button class="btn btn-outline-secondary" type="button" 
                                                    onclick="cambiarCantidad(<?php echo $item['id_producto']; ?>, 1, <?php echo $item['stock']; ?>)">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="fw-bold subtotal">
                                            <?php echo formatearPrecio($item['subtotal']); ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <button class="btn btn-outline-danger btn-sm" 
                                                onclick="eliminarDelCarrito(<?php echo $item['id_producto']; ?>)"
                                                title="Eliminar producto">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <button class="btn btn-outline-warning" onclick="vaciarCarrito()">
                            <i class="fas fa-trash-alt me-2"></i>Vaciar Carrito
                        </button>
                        <span class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Los precios incluyen IGV
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Resumen del Pedido -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Resumen del Pedido</h5>
                </div>
                <div class="card-body carrito-total">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="carrito-subtotal"><?php echo formatearPrecio($total_carrito); ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Envío:</span>
                        <span class="text-success">Gratis</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>IGV (18%):</span>
                        <span>Incluido</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong class="text-success h5" id="carrito-total"><?php echo formatearPrecio($total_carrito); ?></strong>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="checkout.php" class="btn btn-success btn-lg">
                            <i class="fas fa-credit-card me-2"></i>Proceder al Pago
                        </a>
                        <button class="btn btn-outline-success" onclick="guardarParaLuego()">
                            <i class="fas fa-bookmark me-2"></i>Guardar para Luego
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Información Adicional -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6><i class="fas fa-shield-alt me-2 text-success"></i>Compra Segura</h6>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li><i class="fas fa-check text-success me-2"></i>Productos garantizados</li>
                        <li><i class="fas fa-check text-success me-2"></i>Envío gratis en Lima</li>
                        <li><i class="fas fa-check text-success me-2"></i>Pago seguro con Yape</li>
                        <li><i class="fas fa-check text-success me-2"></i>Devoluciones fáciles</li>
                    </ul>
                </div>
            </div>
            
            <!-- Métodos de Pago -->
            <div class="card mt-3">
                <div class="card-body text-center">
                    <h6><i class="fas fa-credit-card me-2"></i>Métodos de Pago</h6>
                    <div class="d-flex justify-content-center gap-2">
                        <div class="badge bg-success p-2">
                            <i class="fas fa-mobile-alt"></i> Yape
                        </div>
                        <div class="badge bg-primary p-2">
                            <i class="fas fa-university"></i> Transferencia
                        </div>
                        <div class="badge bg-info p-2">
                            <i class="fas fa-money-bill"></i> Contra Entrega
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php else: ?>
    <!-- Carrito Vacío -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Tu carrito está vacío</h4>
                    <p class="text-muted mb-4">¡Agrega algunos productos y vuelve aquí para revisar tu pedido!</p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="productos.php" class="btn btn-success btn-lg">
                            <i class="fas fa-pills me-2"></i>Explorar Productos
                        </a>
                        <a href="index.php" class="btn btn-outline-success btn-lg">
                            <i class="fas fa-home me-2"></i>Ir al Inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Productos Sugeridos -->
    <div class="row mt-5">
        <div class="col-12">
            <h4><i class="fas fa-lightbulb me-2"></i>También te puede interesar</h4>
            
            <?php 
            $productos_sugeridos = obtenerProductosDestacados(4);
            if (!empty($productos_sugeridos)):
            ?>
            <div class="row">
                <?php foreach ($productos_sugeridos as $producto): ?>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card h-100 producto-card">
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                            <i class="fas fa-pills fa-2x text-muted"></i>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h6>
                            <p class="card-text text-muted small flex-grow-1">
                                <?php echo htmlspecialchars(substr($producto['descripcion'], 0, 60)) . '...'; ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h6 text-success mb-0"><?php echo formatearPrecio($producto['precio']); ?></span>
                                <?php if ($producto['stock'] > 0): ?>
                                <button class="btn btn-success btn-sm agregar-carrito" 
                                        data-producto-id="<?php echo $producto['id_producto']; ?>">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Cambiar cantidad con botones +/-
function cambiarCantidad(productoId, cambio, maxStock = null) {
    const input = document.querySelector(`input[data-producto-id="${productoId}"]`);
    const cantidadActual = parseInt(input.value);
    const nuevaCantidad = cantidadActual + cambio;
    
    if (nuevaCantidad < 1) return;
    if (maxStock && nuevaCantidad > maxStock) {
        showNotification('No hay suficiente stock disponible', 'warning');
        return;
    }
    
    input.value = nuevaCantidad;
    actualizarCantidadCarrito(productoId, nuevaCantidad);
}

// Vaciar carrito
async function vaciarCarrito() {
    if (!confirm('¿Estás seguro de vaciar todo el carrito?')) return;
    
    try {
        const response = await fetch('api/carrito.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'vaciar' })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Carrito vaciado exitosamente', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(result.message || 'Error al vaciar carrito', 'error');
        }
    } catch (error) {
        showNotification('Error de conexión', 'error');
    }
}

// Guardar para luego (placeholder)
function guardarParaLuego() {
    showNotification('Funcionalidad próximamente disponible', 'info');
}

// Actualizar totales cada vez que cambie algo
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar totales al cargar
    actualizarTotalesCarrito();
});
</script>

<?php require_once 'includes/footer.php'; ?>