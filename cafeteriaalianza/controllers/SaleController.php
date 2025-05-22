<?php
// controllers/SaleController.php
// require_once '../config/Database.php';
// require_once '../models/Sale.php';
// require_once '../models/Product.php'; // Para verificar y actualizar stock
// require_once '../models/User.php'; // Para verificar roles (ej. solo 'vendedor' o 'admin' pueden vender)

class SaleController {
    private $db;
    private $sale;
    private $product; // Para interactuar con el stock del producto

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->sale = new Sale($this->db);
        $this->product = new Product($this->db);
        // session_start();
    }

    // Acción para mostrar el formulario de venta
    public function createForm() {
        // Podrías cargar aquí la lista de productos para un dropdown
        $products_stmt = $this->product->readAll();
        // include '../views/sales/create.php'; // Pasando $products_stmt
    }

    // RF03: Procesar una venta
    public function processSale() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = $_POST['product_id'];
            $quantity_sold = (int)$_POST['quantity_sold'];

            // Validar rol (ejemplo, solo 'vendedor' o 'admin')
            // if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'vendedor'])) {
            //    echo "Acceso denegado para realizar ventas.";
            //    return;
            // }

            if (empty($product_id) || $quantity_sold <= 0) {
                echo "ID de producto y cantidad son requeridos y la cantidad debe ser mayor a cero.";
                return;
            }

            // 1. Verificar stock del producto
            $this->product->id = $product_id;
            if ($this->product->readOne()) { // Carga los datos del producto, incluyendo el stock
                if ($this->product->stock >= $quantity_sold) {
                    // 2. Si hay stock, restar del stock
                    if ($this->product->updateStock($quantity_sold)) {
                        // 3. Registrar la venta
                        $this->sale->product_id = $product_id;
                        $this->sale->quantity_sold = $quantity_sold;
                        $this->sale->sale_date = date('Y-m-d H:i:s'); // Fecha y hora actual

                        if ($this->sale->create()) {
                            echo "Venta realizada exitosamente. Stock actualizado.";
                            // Redirigir o mostrar mensaje
                        } else {
                            // Error al registrar la venta (intentar revertir stock si es crítico)
                            echo "Error al registrar la venta. El stock fue actualizado pero la venta no se guardó.";
                        }
                    } else {
                        echo "Error al actualizar el stock del producto. Venta no realizada.";
                    }
                } else {
                    echo "Stock insuficiente para el producto ID: {$product_id}. Disponible: {$this->product->stock}, Solicitado: {$quantity_sold}. No es posible realizar la venta.";
                }
            } else {
                echo "Producto con ID: {$product_id} no encontrado.";
            }
        }
    }

    // RF04: Consultar producto más vendido
    public function showMostSold() {
        $saleData = $this->sale->getMostSoldProduct();
        if ($saleData) {
            echo "Producto más vendido: " . $saleData['name'] . " (Total vendido: " . $saleData['total_sold'] . ")";
        } else {
            echo "Aún no se han realizado ventas.";
        }
    }
}
?>