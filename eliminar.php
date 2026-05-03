<?php
// eliminar.php - Eliminar un usuario
include 'sesion.php'; // Verificar que el usuario esté logueado

include 'config.php';
include 'Usuario.php';

$id = $_GET['id'] ?? 0;
$usuarioObj = new Usuario($conn);

// Obtener datos del usuario antes de eliminar
$resultado = $usuarioObj->obtenerPorId($id);

// Si es POST, procesar la eliminación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirmar_eliminacion'])) {
  $resultado_eliminacion = $usuarioObj->eliminar($id);

  if ($resultado_eliminacion['exito']) {
    header("Location: listar.php?eliminado=1");
    exit();
  } else {
    $error_eliminacion = $resultado_eliminacion['mensaje'];
  }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Eliminar Usuario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <?php include 'navbar.php'; ?>
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <a href="listar.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Volver</a>

        <?php if ($resultado['exito']): ?>
          <div class="card border-danger">
            <div class="card-header bg-danger text-white">
              <h3 class="card-title"><i class="fas fa-trash"></i> Eliminar Usuario</h3>
            </div>
            <div class="card-body">
              <?php if (isset($error_eliminacion)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_eliminacion) ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
              <?php endif; ?>

              <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>¡ADVERTENCIA!</strong> Esta acción no se puede deshacer.
              </div>

              <p class="mb-4">
                ¿Está seguro de que desea eliminar al usuario?
              </p>

              <div class="card mb-3">
                <div class="card-body">
                  <p><strong>Nombre:</strong> <?= htmlspecialchars($resultado['datos']['nombre'] . ' ' . $resultado['datos']['apellido']) ?></p>
                  <p><strong>Email:</strong> <?= htmlspecialchars($resultado['datos']['email']) ?></p>
                  <p class="mb-0"><strong>ID:</strong> <?= $resultado['datos']['id'] ?></p>
                </div>
              </div>

              <form method="POST" action="eliminar.php?id=<?= $id ?>">
                <div class="form-check mb-3">
                  <input class="form-check-input" type="checkbox" id="confirmar" name="confirmar_eliminacion" required>
                  <label class="form-check-label" for="confirmar">
                    Confirmo que deseo eliminar este usuario
                  </label>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                  <a href="listar.php" class="btn btn-secondary">Cancelar</a>
                  <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Eliminar</button>
                </div>
              </form>
            </div>
          </div>
        <?php else: ?>
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($resultado['mensaje']) ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>

</html>
<?php $conn->close(); ?>