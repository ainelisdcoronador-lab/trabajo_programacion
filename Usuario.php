<?php
// Usuario.php - Clase para gestionar operaciones de usuarios

class Usuario
{
  private $conn;
  private $tabla = 'Usuarios';

  // Constructor
  public function __construct($conexion)
  {
    $this->conn = $conexion;
  }

  /**
   * Insertar un nuevo usuario
   * @param array $datos Datos del usuario
   * @return array ['exito' => bool, 'mensaje' => string, 'id' => int|null]
   */
  public function insertar($datos)
  {
    // Validar que existan los campos necesarios
    $campos_requeridos = ['nombre', 'apellido', 'email', 'telefono', 'direccion', 'ciudad', 'pais', 'fecha_nacimiento', 'genero', 'password'];

    foreach ($campos_requeridos as $campo) {
      if (empty($datos[$campo])) {
        return [
          'exito' => false,
          'mensaje' => "El campo '$campo' es obligatorio"
        ];
      }
    }

    // Validar formato de email
    if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
      return [
        'exito' => false,
        'mensaje' => "El email no es válido"
      ];
    }

    // Verificar si el email ya existe
    $stmt = $this->conn->prepare("SELECT id FROM $this->tabla WHERE email = ?");
    $stmt->bind_param("s", $datos['email']);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
      return [
        'exito' => false,
        'mensaje' => "El email ya está registrado"
      ];
    }
    $stmt->close();

    // Hashear contraseña
    $password_hasheada = password_hash($datos['password'], PASSWORD_DEFAULT);

    // Preparar la consulta de inserción
    $stmt = $this->conn->prepare("INSERT INTO $this->tabla (nombre, apellido, email, telefono, direccion, ciudad, pais, fecha_nacimiento, genero, password) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
      return [
        'exito' => false,
        'mensaje' => "Error al preparar la consulta: " . $this->conn->error
      ];
    }

    $stmt->bind_param(
      "ssssssssss",
      $datos['nombre'],
      $datos['apellido'],
      $datos['email'],
      $datos['telefono'],
      $datos['direccion'],
      $datos['ciudad'],
      $datos['pais'],
      $datos['fecha_nacimiento'],
      $datos['genero'],
      $password_hasheada
    );

    if ($stmt->execute()) {
      $id_insertado = $this->conn->insert_id;
      $stmt->close();
      return [
        'exito' => true,
        'mensaje' => "Usuario registrado exitosamente",
        'id' => $id_insertado
      ];
    } else {
      $error = $stmt->error;
      $stmt->close();
      return [
        'exito' => false,
        'mensaje' => "Error al registrar el usuario: $error"
      ];
    }
  }

  /**
   * Listar todos los usuarios
   * @param int $limite Límite de resultados
   * @param int $offset Desplazamiento para paginación
   * @return array ['exito' => bool, 'datos' => array, 'total' => int]
   */
  public function listar($limite = 10, $offset = 0)
  {
    // Obtener total de usuarios
    $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM $this->tabla");
    $stmt->execute();
    $resultado = $stmt->get_result();
    $row = $resultado->fetch_assoc();
    $total = $row['total'];
    $stmt->close();

    // Obtener usuarios
    $stmt = $this->conn->prepare("SELECT id, nombre, apellido, email, telefono, direccion, ciudad, pais, fecha_nacimiento, genero, fecha_creacion FROM $this->tabla LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limite, $offset);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $usuarios = [];
    while ($row = $resultado->fetch_assoc()) {
      $usuarios[] = $row;
    }
    $stmt->close();

    return [
      'exito' => true,
      'datos' => $usuarios,
      'total' => $total
    ];
  }

  /**
   * Obtener un usuario por ID
   * @param int $id ID del usuario
   * @return array ['exito' => bool, 'datos' => array|null]
   */
  public function obtenerPorId($id)
  {
    $stmt = $this->conn->prepare("SELECT * FROM $this->tabla WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
      $stmt->close();
      return [
        'exito' => false,
        'mensaje' => "Usuario no encontrado"
      ];
    }

    $usuario = $resultado->fetch_assoc();
    $stmt->close();

    return [
      'exito' => true,
      'datos' => $usuario
    ];
  }

  /**
   * Actualizar un usuario
   * @param int $id ID del usuario
   * @param array $datos Datos a actualizar
   * @return array ['exito' => bool, 'mensaje' => string]
   */
  public function actualizar($id, $datos)
  {
    // Verificar que el usuario exista
    $stmt = $this->conn->prepare("SELECT id FROM $this->tabla WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
      $stmt->close();
      return [
        'exito' => false,
        'mensaje' => "Usuario no encontrado"
      ];
    }
    $stmt->close();

    // Construir dinámicamente la consulta UPDATE
    $campos_actualizables = ['nombre', 'apellido', 'email', 'telefono', 'direccion', 'ciudad', 'pais', 'fecha_nacimiento', 'genero'];
    $campos_actualizar = [];
    $tipos = "i";
    $params = [$id];

    foreach ($campos_actualizables as $campo) {
      if (isset($datos[$campo])) {
        $campos_actualizar[] = "$campo = ?";
        $tipos .= "s";
        $params[] = $datos[$campo];
      }
    }

    if (empty($campos_actualizar)) {
      return [
        'exito' => false,
        'mensaje' => "No hay campos para actualizar"
      ];
    }

    $query = "UPDATE $this->tabla SET " . implode(", ", $campos_actualizar) . " WHERE id = ?";
    $stmt = $this->conn->prepare($query);

    if (!$stmt) {
      return [
        'exito' => false,
        'mensaje' => "Error al preparar la consulta: " . $this->conn->error
      ];
    }

    call_user_func_array([$stmt, 'bind_param'], $this->generarReferenciaParams($tipos, $params));

    if ($stmt->execute()) {
      $stmt->close();
      return [
        'exito' => true,
        'mensaje' => "Usuario actualizado exitosamente"
      ];
    } else {
      $error = $stmt->error;
      $stmt->close();
      return [
        'exito' => false,
        'mensaje' => "Error al actualizar el usuario: $error"
      ];
    }
  }

  /**
   * Eliminar un usuario
   * @param int $id ID del usuario
   * @return array ['exito' => bool, 'mensaje' => string]
   */
  public function eliminar($id)
  {
    // Verificar que el usuario exista
    $stmt = $this->conn->prepare("SELECT id FROM $this->tabla WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
      $stmt->close();
      return [
        'exito' => false,
        'mensaje' => "Usuario no encontrado"
      ];
    }
    $stmt->close();

    // Eliminar usuario
    $stmt = $this->conn->prepare("DELETE FROM $this->tabla WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
      $stmt->close();
      return [
        'exito' => true,
        'mensaje' => "Usuario eliminado exitosamente"
      ];
    } else {
      $error = $stmt->error;
      $stmt->close();
      return [
        'exito' => false,
        'mensaje' => "Error al eliminar el usuario: $error"
      ];
    }
  }

  /**
   * Validar credenciales de usuario (login)
   * @param string $email Email del usuario
   * @param string $password Contraseña en texto plano
   * @return array ['exito' => bool, 'mensaje' => string, 'usuario' => array|null]
   */
  public function validarCredenciales($email, $password)
  {
    // Validar que los parámetros no estén vacíos
    if (empty($email) || empty($password)) {
      return [
        'exito' => false,
        'mensaje' => "Email y contraseña son requeridos"
      ];
    }

    // Buscar usuario por email
    $stmt = $this->conn->prepare("SELECT id, nombre, apellido, email, password FROM $this->tabla WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
      $stmt->close();
      return [
        'exito' => false,
        'mensaje' => "Usuario o contraseña incorrectos"
      ];
    }

    $usuario = $resultado->fetch_assoc();
    $stmt->close();

    // Verificar contraseña
    if (!password_verify($password, $usuario['password'])) {
      return [
        'exito' => false,
        'mensaje' => "Usuario o contraseña incorrectos"
      ];
    }

    // Remover la contraseña hasheada del array de retorno
    unset($usuario['password']);

    return [
      'exito' => true,
      'mensaje' => "Credenciales válidas",
      'usuario' => $usuario
    ];
  }

  /**
   * Cambiar contraseña de un usuario
   * @param int $id ID del usuario
   * @param string $password_antigua Contraseña antigua
   * @param string $password_nueva Nueva contraseña
   * @return array ['exito' => bool, 'mensaje' => string]
   */
  public function cambiarPassword($id, $password_antigua, $password_nueva)
  {
    // Obtener usuario actual
    $stmt = $this->conn->prepare("SELECT password FROM $this->tabla WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
      $stmt->close();
      return [
        'exito' => false,
        'mensaje' => "Usuario no encontrado"
      ];
    }

    $usuario = $resultado->fetch_assoc();
    $stmt->close();

    // Verificar contraseña antigua
    if (!password_verify($password_antigua, $usuario['password'])) {
      return [
        'exito' => false,
        'mensaje' => "La contraseña antigua es incorrecta"
      ];
    }

    // Hashear nueva contraseña
    $password_hasheada = password_hash($password_nueva, PASSWORD_DEFAULT);

    // Actualizar contraseña
    $stmt = $this->conn->prepare("UPDATE $this->tabla SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $password_hasheada, $id);

    if ($stmt->execute()) {
      $stmt->close();
      return [
        'exito' => true,
        'mensaje' => "Contraseña actualizada exitosamente"
      ];
    } else {
      $error = $stmt->error;
      $stmt->close();
      return [
        'exito' => false,
        'mensaje' => "Error al actualizar la contraseña: $error"
      ];
    }
  }

  /**
   * Función auxiliar para generar referencias de parámetros (necesaria para bind_param dinámico)
   */
  private function generarReferenciaParams($tipos, &$params)
  {
    $referencias = [$tipos];
    foreach ($params as &$valor) {
      $referencias[] = &$valor;
    }
    return $referencias;
  }
}
