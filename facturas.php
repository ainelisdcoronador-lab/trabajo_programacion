<?php
include 'sesion.php';
include 'config.php';
include 'Factura.php';
include 'Cliente.php';

$facturaObj = new Factura($conn);
$clienteObj = new Cliente($conn);

$accion    = $_GET['accion'] ?? 'listar';
$resultado = [];
$datos     = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['crear'])) {
        $datos_form = [
            'fecha'      => $_POST['fecha'] ?? '',
            'id_cliente' => $_POST['id_cliente'] ?? '',
        ];
        $resultado = $facturaObj->insertar($datos_form);
        if ($resultado['exito']) $accion = 'listar';
    } elseif (isset($_POST['editar'])) {
        $datos_form = [
            'fecha'      => $_POST['fecha'] ?? '',
            'id_cliente' => $_POST['id_cliente'] ?? '',
        ];
        $resultado = $facturaObj->actualizar($_POST['id'], $datos_form);
        if ($resultado['exito']) $accion = 'listar';
    }
}

if ($accion == 'editar' && isset($_GET['id'])) {
    $datos_fac = $facturaObj->obtenerPorId($_GET['id']);
    if ($datos_fac['exito']) $datos = $datos_fac['datos'];
}

if ($accion == 'listar') {
    $resultado = $facturaObj->listar();
}

$clientes = $clienteObj->listar()['datos'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Facturas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <?php include 'navbar.php'; ?>
  <div class="container mt-4">
    <h1 class="mb-4"><i class="fas fa-file-invoice"></i> Gestión de Facturas</h1>

    <?php if (isset($_GET['eliminado'])): ?>
      <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> Factura eliminada correctamente.
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
            <h5 class="mb-0"><?= $accion == 'editar' ? 'Editar Factura' : 'Nueva Factura' ?></h5>
          </div>
          <div class="card-body">
            <form method="POST">
              <div class="mb-3">
                <label class="form-label">Fecha <span class="text-danger">*</span></label>
                <input type="date" name="fecha" class="form-control"
                  value="<?= htmlspecialchars($datos['fecha'] ?? date('Y-m-d')) ?>" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Cliente <span class="text-danger">*</span></label>
                <select name="id_cliente" class="form-select" required>
                  <option value="">— Seleccionar cliente —</option>
                  <?php foreach ($clientes as $cli): ?>
                    <option value="<?= $cli['id_cliente'] ?>"
                      <?= ($datos['id_cliente'] ?? '') == $cli['id_cliente'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($cli['nombre']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <?php if ($accion == 'editar'): ?>
                <input type="hidden" name="id" value="<?= $datos['id_factura'] ?>">
                <button type="submit" name="editar" class="btn btn-warning w-100">
                  <i class="fas fa-save"></i> Actualizar
                </button>
                <a href="facturas.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
              <?php else: ?>
                <button type="submit" name="crear" class="btn btn-success w-100">
                  <i class="fas fa-plus"></i> Crear
                </button>
              <?php endif; ?>
            </form>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <div class="card">
          <div class="card-header bg-info text-white">
            <h5 class="mb-0">Listado de Facturas</h5>
          </div>
          <div class="card-body">
            <?php if (!empty($resultado['datos'])): ?>
              <div class="table-responsive">
                <table class="table table-striped table-sm">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Fecha</th>
                      <th>Cliente</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($resultado['datos'] as $fac): ?>
                      <tr>
                        <td><?= $fac['id_factura'] ?></td>
                        <td><?= htmlspecialchars($fac['fecha']) ?></td>
                        <td><?= htmlspecialchars($fac['cliente'] ?? '—') ?></td>
                        <td>
                          <a href="facturas.php?accion=editar&id=<?= $fac['id_factura'] ?>"
                            class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                          <a href="delete_factura.php?id=<?= $fac['id_factura'] ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('¿Eliminar esta factura?')">
                            <i class="fas fa-trash"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <p class="text-muted">No hay facturas registradas.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php $conn->close(); ?>
