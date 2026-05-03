<?php
include 'sesion.php';
include 'config.php';
include 'Venta.php';

$id       = $_GET['id'] ?? 0;
$ventaObj = new Venta($conn);

if ($id > 0) {
    $resultado = $ventaObj->eliminar($id);
    if ($resultado['exito']) {
        header("Location: ventas.php?eliminado=1");
    } else {
        header("Location: ventas.php?error=" . urlencode($resultado['mensaje']));
    }
} else {
    header("Location: ventas.php");
}
$conn->close();
