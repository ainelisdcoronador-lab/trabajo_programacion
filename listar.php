<?php
// listar.php - Listar todos los usuarios registrados
include 'sesion.php'; // Verificar que el usuario esté logueado

include 'config.php';
include 'Usuario.php';

$usuarioObj = new Usuario($conn);
$resultado = $usuarioObj->listar(100, 0); // Listar hasta 100 usuarios

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Listado de Usuarios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <?php include 'navbar.php'; ?>
  <div class="container mt-4">
    <div class="row">
      <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1><i class="fas fa-users"></i> Listado de Usuarios</h1>
        </div>
        <a href="index.html" class="btn btn-info mb-3"><i class="fas fa-plus"></i> Registrar Usuario</a>

        <?php if ($resultado['exito'] && count($resultado['datos']) > 0): ?>
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Email</th>
                  <th>Teléfono</th>
                  <th>Ciudad</th>
                  <th>Género</th>
                  <th>Fecha Creación</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($resultado['datos'] as $usuario): ?>
                  <tr>
                    <td><?= $usuario['id'] ?></td>
                    <td><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></td>
                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                    <td><?= htmlspecialchars($usuario['telefono']) ?></td>
                    <td><?= htmlspecialchars($usuario['ciudad']) ?></td>
                    <td><?= htmlspecialchars($usuario['genero']) ?></td>
                    <td><?= $usuario['fecha_creacion'] ?></td>
                    <td>
                      <a href="ver.php?id=<?= $usuario['id'] ?>" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i>
                      </a>
                      <a href="editar.php?id=<?= $usuario['id'] ?>" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i>
                      </a>
                      <a href="eliminar.php?id=<?= $usuario['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro?')">
                        <i class="fas fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <p class="text-muted">Total de usuarios: <?= $resultado['total'] ?></p>
        <?php else: ?>
          <div class="alert alert-info">No hay usuarios registrados aún.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php $conn->close(); ?>