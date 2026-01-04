<?php
require_once 'includes/functions.php';

// Si ya está autenticado, redirigir
if (estaAutenticado()) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_POST) {
    $correo = sanitizarInput($_POST['correo']);
    $contraseña = $_POST['contraseña'];
    
    if (iniciarSesion($correo, $contraseña)) {
        // Redirigir según el rol
        $rol = $_SESSION['usuario_rol'];
        switch ($rol) {
            case 'admin':
                header("Location: admin/");
                break;
            case 'empleado':
                header("Location: empleado/");
                break;
            default:
                header("Location: index.php");
                break;
        }
        exit();
    } else {
        $error = 'Credenciales incorrectas. Verifique su correo y contraseña.';
    }
}

$titulo = 'Iniciar Sesión';
require_once 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-header text-center bg-success text-white">
                    <h4><i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="correo" class="form-label">
                                <i class="fas fa-envelope me-1"></i>Correo Electrónico
                            </label>
                            <input type="email" class="form-control" id="correo" name="correo" 
                                   value="<?php echo $_POST['correo'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="contraseña" class="form-label">
                                <i class="fas fa-lock me-1"></i>Contraseña
                            </label>
                            <input type="password" class="form-control" id="contraseña" name="contraseña" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="recordar">
                            <label class="form-check-label" for="recordar">
                                Recordarme
                            </label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                            </button>
                        </div>
                    </form>
                    
                    <hr>
                    
                    <div class="text-center">
                        <p class="mb-2">¿No tienes una cuenta?</p>
                        <a href="registro.php" class="btn btn-outline-success">
                            <i class="fas fa-user-plus me-2"></i>Crear Cuenta
                        </a>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="#" class="text-muted text-decoration-none small">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Demo Users Info -->
            <div class="card mt-3 border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Usuarios Demo</h6>
                </div>
                <div class="card-body small">
                    <p class="mb-1"><strong>Admin:</strong> admin@boticaolivos.pe / admin123</p>
                    <p class="mb-1"><strong>Empleado:</strong> empleado@boticaolivos.pe / emp123</p>
                    <p class="mb-0"><strong>Cliente:</strong> cliente@boticaolivos.pe / cliente123</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>