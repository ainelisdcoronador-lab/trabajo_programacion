<?php
// Venta.php - Clase para gestionar ventas

class Venta
{
  private $conn;
  private $tabla = 'ventas';

  public function __construct($conexion)
  {
    $this->conn = $conexion;
  }

  public function insertar($datos)
  {
    if (empty($datos['id_factura']) || empty($datos['id_producto']) || empty($datos['cantidad'])) {
      return ['exito' => false, 'mensaje' => 'Todos los campos son obligatorios'];
    }

    $stmt = $this->conn->prepare("INSERT INTO $this->tabla (id_factura, id_producto, cantidad) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $datos['id_factura'], $datos['id_producto'], $datos['cantidad']);

    if ($stmt->execute()) {
      $id = $this->conn->insert_id;
      $stmt->close();
      return ['exito' => true, 'mensaje' => 'Venta creada', 'id' => $id];
    } else {
      $error = $stmt->error;
      $stmt->close();
      return ['exito' => false, 'mensaje' => "Error: $error"];
    }
  }

  public function listar()
  {
    $query = "SELECT v.*, f.fecha, c.nombre as cliente, p.descripcion as producto, p.precio
                  FROM $this->tabla v
                  LEFT JOIN facturas f ON v.id_factura = f.id_factura
                  LEFT JOIN clientes c ON f.id_cliente = c.id_cliente
                  LEFT JOIN productos p ON v.id_producto = p.id_producto
                  ORDER BY f.fecha DESC";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $ventas = [];
    while ($row = $resultado->fetch_assoc()) {
      $ventas[] = $row;
    }
    $stmt->close();

    return ['exito' => true, 'datos' => $ventas];
  }

  public function obtenerPorId($id)
  {
    $query = "SELECT v.*, f.fecha, c.nombre as cliente, p.descripcion as producto, p.precio
                  FROM $this->tabla v
                  LEFT JOIN facturas f ON v.id_factura = f.id_factura
                  LEFT JOIN clientes c ON f.id_cliente = c.id_cliente
                  LEFT JOIN productos p ON v.id_producto = p.id_producto
                  WHERE v.id_venta = ?";

    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
      $stmt->close();
      return ['exito' => false, 'mensaje' => 'Venta no encontrada'];
    }

    $venta = $resultado->fetch_assoc();
    $stmt->close();
    return ['exito' => true, 'datos' => $venta];
  }

  public function actualizar($id, $datos)
  {
    $campos = [];
    $tipos = "i";
    $params = [$id];

    if (isset($datos['id_factura'])) {
      $campos[] = "id_factura = ?";
      $tipos .= "i";
      $params[] = $datos['id_factura'];
    }
    if (isset($datos['id_producto'])) {
      $campos[] = "id_producto = ?";
      $tipos .= "i";
      $params[] = $datos['id_producto'];
    }
    if (isset($datos['cantidad'])) {
      $campos[] = "cantidad = ?";
      $tipos .= "i";
      $params[] = $datos['cantidad'];
    }

    if (empty($campos)) {
      return ['exito' => false, 'mensaje' => 'No hay campos para actualizar'];
    }

    $query = "UPDATE $this->tabla SET " . implode(", ", $campos) . " WHERE id_venta = ?";
    $stmt = $this->conn->prepare($query);

    $valores = [];
    foreach ($params as $param) {
      $valores[] = &$param;
    }
    call_user_func_array([$stmt, 'bind_param'], array_merge([$tipos], $valores));

    if ($stmt->execute()) {
      $stmt->close();
      return ['exito' => true, 'mensaje' => 'Venta actualizada'];
    } else {
      $error = $stmt->error;
      $stmt->close();
      return ['exito' => false, 'mensaje' => "Error: $error"];
    }
  }

  public function eliminar($id)
  {
    $stmt = $this->conn->prepare("DELETE FROM $this->tabla WHERE id_venta = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
      $stmt->close();
      return ['exito' => true, 'mensaje' => 'Venta eliminada'];
    } else {
      $error = $stmt->error;
      $stmt->close();
      return ['exito' => false, 'mensaje' => "Error: $error"];
    }
  }
}
