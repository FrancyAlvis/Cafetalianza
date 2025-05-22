<?php
// controllers/ProductController.php
// Necesitarás incluir tus modelos y la conexión a la BD
require_once '../config/Database.php';
require_once '../models/Product.php';
require_once '../models/User.php'; // Para verificar roles

class ProductController {
    private $db;
    private $product;
    // private $user; // Para la gestión de roles

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->product = new Product($this->db);
        // $this->user = new User($this->db); // Si necesitas verificar roles aquí
        // session_start(); // Iniciar sesión para roles
    }

    // Acción para mostrar el listado de productos
    public function index() {
        $stmt = $this->product->readAll();
        // Pasar $stmt a la vista views/products/list.php
        // Por ejemplo: include '../views/products/list.php';
    }

    // Acción para mostrar el formulario de creación
    public function createForm() {
        // include '../views/products/create.php';
    }

    // Acción para procesar la creación de un producto
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Asignar valores del POST al objeto producto
            $this->product->name = $_POST['name'];
            $this->product->reference = $_POST['reference'];
            $this->product->price = $_POST['price'];
            $this->product->weight = $_POST['weight'];
            $this->product->category = $_POST['category'];
            $this->product->stock = $_POST['stock'];
            $this->product->creation_date = $_POST['creation_date']; // Asegurar formato YYYY-MM-DD

            // Validar campos obligatorios (RNF03)
            if (empty($this->product->name) /* || ... otras validaciones */) {
                // Mostrar error, redirigir
                echo "Error: Campos obligatorios faltantes.";
                return;
            }

            if ($this->product->create()) {
                // Redirigir a la lista de productos o mostrar mensaje de éxito
                // header("Location: index.php?controller=product&action=index&status=success");
                echo "Producto creado exitosamente.";
            } else {
                // Mostrar mensaje de error
                echo "Error al crear el producto.";
            }
        }
    }

    // Acción para mostrar el formulario de edición
    public function editForm($id) {
        $this->product->id = $id;
        if ($this->product->readOne()) {
            // Pasar $this->product a la vista views/products/edit.php
            // include '../views/products/edit.php';
        } else {
            echo "Producto no encontrado.";
        }
    }

    // Acción para procesar la actualización
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->product->id = $id; // o $_POST['id'] si lo envías oculto
            $this->product->name = $_POST['name'];
            // ... resto de campos
            if ($this->product->update()) {
                // header("Location: index.php?controller=product&action=index&status=updated");
                echo "Producto actualizado.";
            } else {
                echo "Error al actualizar.";
            }
        }
    }

    // Acción para eliminar producto
    public function delete($id) {
        // RF01: Eliminar productos. (Restringir por rol)
        session_start(); // Asegúrate de que la sesión esté iniciada
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        echo "Acceso denegado. Solo administradores pueden eliminar productos.";
        return;
        }

        $this->product->id = $id;
        if ($this->product->delete()) {
            // header("Location: index.php?controller=product&action=index&status=deleted");
            echo "Producto eliminado.";
        } else {
            echo "Error al eliminar.";
        }
    }

    // RF04: Consultar producto con mayor stock
    public function showMostStocked() {
        $productData = $this->product->getProductWithMostStock();
        if ($productData) {
            // Mostrar $productData en una vista o como JSON
            echo "Producto con más stock: " . $productData['name'] . " (" . $productData['stock'] . " unidades)";
        } else {
            echo "No hay productos en inventario.";
        }
    }
}
?>