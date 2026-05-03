<?php
$pagina_actual = basename($_SERVER['PHP_SELF']);
$modulos = [
    [
        'href'   => 'listar.php',
        'icon'   => 'fas fa-users',
        'label'  => 'Usuarios',
        'activo' => in_array($pagina_actual, ['listar.php', 'editar.php', 'eliminar.php', 'ver.php']),
    ],
    [
        'href'   => 'categorias.php',
        'icon'   => 'fas fa-tags',
        'label'  => 'Categorías',
        'activo' => $pagina_actual === 'categorias.php',
    ],
    [
        'href'   => 'proveedores.php',
        'icon'   => 'fas fa-industry',
        'label'  => 'Proveedores',
        'activo' => $pagina_actual === 'proveedores.php',
    ],
    [
        'href'   => 'productos.php',
        'icon'   => 'fas fa-box',
        'label'  => 'Productos',
        'activo' => $pagina_actual === 'productos.php',
    ],
    [
        'href'   => 'clientes.php',
        'icon'   => 'fas fa-user-tie',
        'label'  => 'Clientes',
        'activo' => $pagina_actual === 'clientes.php',
    ],
    [
        'href'   => 'facturas.php',
        'icon'   => 'fas fa-file-invoice',
        'label'  => 'Facturas',
        'activo' => $pagina_actual === 'facturas.php',
    ],
    [
        'href'   => 'ventas.php',
        'icon'   => 'fas fa-shopping-cart',
        'label'  => 'Ventas',
        'activo' => $pagina_actual === 'ventas.php',
    ],
];
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid px-4">
    <a class="navbar-brand fw-bold" href="listar.php">
      <i class="fas fa-store me-1"></i> Sistema de Gestión
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu"
      aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarMenu">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php foreach ($modulos as $modulo): ?>
          <li class="nav-item">
            <?php if (!empty($modulo['disabled'])): ?>
              <span class="nav-link text-secondary" style="cursor:default" title="Próximamente">
                <i class="<?= $modulo['icon'] ?>"></i> <?= $modulo['label'] ?>
                <span class="badge bg-secondary ms-1" style="font-size:0.6rem;vertical-align:middle">Pronto</span>
              </span>
            <?php else: ?>
              <a class="nav-link <?= $modulo['activo'] ? 'active fw-semibold' : '' ?>" href="<?= $modulo['href'] ?>">
                <i class="<?= $modulo['icon'] ?>"></i> <?= $modulo['label'] ?>
              </a>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
      <ul class="navbar-nav ms-auto align-items-center">
        <?php if (isset($_SESSION['usuario_nombre'])): ?>
          <li class="nav-item">
            <span class="nav-link text-light">
              <i class="fas fa-user-circle me-1"></i><?= htmlspecialchars($_SESSION['usuario_nombre']) ?>
            </span>
          </li>
        <?php endif; ?>
        <li class="nav-item">
          <a class="nav-link text-danger" href="logout.php">
            <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
