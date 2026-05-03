# Sistema de Registro de Usuarios con Autenticación

## Descripción

Sistema de registro y gestión de usuarios con autenticación de sesiones, desarrollado en PHP puro con Base de Datos MySQL.

## Archivos Principales

### Backend (PHP)

- **config.php** - Conexión a la base de datos
- **Usuario.php** - Clase con métodos para gestionar usuarios
- **sesion.php** - Protección de sesión (incluir en páginas protegidas)
- **login.php** - Página de login y autenticación
- **logout.php** - Cierra la sesión del usuario
- **process.php** - Procesa el formulario de registro (auto-login después de registrarse)
- **listar.php** - Lista todos los usuarios (requiere login)
- **ver.php** - Muestra los detalles de un usuario (requiere login)
- **editar.php** - Permite editar los datos de un usuario (requiere login)
- **eliminar.php** - Elimina un usuario (requiere login)

### Frontend (HTML/JS)

- **index.html** - Página principal con formulario de registro + botón de login
- **js/scripts.js** - Validación del formulario y funcionalidad de toggle de contraseña

### Base de Datos

- **config.sql** - Script SQL para crear la base de datos y tabla

## Funcionalidades

✅ **Registro de usuario** con auto-login  
✅ **Login** con email y contraseña  
✅ **Sesiones** para usuarios autenticados  
✅ **Logout** para cerrar sesión  
✅ **Páginas protegidas** (listar, ver, editar, eliminar)  
✅ **Listar** todos los usuarios registrados  
✅ **Ver** detalles de un usuario  
✅ **Editar** datos de un usuario  
✅ **Eliminar** un usuario  
✅ **Validación** de contraseñas  
✅ **Hash** de contraseñas seguro  
✅ **Validación** de email  
✅ **Prevención** de inyección SQL (Prepared Statements)

## Flujo de la Aplicación

1. **Usuario nuevo**: Va a `index.html` → Se registra → Auto-login → Redirige a `listar.php`
2. **Usuario existente**: Va a `login.php` → Ingresa credenciales → Redirige a `listar.php`
3. **Páginas protegidas**: Cualquier intento de acceder sin sesión → Redirige a `login.php`
4. **Logout**: Hace clic en "Cerrar Sesión" → Destruye sesión → Redirige a `login.php`

## Instalación

1. Crear la base de datos ejecutando `config.sql` en MySQL:

   ```sql
   source config.sql;
   ```

2. Asegurarse de que el archivo `config.php` tiene las credenciales correctas de BD:
   - Host: localhost
   - Usuario: root
   - Contraseña: (vacía)
   - Base de datos: registro_users

3. Acceder a `index.html` en el navegador

## Clase Usuario - Métodos Disponibles

```php
$usuario = new Usuario($conn);

// Insertar nuevo usuario
$resultado = $usuario->insertar($datos);

// Listar usuarios
$resultado = $usuario->listar($limite, $offset);

// Obtener usuario por ID
$resultado = $usuario->obtenerPorId($id);

// Actualizar usuario
$resultado = $usuario->actualizar($id, $datos);

// Eliminar usuario
$resultado = $usuario->eliminar($id);

// Validar credenciales (login)
$resultado = $usuario->validarCredenciales($email, $password);

// Cambiar contraseña
$resultado = $usuario->cambiarPassword($id, $password_antigua, $password_nueva);
```

## Estructura de Respuesta

Todos los métodos retornan un array con la siguiente estructura:

```php
[
    'exito' => true/false,
    'mensaje' => 'Descripción del resultado',
    'datos' => [...], // Solo en algunos métodos
    'id' => 1 // Solo en insertar
]
```

## Seguridad

- ✅ Prepared Statements para prevenir SQL Injection
- ✅ Password Hash usando PASSWORD_DEFAULT
- ✅ Validación de emails
- ✅ Verificación de emails duplicados
- ✅ No se retorna la contraseña hasheada en respuestas

## Autor

[Tu nombre]

## Fecha

Mayo 2026
