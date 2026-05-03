<?php
include 'sesion.php';
include 'config.php';
include 'Producto.php';

$id          = $_GET['id'] ?? 0;
$productoObj = new Producto($conn);

if ($id > 0) {
    $resultado = $productoObj->eliminar($id);
    if ($resultado['exito']) {
        header("Location: productos.php?eliminado=1");
    } else {
        header("Location: productos.php?error=" . urlencode($resultado['mensaje']));
    }
} else {
    header("Location: productos.php");
}
$conn->close();
