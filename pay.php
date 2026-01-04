<?php
require_once 'includes/functions.php';

// Validar parámetros
$monto = isset($_GET['m']) ? number_format(floatval($_GET['m']), 2, '.', '') : null;
$token = $_GET['t'] ?? '';

if (!$monto || !$token) {
    echo "Enlace inválido.";
    exit;
}

// Datos de tu empresa
$yape_number = '901115993';
$yape_name = 'Botica Olivos S.A.C.';
$qr_img = 'qr.jpeg'; // QR estático de tu empresa

?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Pagar con Yape - Botica Olivos</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card mx-auto" style="max-width:720px;">
        <div class="card-body text-center">
            <h4>Pagar con Yape</h4>
            <p class="mb-1">Monto a pagar</p>
            <h1 class="display-4">S/ <?php echo htmlspecialchars($monto); ?></h1>

            <?php if (file_exists($qr_img)): ?>
                <img src="<?php echo $qr_img; ?>" alt="QR Yape" style="max-width:200px;">
            <?php endif; ?>

            <div class="mt-3">
                <strong>Número Yape:</strong><br>
                <span class="h5"><?php echo htmlspecialchars($yape_number); ?></span><br>
                <small><?php echo htmlspecialchars($yape_name); ?></small>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button id="btnOpenYape" class="btn btn-success">Abrir Yape</button>
                <button id="btnCopy" class="btn btn-outline-secondary">Copiar Monto</button>
                <a href="checkout.php?confirm_token=<?php echo urlencode($token); ?>" class="btn btn-primary">
                    Ya pagué — Subir comprobante
                </a>
            </div>

            <p class="text-muted small mt-3">Si el botón "Abrir Yape" no funciona, abre la app manualmente y pega el monto copiado.</p>
        </div>
    </div>
</div>

<script>
// Intento de abrir Yape (experimental)
document.getElementById('btnOpenYape').addEventListener('click', function(){
    const monto = "<?php echo $monto; ?>";
    const phone = "<?php echo $yape_number; ?>";
    const scheme = `yape://pay?phone=${encodeURIComponent(phone)}&amount=${encodeURIComponent(monto)}`;
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = scheme;
    document.body.appendChild(iframe);
    setTimeout(()=>{ document.body.removeChild(iframe); }, 1000);
    alert('Si la app no se abrió automáticamente: abre Yape, pega el monto ' + monto + ' y paga al número ' + phone + '.');
});

// Copiar monto
document.getElementById('btnCopy').addEventListener('click', async function(){
    const monto = "<?php echo $monto; ?>";
    try {
        await navigator.clipboard.writeText(monto);
        alert('Monto copiado: S/ ' + monto + '. Ahora abre Yape y pega el monto.');
    } catch (e) {
        prompt('Copia manualmente el monto:', monto);
    }
});
</script>
</body>
</html>
