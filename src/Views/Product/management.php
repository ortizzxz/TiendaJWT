<div class="container">
    <h1>Gestión de Productos</h1>

    <?php
    function displayMessage()
    {
        if (isset($_SESSION['errors'])) {
            echo "<div class='error-message'>";
            if (is_array($_SESSION['errors'])) {
                foreach ($_SESSION['errors'] as $error) {
                    echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "<br>";
                }
            } else {
                echo htmlspecialchars($_SESSION['errors'], ENT_QUOTES, 'UTF-8');
            }
            echo "</div>";
            unset($_SESSION['errors']);
        } elseif (isset($_SESSION['success'])) {
            echo "<div class='success-message'>";
            if (is_array($_SESSION['success'])) {
                foreach ($_SESSION['success'] as $success) {
                    echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8') . "<br>";
                }
            } else {
                echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8');
            }
            echo "</div>";
            unset($_SESSION['success']);
        }
    }


    displayMessage();

    function displayNoProductsMessage()
    {
        echo "<h2 id='productTitle'>No hay productos disponibles.</h2>";
    }

    function displayProductsTable(array $products)
    {
        echo "<h2 id='productTitle'>Lista de Productos</h2>";

        echo "<table class='styled-table'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Categoría</th>";
        echo "<th>Nombre</th>";
        echo "<th>Descripción</th>";
        echo "<th>Precio</th>";
        echo "<th>Stock</th>";
        echo "<th>Oferta</th>";
        echo "<th>Imagen</th>";
        echo "<th>Acción</th>";
        echo "</tr>";
        echo "</thead>";

        echo "<tbody>";

        foreach ($products as $product) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($product['id']) . "</td>";
            echo "<td>" . htmlspecialchars($product['categoria_id']) . "</td>";
            echo "<td>" . htmlspecialchars($product['nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($product['descripcion']) . "</td>";
            echo "<td>" . htmlspecialchars($product['precio']) . "</td>";
            echo "<td>" . htmlspecialchars($product['stock']) . "</td>";
            echo "<td>" . htmlspecialchars($product['oferta']) . "</td>";
            echo "<td><img src='" . BASE_URL . "uploads/productos/" . htmlspecialchars($product['imagen']) . "' alt='Imagen de " . htmlspecialchars($product['nombre']) . "' class='product-image'></td>";
            echo "<td>
                    <a href='" . BASE_URL . "products/delete/" . $product['id'] . "'>Eliminar</a>
                    <a href='" . BASE_URL . "products/edit/" . $product['id'] . "'>Editar</a>
                </td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    }

    if (empty($data)) {
        displayNoProductsMessage();
    } else {
        displayProductsTable($data);
    }
    ?>

    <div class="product-form">
        <h2>Agregar Nuevo Producto</h2>
        <form action="<?= BASE_URL; ?>products" method="POST" enctype="multipart/form-data">
            <label for="categoria_id">Categoría del producto:</label>
            <select name="data[categoria_id]" id="categoria_id" required>
                <option value="">Seleccione una categoría</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['id']); ?>">
                        <?= htmlspecialchars($category['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="text" name="data[nombre]"
                placeholder="Nombre del producto (Abrigo de Lana, Zapatillas Nike Fussion, ...)" required>
            <input type="text" name="data[descripcion]"
                placeholder="Descripcion del producto (Abrigo hecho de 100% lana de camello dorado...)" required>
            <input type="text" name="data[precio]" placeholder="Precio del producto (100€, 260€, ...)" required>
            <input type="number" name="data[stock]" placeholder="Stock del producto (10, 20, 60, ...)" required>
            <input type="text" name="data[oferta]" placeholder="Oferta del producto (0, 50%, 10%, ...)">
            <input type="file" name="imagen" id="imagen" class="custom-file-input" required>
            <input type="submit" value="Agregar Producto">
        </form>
    </div>

</div>