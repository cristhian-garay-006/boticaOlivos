<?php
require_once 'includes/functions.php';

// Si ya está autenticado, redirigir
if (estaAutenticado()) {
    header("Location: index.php");
    exit();
}

$errores = [];
$mensaje = '';

if ($_POST) {
    // Validar campos
    $nombre = sanitizarInput($_POST['nombre']);
    $apellido = sanitizarInput($_POST['apellido']);
    $correo = sanitizarInput($_POST['correo']);
    $contraseña = $_POST['contraseña'];
    $confirmar_contraseña = $_POST['confirmar_contraseña'];
    $telefono = sanitizarInput($_POST['telefono']);
    $direccion = sanitizarInput($_POST['direccion']);
    
    // Validaciones
    if (strlen($nombre) < 2) {
        $errores[] = 'El nombre debe tener al menos 2 caracteres.';
    }
    
    if (strlen($apellido) < 2) {
        $errores[] = 'El apellido debe tener al menos 2 caracteres.';
    }
    
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo electrónico no es válido.';
    }
    
    if (strlen($contraseña) < 6) {
        $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
    }
    
    if ($contraseña !== $confirmar_contraseña) {
        $errores[] = 'Las contraseñas no coinciden.';
    }
    
    // Verificar si el correo ya existe
    if (empty($errores)) {
        $sql = "SELECT id_usuario FROM Usuarios WHERE correo = ?";
        $usuario_existente = obtenerFila($sql, [$correo]);
        
        if ($usuario_existente) {
            $errores[] = 'El correo electrónico ya está registrado.';
        }
    }
    
    // Si no hay errores, crear el usuario
    if (empty($errores)) {
        $sql = "INSERT INTO Usuarios (nombre, apellido, correo, contraseña, rol, direccion, telefono) 
                VALUES (?, ?, ?, ?, 'cliente', ?, ?)";
        
        $resultado = insertarRegistro($sql, [
            $nombre,
            $apellido, 
            $correo,
            md5($contraseña), // En producción usar password_hash()
            $direccion,
            $telefono
        ]);
        
        if ($resultado) {
            $_SESSION['mensaje'] = 'Cuenta creada exitosamente. Ya puedes iniciar sesión.';
            $_SESSION['mensaje_tipo'] = 'success';
            header("Location: login.php");
            exit();
        } else {
            $errores[] = 'Error al crear la cuenta. Intenta nuevamente.';
        }
    }
}

$titulo = 'Crear Cuenta';
require_once 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header text-center bg-success text-white">
                    <h4><i class="fas fa-user-plus me-2"></i>Crear Cuenta</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($errores)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            <?php foreach ($errores as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">
                                        <i class="fas fa-user me-1"></i>Nombre *
                                    </label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           value="<?php echo $_POST['nombre'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="apellido" class="form-label">
                                        <i class="fas fa-user me-1"></i>Apellido *
                                    </label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" 
                                           value="<?php echo $_POST['apellido'] ?? ''; ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="correo" class="form-label">
                                <i class="fas fa-envelope me-1"></i>Correo Electrónico *
                            </label>
                            <input type="email" class="form-control" id="correo" name="correo" 
                                   value="<?php echo $_POST['correo'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contraseña" class="form-label">
                                        <i class="fas fa-lock me-1"></i>Contraseña *
                                    </label>
                                    <input type="password" class="form-control" id="contraseña" name="contraseña" required>
                                    <div class="form-text">Mínimo 6 caracteres</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="confirmar_contraseña" class="form-label">
                                        <i class="fas fa-lock me-1"></i>Confirmar Contraseña *
                                    </label>
                                    <input type="password" class="form-control" id="confirmar_contraseña" name="confirmar_contraseña" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="telefono" class="form-label">
                                <i class="fas fa-phone me-1"></i>Teléfono
                            </label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" 
                                   value="<?php echo $_POST['telefono'] ?? ''; ?>" placeholder="987654321">
                        </div>
                        
                        <div class="mb-3">
                            <label for="direccion" class="form-label">
                                <i class="fas fa-map-marker-alt me-1"></i>Dirección
                            </label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="2" 
                                      placeholder="Av. Ejemplo 123, Distrito, Lima"><?php echo $_POST['direccion'] ?? ''; ?></textarea>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terminos" required>
                            <label class="form-check-label" for="terminos">
                                Acepto los <a href="terminos.php" target="_blank">términos y condiciones</a> *
                            </label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-user-plus me-2"></i>Crear Cuenta
                            </button>
                        </div>
                    </form>
                    
                    <hr>
                    
                    <div class="text-center">
                        <p class="mb-2">¿Ya tienes una cuenta?</p>
                        <a href="login.php" class="btn btn-outline-success">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>