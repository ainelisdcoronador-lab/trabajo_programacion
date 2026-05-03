<?php
// sesion.php - Verificar si el usuario está logueado
// Incluir este archivo en páginas protegidas

session_start();

// Si no hay sesión activa, redirigir a login
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_nombre'])) {
  header("Location: login.php?redirigido=1");
  exit();
}

// Datos del usuario logueado disponibles en:
// $_SESSION['usuario_id']
// $_SESSION['usuario_nombre']
// $_SESSION['usuario_email']
// $_SESSION['usuario_apellido']
