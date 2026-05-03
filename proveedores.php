<?php
// proveedores.php - CRUD de Proveedores
include 'sesion.php';
include 'config.php';
include 'Proveedor.php';

$proveedorObj = new Proveedor($conn);
$accion = $_GET['accion'] ?? 'listar';
$resultado = [];
$datos = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['crear'])) {
    $datos_form = [
      'nombre' => $_POST['nombre'] ?? '',
      'direccion' => $_POST['direccion'] ?? '',
      'telefono' => $_POST['telefono'] ?? ''
    ];
    $resultado = $proveedorObj->insertar($datos_form);
    if ($resultado['exito']) $accion = 'listar';
  } elseif (isset($_POST['editar'])) {
    $datos_form = [
      'nombre' => $_POST['nombre'] ?? '',
      'direccion' => $_POST['direccion'] ?? '',
      'telefono' => $_POST['telefono'] ?? ''
    ];
    $resultado = $proveedorObj->actualizar($_POST['id'], $datos_form);
    if ($resultado['exito']) $accion = 'listar';
  }
}

if ($accion == 'editar' && isset($_GET['id'])) {
  $datos_prov = $proveedorObj->obtenerPorId($_GET['id']);
  if ($datos_prov['exito']) $datos = $datos_prov['datos'];
}

if ($accion == 'listar') {
  $resultado = $proveedorObj->listar();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Proveedores</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <?php include 'navbar.php'; ?>
  <div class="container mt-4">
    <h1 class="mb-4"><i class="fas fa-industry"></i> Gestión de Proveedores</h1>

    <?php if (isset($_GET['eliminado'])): ?>
      <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> Proveedor eliminado correctamente.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if ($resultado && !$resultado['exito']): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($resultado['mensaje']) ?></div>
    <?php endif; ?>

    <div class="row">
      <div class="col-md-4">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h5><?= $accion == 'editar' ? 'Editar Proveedor' : 'Nuevo Proveedor' ?></h5>
          </div>
          <div class="card-body">
            <form method="POST">
              <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Dirección</label>
                <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($datos['direccion'] ?? '') ?>">
              </div>
              <div class="mb-3">
                <label class="form-label">Teléfono</label>
                <input type="tel" name="telefono" class="form-control" value="<?= htmlspecialchars($datos['telefono'] ?? '') ?>">
              </div>
              <?php if ($accion == 'editar'): ?>
                <input type="hidden" name="id" value="<?= $datos['id_proveedor'] ?>">
                <button type="submit" name="editar" class="btn btn-warning w-100">Actualizar</button>
              <?php else: ?>
                <button type="submit" name="crear" class="btn btn-success w-100">Crear</button>
              <?php endif; ?>
              <?php if ($accion == 'editar'): ?>
                <a href="proveedores.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
              <?php endif; ?>
            </form>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <div class="card">
          <div class="card-header bg-info text-white">
            <h5>Listado de Proveedores</h5>
          </div>
          <div class="card-body">
            <?php if ($resultado['exito'] && !empty($resultado['datos'])): ?>
              <div class="table-responsive">
                <table class="table table-striped table-sm">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Nombre</th>
                      <th>Teléfono</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($resultado['datos'] as $prov): ?>
                      <tr>
                        <td><?= $prov['id_proveedor'] ?></td>
                        <td><?= htmlspecialchars($prov['nombre']) ?></td>
                        <td><?= htmlspecialchars($prov['telefono'] ?? '-') ?></td>
                        <td>
                          <a href="proveedores.php?accion=editar&id=<?= $prov['id_proveedor'] ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                          </a>
                          <a href="delete_proveedor.php?id=<?= $prov['id_proveedor'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">
                            <i class="fas fa-trash"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <p class="text-muted">No hay proveedores registrados</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-4">
      <a href="listar.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php $conn->close(); ?>