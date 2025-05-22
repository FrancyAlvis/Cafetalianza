<?php
// models/Product.php
class Product {
    private $conn;
    private $table_name = "products";

    public $id;
    public $name;
    public $reference;
    public $price;
    public $weight;
    public $category;
    public $stock;
    public $creation_date;

    public function __construct($db) {
        $this->conn = $db;
    }

    // RF01: Crear producto
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET name=:name, reference=:reference, price=:price, weight=:weight, category=:category, stock=:stock, creation_date=:creation_date";
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->name = htmlspecialchars(strip_tags($this->name));
        // ... (hacer lo mismo para los demás atributos)

        // Vincular datos
        $stmt->bindParam(":name", $this->name);
        // ... (bindParam para los demás)
        $stmt->bindParam(":creation_date", $this->creation_date); // Asegúrate que el formato sea YYYY-MM-DD

        if ($stmt->execute()) {
            return true;
        }
        // Imprimir error si execute() falla
        // printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // RF01: Listar todos los productos
    public function readAll() {
        $query = "SELECT id, name, reference, price, weight, category, stock, created_at FROM " . $this->table_name . " ORDER BY creation_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Leer un solo producto (para editar)
    public function readOne() {
        $query = "SELECT name, reference, price, weight, category, stock, creation_date
                  FROM " . $this->table_name . "
                  WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->name = $row['name'];
            $this->reference = $row['reference'];
            $this->price = $row['price'];
            $this->weight = $row['weight'];
            $this->category = $row['category'];
            $this->stock = $row['stock'];
            $this->creation_date = $row['creation_date'];
            return true;
        }
        return false;
    }


    // RF01: Editar producto
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET name = :name, reference = :reference, price = :price, weight = :weight, category = :category, stock = :stock
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        // ...

        // Vincular nuevos valores
        $stmt->bindParam(':name', $this->name);
        // ... (bindParam para los demás)
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // RF01: Eliminar producto
    public function delete() {
        // Verificar rol de usuario antes de permitir esto en el controlador
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // RF03: Restar stock (usado por el módulo de ventas)
    public function updateStock($quantity_sold) {
        $query = "UPDATE " . $this->table_name . "
                  SET stock = stock - :quantity_sold
                  WHERE id = :id AND stock >= :quantity_sold"; // Asegurar que no quede negativo
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantity_sold', $quantity_sold);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute() && $stmt->rowCount() > 0) { // rowCount verifica que se afectó una fila
            $this->stock -= $quantity_sold;
            return true;
        }
        return false; // No se actualizó, stock insuficiente o producto no encontrado
    }

    // RF04: Producto con mayor stock
    public function getProductWithMostStock() {
        $query = "SELECT id, name, stock FROM " . $this->table_name . " ORDER BY stock DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>