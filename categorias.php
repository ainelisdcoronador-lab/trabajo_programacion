<?php
// categorias.php - Listar, crear y editar categorías
include 'sesion.php';
include 'config.php';
include 'Categoria.php';

$categoriaObj = new Categoria($conn);
$accion = $_GET['accion'] ?? 'listar';
$resultado = [];
$datos = [];

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['crear'])) {
    $resultado = $categoriaObj->insertar($_POST['descripcion']);
    if ($resultado['exito']) {
      $accion = 'listar';
    }
  } elseif (isset($_POST['editar'])) {
    $resultado = $categoriaObj->actualizar($_POST['id'], $_POST['descripcion']);
    if ($resultado['exito']) {
      $accion = 'listar';
    }
  }
}

// Si es editar, obtener datos
if ($accion == 'editar' && isset($_GET['id'])) {
  $datos_cat = $categoriaObj->obtenerPorId($_GET['id']);
  if ($datos_cat['exito']) {
    $datos = $datos_cat['datos'];
  }
}

// Listar categorías
if ($accion == 'listar') {
  $resultado = $categoriaObj->listar();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Categorías</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <?php include 'navbar.php'; ?>
  <div class="container mt-4">
    <h1 class="mb-4"><i class="fas fa-tags"></i> Gestión de Categorías</h1>

    <?php if ($resultado && !$resultado['exito']): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($resultado['mensaje']) ?></div>
    <?php endif; ?>

    <div class="row">
      <div class="col-md-4">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h5><?= $accion == 'editar' ? 'Editar Categoría' : 'Nueva Categoría' ?></h5>
          </div>
          <div class="card-body">
            <form method="POST">
              <div class="mb-3">
                <label class="form-label">Descripción</label>
                <input type="text" name="descripcion" class="form-control" value="<?= htmlspecialchars($datos['descripcion'] ?? '') ?>" required>
              </div>
              <?php if ($accion == 'editar'): ?>
                <input type="hidden" name="id" value="<?= $datos['id_categoria'] ?>">
                <button type="submit" name="editar" class="btn btn-warning w-100">
                  <i class="fas fa-save"></i> Actualizar
                </button>
              <?php else: ?>
                <button type="submit" name="crear" class="btn btn-success w-100">
                  <i class="fas fa-plus"></i> Crear
                </button>
              <?php endif; ?>
              <?php if ($accion == 'editar'): ?>
                <a href="categorias.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
              <?php endif; ?>
            </form>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <div class="card">
          <div class="card-header bg-info text-white">
            <h5>Listado de Categorías</h5>
          </div>
          <div class="card-body">
            <?php if ($resultado['exito'] && !empty($resultado['datos'])): ?>
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Descripción</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($resultado['datos'] as $cat): ?>
                      <tr>
                        <td><?= $cat['id_categoria'] ?></td>
                        <td><?= htmlspecialchars($cat['descripcion']) ?></td>
                        <td>
                          <a href="categorias.php?accion=editar&id=<?= $cat['id_categoria'] ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                          </a>
                          <a href="delete_categoria.php?id=<?= $cat['id_categoria'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">
                            <i class="fas fa-trash"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <p class="text-muted">No hay categorías registradas</p>
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