<?php
// models/User.php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $name;
    public $email;
    public $password; // Contraseña en texto plano para el login, se hashea antes de guardar/comparar
    public $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET name=:name, email=:email, password=:password, role=:role";
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->role = htmlspecialchars(strip_tags($this->role));

        // Hashear contraseña
        $hashed_password = password_hash($this->password, PASSWORD_BCRYPT);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":role", $this->role);

        if ($stmt->execute()) {
            return true;
        }
        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    public function login() {
        $query = "SELECT id, name, email, password, role FROM " . $this->table_name . " WHERE email = :email LIMIT 0,1";
        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($this->password, $row['password'])) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->role = $row['role'];
                // No almacenar la contraseña (ni hasheada) en el objeto después del login por seguridad
                return true;
            }
        }
        return false;
    }
}
?>