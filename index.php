<?php
require_once 'includes/functions.php';

// Obtener productos destacados
$productos_destacados = obtenerProductosDestacados(8);
$categorias = obtenerCategorias();

$titulo = 'Inicio';
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section bg-success text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-3">Bienvenido a Botica Olivos</h1>
                <p class="lead mb-4">Tu farmacia de confianza con más de 10 años sirviendo a la comunidad. Encuentra todos los productos que necesitas para cuidar tu salud y bienestar.</p>
                <div class="d-flex gap-3">
                    <a href="productos.php" class="btn btn-light btn-lg">
                        <i class="fas fa-pills me-2"></i>Ver Productos
                    </a>
                    <?php if (!estaAutenticado()): ?>
                    <a href="registro.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Regístrate
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-image">
                    <i class="fas fa-heartbeat" style="font-size: 15rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Servicios Destacados -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-shipping-fast fa-3x text-success mb-3"></i>
                        <h5>Envío Rápido</h5>
                        <p class="text-muted small">Delivery en 30 minutos o recojo en tienda</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                        <h5>Productos Garantizados</h5>
                        <p class="text-muted small">Medicamentos originales con garantía</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-user-md fa-3x text-success mb-3"></i>
                        <h5>Atención Especializada</h5>
                        <p class="text-muted small">Farmacéuticos certificados para asesorarte</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-mobile-alt fa-3x text-success mb-3"></i>
                        <h5>Pago Fácil</h5>
                        <p class="text-muted small">Yape, transferencia o contra entrega</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categorías Populares -->
<section class="py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold">Categorías Populares</h2>
                <p class="text-muted">Encuentra lo que necesitas por categoría</p>
            </div>
        </div>
        
        <div class="row">
            <?php foreach (array_slice($categorias, 0, 6) as $categoria): ?>
            <div class="col-lg-2 col-md-4 col-6 mb-4">
                <a href="productos.php?categoria=<?php echo $categoria['id_categoria']; ?>" 
                   class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm categoria-card">
                        <div class="card-body text-center p-3">
                            <i class="fas fa-capsules fa-2x text-success mb-2"></i>
                            <h6 class="mb-0"><?php echo htmlspecialchars($categoria['nombre_categoria']); ?></h6>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="productos.php" class="btn btn-outline-success">
                <i class="fas fa-th-large me-2"></i>Ver Todas las Categorías
            </a>
        </div>
    </div>
</section>

<!-- Productos Destacados -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold">Productos Más Populares</h2>
                <p class="text-muted">Los productos más vendidos de nuestra botica</p>
            </div>
        </div>
        
        <?php if (!empty($productos_destacados)): ?>
        <div class="row">
            <?php foreach ($productos_destacados as $producto): ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card h-100 producto-card shadow-sm">
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-pills fa-3x text-muted"></i>
                    </div>
                    
                    <?php if ($producto['stock'] == 0): ?>
                    <div class="badge bg-danger position-absolute top-0 start-0 m-2">Agotado</div>
                    <?php elseif (isset($producto['total_vendido']) && $producto['total_vendido'] > 10): ?>
                    <div class="badge bg-warning position-absolute top-0 start-0 m-2">Más Vendido</div>
                    <?php endif; ?>
                    
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h6>
                        <p class="card-text text-muted small flex-grow-1">
                            <?php echo htmlspecialchars(substr($producto['descripcion'], 0, 80)) . '...'; ?>
                        </p>
                        <div class="text-muted small mb-2">
                            <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($producto['nombre_categoria']); ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="h5 text-success fw-bold mb-0">
                                    <?php echo formatearPrecio($producto['precio']); ?>
                                </span>
                                <div class="small text-muted">Stock: <?php echo $producto['stock']; ?></div>
                            </div>
                            <?php if (estaAutenticado() && $producto['stock'] > 0): ?>
                            <button class="btn btn-success btn-sm agregar-carrito" 
                                    data-producto-id="<?php echo $producto['id_producto']; ?>">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                            <?php elseif (!estaAutenticado()): ?>
                            <a href="login.php" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-sign-in-alt"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="productos.php" class="btn btn-success btn-lg">
                <i class="fas fa-eye me-2"></i>Ver Todos los Productos
            </a>
        </div>
        
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No hay productos disponibles</h4>
            <p class="text-muted">Los productos se mostrarán una vez que sean agregados por el administrador.</p>
            
            <?php if (tienePermiso('admin')): ?>
            <a href="admin/" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Agregar Productos
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Información Adicional -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card border-0 h-100">
                    <div class="card-body">
                        <h4><i class="fas fa-clock text-success me-2"></i>Horarios de Atención</h4>
                        <ul class="list-unstyled">
                            <li><strong>Lunes a Viernes:</strong> 8:00 AM - 10:00 PM</li>
                            <li><strong>Sábados:</strong> 8:00 AM - 10:00 PM</li>
                            <li><strong>Domingos:</strong> 9:00 AM - 9:00 PM</li>
                            <li><strong>Feriados:</strong> 9:00 AM - 6:00 PM</li>
                        </ul>
                        <p class="text-muted">Delivery disponible en todos nuestros horarios</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card border-0 h-100">
                    <div class="card-body">
                        <h4><i class="fas fa-map-marker-alt text-success me-2"></i>Nuestra Ubicación</h4>
                        <address>
                            <strong>Botica Olivos</strong><br>
                            Av. Los Olivos 123<br>
                            Los Olivos, Lima<br>
                            Perú
                        </address>
                        <p>
                            <i class="fas fa-phone me-2"></i><strong>Teléfono:</strong> (01) 234-5678<br>
                            <i class="fas fa-whatsapp me-2"></i><strong>WhatsApp:</strong> 987-654-321
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Funcionalidad para agregar al carrito
document.addEventListener('DOMContentLoaded', function() {
    // Agregar al carrito
    document.querySelectorAll('.agregar-carrito').forEach(button => {
        button.addEventListener('click', function() {
            const productoId = this.dataset.productoId;
            agregarAlCarrito(productoId);
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>