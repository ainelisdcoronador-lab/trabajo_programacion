<?php
// Cliente.php - Clase para gestionar clientes

class Cliente
{
  private $conn;
  private $tabla = 'clientes';

  public function __construct($conexion)
  {
    $this->conn = $conexion;
  }

  public function insertar($datos)
  {
    if (empty($datos['nombre']) || empty($datos['ciudad'])) {
      return ['exito' => false, 'mensaje' => 'Nombre y ciudad son obligatorios'];
    }

    $stmt = $this->conn->prepare("INSERT INTO $this->tabla (nombre, direccion, telefono, ciudad) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $datos['nombre'], $datos['direccion'] ?? null, $datos['telefono'] ?? null, $datos['ciudad']);

    if ($stmt->execute()) {
      $id = $this->conn->insert_id;
      $stmt->close();
      return ['exito' => true, 'mensaje' => 'Cliente creado', 'id' => $id];
    } else {
      $error = $stmt->error;
      $stmt->close();
      return ['exito' => false, 'mensaje' => "Error: $error"];
    }
  }

  public function listar()
  {
    $stmt = $this->conn->prepare("SELECT * FROM $this->tabla ORDER BY nombre ASC");
    $stmt->execute();
    $resultado = $stmt->get_result();

    $clientes = [];
    while ($row = $resultado->fetch_assoc()) {
      $clientes[] = $row;
    }
    $stmt->close();

    return ['exito' => true, 'datos' => $clientes];
  }

  public function obtenerPorId($id)
  {
    $stmt = $this->conn->prepare("SELECT * FROM $this->tabla WHERE id_cliente = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
      $stmt->close();
      return ['exito' => false, 'mensaje' => 'Cliente no encontrado'];
    }

    $cliente = $resultado->fetch_assoc();
    $stmt->close();
    return ['exito' => true, 'datos' => $cliente];
  }

  public function actualizar($id, $datos)
  {
    $campos = [];
    $tipos = "i";
    $params = [$id];

    if (isset($datos['nombre'])) {
      $campos[] = "nombre = ?";
      $tipos .= "s";
      $params[] = $datos['nombre'];
    }
    if (isset($datos['direccion'])) {
      $campos[] = "direccion = ?";
      $tipos .= "s";
      $params[] = $datos['direccion'];
    }
    if (isset($datos['telefono'])) {
      $campos[] = "telefono = ?";
      $tipos .= "s";
      $params[] = $datos['telefono'];
    }
    if (isset($datos['ciudad'])) {
      $campos[] = "ciudad = ?";
      $tipos .= "s";
      $params[] = $datos['ciudad'];
    }

    if (empty($campos)) {
      return ['exito' => false, 'mensaje' => 'No hay campos para actualizar'];
    }

    $query = "UPDATE $this->tabla SET " . implode(", ", $campos) . " WHERE id_cliente = ?";
    $stmt = $this->conn->prepare($query);

    $valores = [];
    foreach ($params as $param) {
      $valores[] = &$param;
    }
    call_user_func_array([$stmt, 'bind_param'], array_merge([$tipos], $valores));

    if ($stmt->execute()) {
      $stmt->close();
      return ['exito' => true, 'mensaje' => 'Cliente actualizado'];
    } else {
      $error = $stmt->error;
      $stmt->close();
      return ['exito' => false, 'mensaje' => "Error: $error"];
    }
  }

  public function eliminar($id)
  {
    $stmt = $this->conn->prepare("DELETE FROM $this->tabla WHERE id_cliente = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
      $stmt->close();
      return ['exito' => true, 'mensaje' => 'Cliente eliminado'];
    } else {
      $error = $stmt->error;
      $stmt->close();
      return ['exito' => false, 'mensaje' => "Error: $error"];
    }
  }
}
