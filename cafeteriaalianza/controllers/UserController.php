<?php
// controllers/UserController.php
// require_once '../config/Database.php';
// require_once '../models/User.php';

class UserController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
        if (session_status() == PHP_SESSION_NONE) { // Evitar iniciar sesión múltiples veces
            session_start();
        }
    }

    public function loginForm() {
        // include '../views/users/login.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->user->email = $_POST['email'];
            $this->user->password = $_POST['password']; // Contraseña en texto plano

            if ($this->user->login()) { // El método login verifica el hash
                // Guardar información del usuario en la sesión
                $_SESSION['user_id'] = $this->user->id;
                $_SESSION['user_name'] = $this->user->name;
                $_SESSION['user_role'] = $this->user->role; // 'admin' o 'vendedor'
                echo "Login exitoso. Bienvenido " . $_SESSION['user_name'];
                // Redirigir al dashboard o página principal
                // header("Location: index.php?controller=product&action=index");
            } else {
                echo "Error: Email o contraseña incorrectos.";
                // Redirigir de nuevo al login con mensaje de error
                // header("Location: index.php?controller=user&action=loginForm&error=1");
            }
        }
    }

    public function logout() {
        session_destroy();
        // header("Location: index.php?controller=user&action=loginForm&status=loggedout");
        echo "Sesión cerrada.";
    }

    // Podrías añadir aquí un método para crear usuarios (ej. registerForm, storeUser)
    // si los administradores pueden crear otros usuarios.
}
?>