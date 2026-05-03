<?php
// login.php - Página de login
session_start();

include 'config.php';
include 'Usuario.php';

$error = '';
$redirigido = isset($_GET['redirigido']) ? 1 : 0;

// Si es POST, procesar el login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if (empty($email) || empty($password)) {
    $error = "Por favor completa email y contraseña";
  } else {
    $usuarioObj = new Usuario($conn);
    $resultado = $usuarioObj->validarCredenciales($email, $password);

    if ($resultado['exito']) {
      // Crear sesión con los datos del usuario
      $_SESSION['usuario_id'] = $resultado['usuario']['id'];
      $_SESSION['usuario_nombre'] = $resultado['usuario']['nombre'];
      $_SESSION['usuario_apellido'] = $resultado['usuario']['apellido'];
      $_SESSION['usuario_email'] = $resultado['usuario']['email'];

      // Redirigir a listar usuarios
      header("Location: listar.php?login=exitoso");
      exit();
    } else {
      $error = $resultado['mensaje'];
    }
  }
  $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Sistema de Usuarios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-light">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-lg">
          <div class="card-header bg-primary text-white">
            <h3 class="card-title text-center mb-0"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</h3>
          </div>
          <div class="card-body">
            <?php if ($redirigido): ?>
              <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-lock"></i> Debes iniciar sesión para acceder
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
              <div class="mb-3">
                <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="tu@email.com" required autofocus>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label"><i class="fas fa-lock"></i> Contraseña</label>
                <div class="input-group">
                  <input type="password" class="form-control" id="password" name="password" placeholder="Tu contraseña" required>
                  <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
              </div>

              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                  <i class="fas fa-sign-in-alt"></i> Ingresar
                </button>
              </div>
            </form>

            <hr>

            <p class="text-center text-muted">
              ¿No tienes cuenta? <a href="index.html" class="text-decoration-none">Regístrate aquí</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
      const passwordInput = document.getElementById('password');
      const icon = this.querySelector('i');
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>