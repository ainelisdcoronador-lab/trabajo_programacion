<?php
// delete_categoria.php
include 'sesion.php';
include 'config.php';
include 'Categoria.php';

$id = $_GET['id'] ?? 0;
$categoriaObj = new Categoria($conn);

if ($id > 0) {
  $resultado = $categoriaObj->eliminar($id);
  if ($resultado['exito']) {
    header("Location: categorias.php?eliminado=1");
  } else {
    header("Location: categorias.php?error=" . urlencode($resultado['mensaje']));
  }
} else {
  header("Location: categorias.php");
}
$conn->close();
