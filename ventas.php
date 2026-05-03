<?php
include 'sesion.php';
include 'config.php';
include 'Venta.php';
include 'Factura.php';
include 'Producto.php';

$ventaObj   = new Venta($conn);
$facturaObj = new Factura($conn);
$productoObj = new Producto($conn);

$accion    = $_GET['accion'] ?? 'listar';
$resultado = [];
$datos     = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['crear'])) {
        $datos_form = [
            'id_factura'  => $_POST['id_factura'] ?? '',
            'id_producto' => $_POST['id_producto'] ?? '',
            'cantidad'    => $_POST['cantidad'] ?? '',
        ];
        $resultado = $ventaObj->insertar($datos_form);
        if ($resultado['exito']) $accion = 'listar';
    } elseif (isset($_POST['editar'])) {
        $datos_form = [
            'id_factura'  => $_POST['id_factura'] ?? '',
            'id_producto' => $_POST['id_producto'] ?? '',
            'cantidad'    => $_POST['cantidad'] ?? '',
        ];
        $resultado = $ventaObj->actualizar($_POST['id'], $datos_form);
        if ($resultado['exito']) $accion = 'listar';
    }
}

if ($accion == 'editar' && isset($_GET['id'])) {
    $datos_ven = $ventaObj->obtenerPorId($_GET['id']);
    if ($datos_ven['exito']) $datos = $datos_ven['datos'];
}

if ($accion == 'listar') {
    $resultado = $ventaObj->listar();
}

$facturas  = $facturaObj->listar()['datos'] ?? [];
$productos = $productoObj->listar()['datos'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Ventas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <?php include 'navbar.php'; ?>
  <div class="container mt-4">
    <h1 class="mb-4"><i class="fas fa-shopping-cart"></i> Gestión de Ventas</h1>

    <?php if (isset($_GET['eliminado'])): ?>
      <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> Venta eliminada correctamente.
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
            <h5 class="mb-0"><?= $accion == 'editar' ? 'Editar Venta' : 'Nueva Venta' ?></h5>
          </div>
          <div class="card-body">
            <form method="POST">
              <div class="mb-3">
                <label class="form-label">Factura <span class="text-danger">*</span></label>
                <select name="id_factura" class="form-select" required>
                  <option value="">— Seleccionar factura —</option>
                  <?php foreach ($facturas as $fac): ?>
                    <option value="<?= $fac['id_factura'] ?>"
                      <?= ($datos['id_factura'] ?? '') == $fac['id_factura'] ? 'selected' : '' ?>>
                      #<?= $fac['id_factura'] ?> — <?= htmlspecialchars($fac['cliente'] ?? 'Sin cliente') ?> (<?= $fac['fecha'] ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Producto <span class="text-danger">*</span></label>
                <select name="id_producto" class="form-select" required>
                  <option value="">— Seleccionar producto —</option>
                  <?php foreach ($productos as $prod): ?>
                    <option value="<?= $prod['id_producto'] ?>"
                      <?= ($datos['id_producto'] ?? '') == $prod['id_producto'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($prod['descripcion']) ?> — $<?= number_format($prod['precio'], 2) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Cantidad <span class="text-danger">*</span></label>
                <input type="number" name="cantidad" class="form-control" min="1"
                  value="<?= htmlspecialchars($datos['cantidad'] ?? '1') ?>" required>
              </div>
              <?php if ($accion == 'editar'): ?>
                <input type="hidden" name="id" value="<?= $datos['id_venta'] ?>">
                <button type="submit" name="editar" class="btn btn-warning w-100">
                  <i class="fas fa-save"></i> Actualizar
                </button>
                <a href="ventas.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
              <?php else: ?>
                <button type="submit" name="crear" class="btn btn-success w-100">
                  <i class="fas fa-plus"></i> Registrar Venta
                </button>
              <?php endif; ?>
            </form>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <div class="card">
          <div class="card-header bg-info text-white">
            <h5 class="mb-0">Listado de Ventas</h5>
          </div>
          <div class="card-body">
            <?php if (!empty($resultado['datos'])): ?>
              <div class="table-responsive">
                <table class="table table-striped table-sm">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Factura</th>
                      <th>Cliente</th>
                      <th>Producto</th>
                      <th>Precio</th>
                      <th>Cant.</th>
                      <th>Total</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($resultado['datos'] as $ven): ?>
                      <tr>
                        <td><?= $ven['id_venta'] ?></td>
                        <td>#<?= $ven['id_factura'] ?></td>
                        <td><?= htmlspecialchars($ven['cliente'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($ven['producto'] ?? '—') ?></td>
                        <td>$<?= number_format($ven['precio'] ?? 0, 2) ?></td>
                        <td><?= $ven['cantidad'] ?></td>
                        <td>$<?= number_format(($ven['precio'] ?? 0) * $ven['cantidad'], 2) ?></td>
                        <td>
                          <a href="ventas.php?accion=editar&id=<?= $ven['id_venta'] ?>"
                            class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                          <a href="delete_venta.php?id=<?= $ven['id_venta'] ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('¿Eliminar esta venta?')">
                            <i class="fas fa-trash"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <p class="text-muted">No hay ventas registradas.</p>
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
