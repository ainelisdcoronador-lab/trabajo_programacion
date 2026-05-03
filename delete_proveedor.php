<?php
include 'sesion.php';
include 'config.php';
include 'Proveedor.php';

$id           = $_GET['id'] ?? 0;
$proveedorObj = new Proveedor($conn);

if ($id > 0) {
    $resultado = $proveedorObj->eliminar($id);
    if ($resultado['exito']) {
        header("Location: proveedores.php?eliminado=1");
    } else {
        header("Location: proveedores.php?error=" . urlencode($resultado['mensaje']));
    }
} else {
    header("Location: proveedores.php");
}
$conn->close();
