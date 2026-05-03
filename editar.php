<?php
// editar.php - Editar datos de un usuario
include 'sesion.php'; // Verificar que el usuario esté logueado

include 'config.php';
include 'Usuario.php';

$id = $_GET['id'] ?? $_POST['id'] ?? 0;
$usuarioObj = new Usuario($conn);

// Si es POST, procesar la actualización
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $datos = [
    'nombre' => trim($_POST['nombre'] ?? ''),
    'apellido' => trim($_POST['apellido'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'telefono' => trim($_POST['telefono'] ?? ''),
    'direccion' => trim($_POST['direccion'] ?? ''),
    'ciudad' => trim($_POST['ciudad'] ?? ''),
    'pais' => trim($_POST['pais'] ?? ''),
    'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? '',
    'genero' => $_POST['genero'] ?? ''
  ];

  $resultado_actualizacion = $usuarioObj->actualizar($id, $datos);

  if ($resultado_actualizacion['exito']) {
    header("Location: ver.php?id=$id&actualizado=1");
    exit();
  } else {
    $error_actualizacion = $resultado_actualizacion['mensaje'];
  }
}

// Obtener datos del usuario
$resultado = $usuarioObj->obtenerPorId($id);

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Usuario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <?php include 'navbar.php'; ?>
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <a href="listar.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Volver</a>

        <?php if ($resultado['exito']): ?>
          <div class="card">
            <div class="card-header bg-warning">
              <h3 class="card-title"><i class="fas fa-edit"></i> Editar Usuario</h3>
            </div>
            <div class="card-body">
              <?php if (isset($error_actualizacion)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_actualizacion) ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
              <?php endif; ?>

              <form method="POST" action="editar.php">
                <input type="hidden" name="id" value="<?= $resultado['datos']['id'] ?>">

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="nombre" class="form-label"><i class="fas fa-user"></i> Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($resultado['datos']['nombre']) ?>" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="apellido" class="form-label"><i class="fas fa-user"></i> Apellido</label>
                    <input type="text" class="form-control" id="apellido" name="apellido" value="<?= htmlspecialchars($resultado['datos']['apellido']) ?>" required>
                  </div>
                </div>

                <div class="mb-3">
                  <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email</label>
                  <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($resultado['datos']['email']) ?>" required>
                </div>

                <div class="mb-3">
                  <label for="telefono" class="form-label"><i class="fas fa-phone"></i> Teléfono</label>
                  <input type="tel" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($resultado['datos']['telefono']) ?>">
                </div>

                <div class="mb-3">
                  <label for="direccion" class="form-label"><i class="fas fa-map-marker-alt"></i> Dirección</label>
                  <input type="text" class="form-control" id="direccion" name="direccion" value="<?= htmlspecialchars($resultado['datos']['direccion']) ?>">
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="ciudad" class="form-label"><i class="fas fa-city"></i> Ciudad</label>
                    <input type="text" class="form-control" id="ciudad" name="ciudad" value="<?= htmlspecialchars($resultado['datos']['ciudad']) ?>">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="pais" class="form-label"><i class="fas fa-globe"></i> País</label>
                    <input type="text" class="form-control" id="pais" name="pais" value="<?= htmlspecialchars($resultado['datos']['pais']) ?>">
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="fecha_nacimiento" class="form-label"><i class="fas fa-calendar-alt"></i> Fecha de Nacimiento</label>
                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?= $resultado['datos']['fecha_nacimiento'] ?>">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="genero" class="form-label"><i class="fas fa-venus-mars"></i> Género</label>
                    <select class="form-select" id="genero" name="genero">
                      <option value="">Selecciona tu género</option>
                      <option value="Masculino" <?= $resultado['datos']['genero'] === 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                      <option value="Femenino" <?= $resultado['datos']['genero'] === 'Femenino' ? 'selected' : '' ?>>Femenino</option>
                      <option value="Otro" <?= $resultado['datos']['genero'] === 'Otro' ? 'selected' : '' ?>>Otro</option>
                      <option value="Prefiero no decirlo" <?= $resultado['datos']['genero'] === 'Prefiero no decirlo' ? 'selected' : '' ?>>Prefiero no decirlo</option>
                    </select>
                  </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                  <button type="button" class="btn btn-secondary" onclick="window.history.back()">Cancelar</button>
                  <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Guardar Cambios</button>
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