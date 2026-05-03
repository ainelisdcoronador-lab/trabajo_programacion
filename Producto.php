<?php
// Producto.php - Clase para gestionar productos

class Producto
{
  private $conn;
  private $tabla = 'productos';

  public function __construct($conexion)
  {
    $this->conn = $conexion;
  }

  public function insertar($datos)
  {
    if (empty($datos['descripcion']) || empty($datos['precio'])) {
      return ['exito' => false, 'mensaje' => 'Descripción y precio son obligatorios'];
    }

    $stmt = $this->conn->prepare("INSERT INTO $this->tabla (descripcion, precio, id_categoria, id_proveedor) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdii", $datos['descripcion'], $datos['precio'], $datos['id_categoria'] ?? null, $datos['id_proveedor'] ?? null);

    if ($stmt->execute()) {
      $id = $this->conn->insert_id;
      $stmt->close();
      return ['exito' => true, 'mensaje' => 'Producto creado', 'id' => $id];
    } else {
      $error = $stmt->error;
      $stmt->close();
      return ['exito' => false, 'mensaje' => "Error: $error"];
    }
  }

  public function listar()
  {
    $query = "SELECT p.*, c.descripcion as categoria, pr.nombre as proveedor
                  FROM $this->tabla p
                  LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                  LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor
                  ORDER BY p.descripcion ASC";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $productos = [];
    while ($row = $resultado->fetch_assoc()) {
      $productos[] = $row;
    }
    $stmt->close();

    return ['exito' => true, 'datos' => $productos];
  }

  public function obtenerPorId($id)
  {
    $query = "SELECT p.*, c.descripcion as categoria, pr.nombre as proveedor
                  FROM $this->tabla p
                  LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                  LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor
                  WHERE p.id_producto = ?";

    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
      $stmt->close();
      return ['exito' => false, 'mensaje' => 'Producto no encontrado'];
    }

    $producto = $resultado->fetch_assoc();
    $stmt->close();
    return ['exito' => true, 'datos' => $producto];
  }

  public function actualizar($id, $datos)
  {
    $campos = [];
    $tipos = "i";
    $params = [$id];

    if (isset($datos['descripcion'])) {
      $campos[] = "descripcion = ?";
      $tipos .= "s";
      $params[] = $datos['descripcion'];
    }
    if (isset($datos['precio'])) {
      $campos[] = "precio = ?";
      $tipos .= "d";
      $params[] = $datos['precio'];
    }
    if (isset($datos['id_categoria'])) {
      $campos[] = "id_categoria = ?";
      $tipos .= "i";
      $params[] = $datos['id_categoria'];
    }
    if (isset($datos['id_proveedor'])) {
      $campos[] = "id_proveedor = ?";
      $tipos .= "i";
      $params[] = $datos['id_proveedor'];
    }

    if (empty($campos)) {
      return ['exito' => false, 'mensaje' => 'No hay campos para actualizar'];
    }

    $query = "UPDATE $this->tabla SET " . implode(", ", $campos) . " WHERE id_producto = ?";
    $stmt = $this->conn->prepare($query);

    $valores = [];
    foreach ($params as $param) {
      $valores[] = &$param;
    }
    call_user_func_array([$stmt, 'bind_param'], array_merge([$tipos], $valores));

    if ($stmt->execute()) {
      $stmt->close();
      return ['exito' => true, 'mensaje' => 'Producto actualizado'];
    } else {
      $error = $stmt->error;
      $stmt->close();
      return ['exito' => false, 'mensaje' => "Error: $error"];
    }
  }

  public function eliminar($id)
  {
    $stmt = $this->conn->prepare("DELETE FROM $this->tabla WHERE id_producto = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
      $stmt->close();
      return ['exito' => true, 'mensaje' => 'Producto eliminado'];
    } else {
      $error = $stmt->error;
      $stmt->close();
      return ['exito' => false, 'mensaje' => "Error: $error"];
    }
  }
}
