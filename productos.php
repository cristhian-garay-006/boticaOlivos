<?php
require_once 'includes/functions.php';

// Obtener la categoría desde la URL si existe
$categoria_id = isset($_GET['categoria']) ? (int)$_GET['categoria'] : null;

// Obtener productos filtrados
$productos = obtenerProductos(null, $categoria_id);
$categorias = obtenerCategorias();

$titulo = 'Productos';
require_once 'includes/header.php';
?>

<!-- Título de la página -->
<section class="py-5 text-center bg-success text-white">
    <div class="container">
        <h1 class="fw-bold">Nuestros Productos</h1>
        <?php if ($categoria_id): ?>
            <?php
            $categoriaActual = array_filter($categorias, fn($c) => $c['id_categoria'] == $categoria_id);
            $nombreCategoria = $categoriaActual ? array_values($categoriaActual)[0]['nombre_categoria'] : '';
            ?>
            <p class="lead">Categoría: <?php echo htmlspecialchars($nombreCategoria); ?></p>
        <?php else: ?>
            <p class="lead">Todos los productos disponibles</p>
        <?php endif; ?>
    </div>
</section>

<!-- Filtro de categorías -->
<section class="py-3 bg-light">
    <div class="container d-flex flex-wrap gap-2">
        <a href="productos.php" class="btn btn-outline-success btn-sm">Todas las categorías</a>
        <?php foreach ($categorias as $cat): ?>
            <a href="productos.php?categoria=<?php echo $cat['id_categoria']; ?>" class="btn btn-outline-success btn-sm">
                <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Lista de productos -->
<section class="py-5">
    <div class="container">
        <?php if (!empty($productos)): ?>
            <div class="row">
                <?php foreach ($productos as $producto): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100 producto-card shadow-sm position-relative">
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
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <!-- Selector de cantidad -->
                                    <div class="input-group input-group-sm" style="width: 110px;">
                                        <button class="btn btn-outline-secondary btn-cantidad" data-action="menos" data-producto-id="<?php echo $producto['id_producto']; ?>">-</button>
                                        <input type="number" class="form-control cantidad-input" value="1" min="1" data-producto-id="<?php echo $producto['id_producto']; ?>" style="text-align: center;">
                                        <button class="btn btn-outline-secondary btn-cantidad" data-action="mas" data-producto-id="<?php echo $producto['id_producto']; ?>">+</button>
                                    </div>

                                    <!-- Botón de carrito -->
                                    <?php if (estaAutenticado() && $producto['stock'] > 0): ?>
                                        <button class="btn btn-success btn-sm agregar-carrito" 
                                                data-producto-id="<?php echo $producto['id_producto']; ?>" style="margin-left: 5px;">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    <?php elseif (!estaAutenticado()): ?>
                                        <a href="login.php" class="btn btn-outline-success btn-sm" style="margin-left: 5px;">
                                            <i class="fas fa-sign-in-alt"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <div class="mt-2">
                                    <span class="h5 text-success fw-bold"><?php echo formatearPrecio($producto['precio']); ?></span>
                                    <div class="small text-muted">Stock: <?php echo $producto['stock']; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No hay productos disponibles</h4>
                <p class="text-muted">Los productos se mostrarán una vez que sean agregados por el administrador.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar clicks en + y - para cantidad
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-cantidad')) {
            const input = document.querySelector(`.cantidad-input[data-producto-id="${e.target.dataset.productoId}"]`);
            let cantidad = parseInt(input.value);

            if (e.target.dataset.action === 'mas') {
                cantidad++;
            } else if (e.target.dataset.action === 'menos' && cantidad > 1) {
                cantidad--;
            }

            input.value = cantidad;

            if (window.location.pathname.includes('carrito.php')) {
                actualizarCantidadCarrito(e.target.dataset.productoId, cantidad);
            }
        }
    });

    // Manejar cambio manual en input de cantidad
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('cantidad-input')) {
            const productoId = e.target.dataset.productoId;
            const cantidad = parseInt(e.target.value);
            if (cantidad > 0) {
                actualizarCantidadCarrito(productoId, cantidad);
            }
        }
    });

    // Agregar al carrito
    document.querySelectorAll('.agregar-carrito').forEach(button => {
        button.addEventListener('click', function() {
            const productoId = this.dataset.productoId;
            const cantidadInput = document.querySelector(`.cantidad-input[data-producto-id="${productoId}"]`);
            const cantidad = parseInt(cantidadInput.value) || 1;
            agregarAlCarrito(productoId, cantidad);
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
