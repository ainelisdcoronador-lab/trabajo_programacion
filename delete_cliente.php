<?php
include 'sesion.php';
include 'config.php';
include 'Cliente.php';

$id         = $_GET['id'] ?? 0;
$clienteObj = new Cliente($conn);

if ($id > 0) {
    $resultado = $clienteObj->eliminar($id);
    if ($resultado['exito']) {
        header("Location: clientes.php?eliminado=1");
    } else {
        header("Location: clientes.php?error=" . urlencode($resultado['mensaje']));
    }
} else {
    header("Location: clientes.php");
}
$conn->close();
