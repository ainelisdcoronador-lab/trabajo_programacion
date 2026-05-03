<?php
// Categoria.php - Clase para gestionar categorías

class Categoria
{
  private $conn;
  private $tabla = 'categorias';

  public function __construct($conexion)
  {
    $this->conn = $conexion;
  }

  public function insertar($descripcion)
  {
    $stmt = $this->conn->prepare("INSERT INTO $this->tabla (descripcion) VALUES (?)");
    $stmt->bind_param("s", $descripcion);

    if ($stmt->execute()) {
      $id = $this->conn->insert_id;
      $stmt->close();
      return ['exito' => true, 'mensaje' => 'Categoría creada', 'id' => $id];
    } else {
      $error = $stmt->error;
      $stmt->close();
      return ['exito' => false, 'mensaje' => "Error: $error"];
    }
  }

  public function listar()
  {
    $stmt = $this->conn->prepare("SELECT * FROM $this->tabla ORDER BY descripcion ASC");
    $stmt->execute();
    $resultado = $stmt->get_result();

    $categorias = [];
    while ($row = $resultado->fetch_assoc()) {
      $categorias[] = $row;
    }
    $stmt->close();

    return ['exito' => true, 'datos' => $categorias];
  }

  public function obtenerPorId($id)
  {
    $stmt = $this->conn->prepare("SELECT * FROM $this->tabla WHERE id_categoria = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
      $stmt->close();
      return ['exito' => false, 'mensaje' => 'Categoría no encontrada'];
    }

    $categoria = $resultado->fetch_assoc();
    $stmt->close();
    return ['exito' => true, 'datos' => $categoria];
  }

  public function actualizar($id, $descripcion)
  {
    $stmt = $this->conn->prepare("UPDATE $this->tabla SET descripcion = ? WHERE id_categoria = ?");
    $stmt->bind_param("si", $descripcion, $id);

    if ($stmt->execute()) {
      $stmt->close();
      return ['exito' => true, 'mensaje' => 'Categoría actualizada'];
    } else {
      $error = $stmt->error;
      $stmt->close();
      return ['exito' => false, 'mensaje' => "Error: $error"];
    }
  }

  public function eliminar($id)
  {
    $stmt = $this->conn->prepare("DELETE FROM $this->tabla WHERE id_categoria = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
      $stmt->close();
      return ['exito' => true, 'mensaje' => 'Categoría eliminada'];
    } else {
      $error = $stmt->error;
      $stmt->close();
      return ['exito' => false, 'mensaje' => "Error: $error"];
    }
  }
}
