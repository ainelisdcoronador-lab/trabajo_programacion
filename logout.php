<?php
// logout.php - Cerrar sesión
session_start();

// Destruir todas las variables de sesión
session_destroy();

// Redirigir a login
header("Location: login.php?logout=exitoso");
exit();
