<?php
// Asume que $stmt (resultado de Product->readAll()) está disponible
// include '../views/layouts/header.php'; // Si tienes un layout
// session_start(); // Necesario para verificar roles
?>
<h1>Lista de Productos</h1>
<?php // if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
    <?php // endif; ?>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Referencia</th>
        <th>Precio</th>
        <th>Peso</th>
        <th>Categoría</th>
        <th>Stock</th>
        <th>Fecha Creación</th>
        <th>Acciones</th>
    </tr>
    <?php /*
    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row); // Extrae $id, $name, etc.
            echo "<tr>";
            echo "<td>{$id}</td>";
            echo "<td>{$name}</td>";
            echo "<td>{$reference}</td>";
            echo "<td>{$price}</td>";
            echo "<td>{$weight}</td>";
            echo "<td>{$category}</td>";
            echo "<td>{$stock}</td>";
            echo "<td>{$creation_date}</td>";
            echo "<td>";
            echo "<a href='index.php?controller=product&action=editForm&id={$id}'>Editar</a> ";
            if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                echo "<a href='index.php?controller=product&action=delete&id={$id}' onclick='return confirm(\"¿Estás seguro?\")'>Eliminar</a>";
            }
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='9'>No hay productos registrados.</td></tr>";
    }
    */ ?>
</table>
<?php // include '../views/layouts/footer.php'; ?>