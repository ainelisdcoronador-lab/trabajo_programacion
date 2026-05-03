// scripts.js - Scripts para el formulario de registro

document.getElementById('registroForm').addEventListener('submit', function (event) {
  const password = document.getElementById('password').value;
  const confirmPassword = document.getElementById('confirmPassword').value;

  if (password !== confirmPassword) {
    event.preventDefault();
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Las contraseñas no coinciden.',
    });
    return;
  }

  // Si las contraseñas coinciden, el formulario se enviará normalmente
});

// Función para toggle contraseña
document.getElementById('togglePassword').addEventListener('click', function () {
  const passwordInput = document.getElementById('password');
  const icon = this.querySelector('i');
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    passwordInput.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
});

// Función para toggle confirmar contraseña
document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
  const confirmPasswordInput = document.getElementById('confirmPassword');
  const icon = this.querySelector('i');
  if (confirmPasswordInput.type === 'password') {
    confirmPasswordInput.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    confirmPasswordInput.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
});

// Manejar mensajes de la URL
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.has('registro') && urlParams.get('registro') === 'exitoso') {
  Swal.fire({
    icon: 'success',
    title: 'Registro exitoso',
    text: '¡Bienvenido!',
  });
} else if (urlParams.has('error')) {
  const errorMsg = urlParams.get('error');
  Swal.fire({
    icon: 'error',
    title: 'Error en el registro',
    text: decodeURIComponent(errorMsg),
  });
}
