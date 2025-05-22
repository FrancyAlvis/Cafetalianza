<?php
// models/Sale.php
class Sale {
    private $conn;
    private $table_name = "sales";

    public $id;
    public $product_id;
    public $quantity_sold;
    public $sale_date;

    public function __construct($db) {
        $this->conn = $db;
    }

    // RF03: Registrar venta
    public function create() {
        // La verificación de stock y la actualización del stock del producto
        // se harán en el controlador ANTES de llamar a este método.

        $query = "INSERT INTO " . $this->table_name . "
                  SET product_id=:product_id, quantity_sold=:quantity_sold, sale_date=:sale_date";
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->product_id = htmlspecialchars(strip_tags($this->product_id));
        $this->quantity_sold = htmlspecialchars(strip_tags($this->quantity_sold));
        $this->sale_date = htmlspecialchars(strip_tags($this->sale_date)); // Formato YYYY-MM-DD HH:MM:SS

        // Vincular datos
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":quantity_sold", $this->quantity_sold);
        $stmt->bindParam(":sale_date", $this->sale_date);

        if ($stmt->execute()) {
            return true;
        }
        // printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // RF04: Producto más vendido
    public function getMostSoldProduct() {
        $query = "SELECT p.name, SUM(s.quantity_sold) AS total_sold
                  FROM " . $this->table_name . " s
                  JOIN products p ON s.product_id = p.id
                  GROUP BY s.product_id, p.name
                  ORDER BY total_sold DESC
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>