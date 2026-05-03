<?php
// process.php - Procesar el formulario de registro
session_start();

include 'config.php';
include 'Usuario.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Recibir y sanitizar datos
  $datos = [
    'nombre' => trim($_POST['nombre'] ?? ''),
    'apellido' => trim($_POST['apellido'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'telefono' => trim($_POST['telefono'] ?? ''),
    'direccion' => trim($_POST['direccion'] ?? ''),
    'ciudad' => trim($_POST['ciudad'] ?? ''),
    'pais' => trim($_POST['pais'] ?? ''),
    'fecha_nacimiento' => $_POST['fechaNacimiento'] ?? '', // Corregido: camelCase a snake_case
    'genero' => $_POST['genero'] ?? '',
    'password' => $_POST['password'] ?? ''
  ];

  // Verificar si las contraseñas coinciden
  if ($datos['password'] !== ($_POST['confirmPassword'] ?? '')) {
    header("Location: index.html?error=contraseñas_no_coinciden");
    exit();
  }

  // Instanciar la clase Usuario y registrar
  $usuarioObj = new Usuario($conn);
  $resultado = $usuarioObj->insertar($datos);

  // Redirigir según el resultado
  if ($resultado['exito']) {
    // Crear sesión automáticamente después del registro
    $_SESSION['usuario_id'] = $resultado['id'];
    $_SESSION['usuario_nombre'] = $datos['nombre'];
    $_SESSION['usuario_apellido'] = $datos['apellido'];
    $_SESSION['usuario_email'] = $datos['email'];

    // Redirigir a listar usuarios
    header("Location: listar.php?registro=exitoso");
  } else {
    header("Location: index.html?error=" . urlencode($resultado['mensaje']));
  }

  $conn->close();
} else {
  // Si no es POST, redirigir
  header("Location: index.html");
  exit();
}
