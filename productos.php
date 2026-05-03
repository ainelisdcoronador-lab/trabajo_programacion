<?php
include 'sesion.php';
include 'config.php';
include 'Producto.php';
include 'Categoria.php';
include 'Proveedor.php';

$productoObj  = new Producto($conn);
$categoriaObj = new Categoria($conn);
$proveedorObj = new Proveedor($conn);

$accion    = $_GET['accion'] ?? 'listar';
$resultado = [];
$datos     = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['crear'])) {
        $datos_form = [
            'descripcion'  => trim($_POST['descripcion'] ?? ''),
            'precio'       => $_POST['precio'] ?? '',
            'id_categoria' => $_POST['id_categoria'] ?: null,
            'id_proveedor' => $_POST['id_proveedor'] ?: null,
        ];
        $resultado = $productoObj->insertar($datos_form);
        if ($resultado['exito']) $accion = 'listar';
    } elseif (isset($_POST['editar'])) {
        $datos_form = [
            'descripcion'  => trim($_POST['descripcion'] ?? ''),
            'precio'       => $_POST['precio'] ?? '',
            'id_categoria' => $_POST['id_categoria'] ?: null,
            'id_proveedor' => $_POST['id_proveedor'] ?: null,
        ];
        $resultado = $productoObj->actualizar($_POST['id'], $datos_form);
        if ($resultado['exito']) $accion = 'listar';
    }
}

if ($accion == 'editar' && isset($_GET['id'])) {
    $datos_prod = $productoObj->obtenerPorId($_GET['id']);
    if ($datos_prod['exito']) $datos = $datos_prod['datos'];
}

if ($accion == 'listar') {
    $resultado = $productoObj->listar();
}

$categorias = $categoriaObj->listar()['datos'] ?? [];
$proveedores = $proveedorObj->listar()['datos'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Productos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <?php include 'navbar.php'; ?>
  <div class="container mt-4">
    <h1 class="mb-4"><i class="fas fa-box"></i> Gestión de Productos</h1>

    <?php if (isset($_GET['eliminado'])): ?>
      <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> Producto eliminado correctamente.
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
            <h5 class="mb-0"><?= $accion == 'editar' ? 'Editar Producto' : 'Nuevo Producto' ?></h5>
          </div>
          <div class="card-body">
            <form method="POST">
              <div class="mb-3">
                <label class="form-label">Descripción <span class="text-danger">*</span></label>
                <input type="text" name="descripcion" class="form-control"
                  value="<?= htmlspecialchars($datos['descripcion'] ?? '') ?>" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Precio <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">$</span>
                  <input type="number" name="precio" class="form-control" step="0.01" min="0"
                    value="<?= htmlspecialchars($datos['precio'] ?? '') ?>" required>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Categoría</label>
                <select name="id_categoria" class="form-select">
                  <option value="">— Sin categoría —</option>
                  <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id_categoria'] ?>"
                      <?= ($datos['id_categoria'] ?? '') == $cat['id_categoria'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($cat['descripcion']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Proveedor</label>
                <select name="id_proveedor" class="form-select">
                  <option value="">— Sin proveedor —</option>
                  <?php foreach ($proveedores as $prov): ?>
                    <option value="<?= $prov['id_proveedor'] ?>"
                      <?= ($datos['id_proveedor'] ?? '') == $prov['id_proveedor'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($prov['nombre']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <?php if ($accion == 'editar'): ?>
                <input type="hidden" name="id" value="<?= $datos['id_producto'] ?>">
                <button type="submit" name="editar" class="btn btn-warning w-100">
                  <i class="fas fa-save"></i> Actualizar
                </button>
                <a href="productos.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
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
            <h5 class="mb-0">Listado de Productos</h5>
          </div>
          <div class="card-body">
            <?php if (!empty($resultado['datos'])): ?>
              <div class="table-responsive">
                <table class="table table-striped table-sm">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Descripción</th>
                      <th>Precio</th>
                      <th>Categoría</th>
                      <th>Proveedor</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($resultado['datos'] as $prod): ?>
                      <tr>
                        <td><?= $prod['id_producto'] ?></td>
                        <td><?= htmlspecialchars($prod['descripcion']) ?></td>
                        <td>$<?= number_format($prod['precio'], 2) ?></td>
                        <td><?= htmlspecialchars($prod['categoria'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($prod['proveedor'] ?? '—') ?></td>
                        <td>
                          <a href="productos.php?accion=editar&id=<?= $prod['id_producto'] ?>"
                            class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                          <a href="delete_producto.php?id=<?= $prod['id_producto'] ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('¿Eliminar este producto?')">
                            <i class="fas fa-trash"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <p class="text-muted">No hay productos registrados.</p>
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
