<?php
// Factura.php - Clase para gestionar facturas

class Factura
{
  private $conn;
  private $tabla = 'facturas';

  public function __construct($conexion)
  {
    $this->conn = $conexion;
  }

  public function insertar($datos)
  {
    if (empty($datos['fecha']) || empty($datos['id_cliente'])) {
      return ['exito' => false, 'mensaje' => 'Fecha e ID del cliente son obligatorios'];
    }

    $stmt = $this->conn->prepare("INSERT INTO $this->tabla (fecha, id_cliente) VALUES (?, ?)");
    $stmt->bind_param("si", $datos['fecha'], $datos['id_cliente']);

    if ($stmt->execute()) {
      $id = $this->conn->insert_id;
      $stmt->close();
      return ['exito' => true, 'mensaje' => 'Factura creada', 'id' => $id];
    } else {
      $error = $stmt->error;
      $stmt->close();
      return ['exito' => false, 'mensaje' => "Error: $error"];
    }
  }

  public function listar()
  {
    $query = "SELECT f.*, c.nombre as cliente
                  FROM $this->tabla f
                  LEFT JOIN clientes c ON f.id_cliente = c.id_cliente
                  ORDER BY f.fecha DESC";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $facturas = [];
    while ($row = $resultado->fetch_assoc()) {
      $facturas[] = $row;
    }
    $stmt->close();

    return ['exito' => true, 'datos' => $facturas];
  }

  public function obtenerPorId($id)
  {
    $query = "SELECT f.*, c.nombre as cliente
                  FROM $this->tabla f
                  LEFT JOIN clientes c ON f.id_cliente = c.id_cliente
                  WHERE f.id_factura = ?";

    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
      $stmt->close();
      return ['exito' => false, 'mensaje' => 'Factura no encontrada'];
    }

    $factura = $resultado->fetch_assoc();
    $stmt->close();
    return ['exito' => true, 'datos' => $factura];
  }

  public function actualizar($id, $datos)
  {
    $campos = [];
    $tipos = "i";
    $params = [$id];

    if (isset($datos['fecha'])) {
      $campos[] = "fecha = ?";
      $tipos .= "s";
      $params[] = $datos['fecha'];
    }
    if (isset($datos['id_cliente'])) {
      $campos[] = "id_cliente = ?";
      $tipos .= "i";
      $params[] = $datos['id_cliente'];
    }

    if (empty($campos)) {
      return ['exito' => false, 'mensaje' => 'No hay campos para actualizar'];
    }

    $query = "UPDATE $this->tabla SET " . implode(", ", $campos) . " WHERE id_factura = ?";
    $stmt = $this->conn->prepare($query);

    $valores = [];
    foreach ($params as $param) {
      $valores[] = &$param;
    }
    call_user_func_array([$stmt, 'bind_param'], array_merge([$tipos], $valores));

    if ($stmt->execute()) {
      $stmt->close();
      return ['exito' => true, 'mensaje' => 'Factura actualizada'];
    } else {
      $error = $stmt->error;
      $stmt->close();
      return ['exito' => false, 'mensaje' => "Error: $error"];
    }
  }

  public function eliminar($id)
  {
    $stmt = $this->conn->prepare("DELETE FROM $this->tabla WHERE id_factura = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
      $stmt->close();
      return ['exito' => true, 'mensaje' => 'Factura eliminada'];
    } else {
      $error = $stmt->error;
      $stmt->close();
      return ['exito' => false, 'mensaje' => "Error: $error"];
    }
  }
}
