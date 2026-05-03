<?php
include 'sesion.php';
include 'config.php';
include 'Factura.php';

$id         = $_GET['id'] ?? 0;
$facturaObj = new Factura($conn);

if ($id > 0) {
    $resultado = $facturaObj->eliminar($id);
    if ($resultado['exito']) {
        header("Location: facturas.php?eliminado=1");
    } else {
        header("Location: facturas.php?error=" . urlencode($resultado['mensaje']));
    }
} else {
    header("Location: facturas.php");
}
$conn->close();
