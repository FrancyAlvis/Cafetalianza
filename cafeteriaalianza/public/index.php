<?php
// public/index.php (Front Controller Básico)
// Habilitar reporte de errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir configuración y clases base
require_once '../config/Database.php';
// Incluir todos los modelos y controladores (o usar un autoloader)
require_once '../models/Products.php';
require_once '../models/Sales.php';
require_once '../models/Users.php';
require_once '../controllers/ProductController.php';
require_once '../controllers/SaleController.php';
require_once '../controllers/UserController.php';

// Iniciar sesión para gestión de usuarios y roles
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Determinar el controlador y la acción desde la URL
// Ejemplo: index.php?controller=product&action=index
$controllerName = isset($_GET['controller']) ? ucfirst($_GET['controller']) . 'Controller' : 'ProductController'; // Controlador por defecto
$actionName = isset($_GET['action']) ? $_GET['action'] : 'index'; // Acción por defecto

// Verificar si el controlador existe
if (class_exists($controllerName)) {
    $controller = new $controllerName();

    // Verificar si la acción existe en el controlador
    if (method_exists($controller, $actionName)) {
        // Capturar parámetros adicionales (como ID)
        $params = [];
        if (isset($_GET['id'])) {
            $params[] = $_GET['id'];
        }
        // Llamar a la acción del controlador
        call_user_func_array([$controller, $actionName], $params);
    } else {
        echo "Error 404: Acción '{$actionName}' no encontrada en el controlador '{$controllerName}'.";
        // Podrías redirigir a una página de error
    }
} else {
    echo "Error 404: Controlador '{$controllerName}' no encontrado.";
}

?>