
<div class="category-form">
   <h2>Agregar Nueva Categoría</h2>
   <form action="<?= BASE_URL; ?>categories" method="POST">
       <input type="text" name="nombre" placeholder="Nombre de la categoría" required>
       <input type="submit" value="Agregar Categoría">
   </form>
    <?=displayMessage() ?>
</div>


<?php
    function displayMessage() {
        if (isset($_SESSION['error'])) {
            echo "<div class='error-message'>" . htmlspecialchars($_SESSION['error']) . "</div>";
            unset($_SESSION['error']);
        } elseif (isset($_SESSION['success'])) {
            echo "<div class='success-message'>" . htmlspecialchars($_SESSION['success']) . "</div>";
            unset($_SESSION['success']);
        }
    }


    function displayNoCategoriesMessage() {
        echo "<h2 id='categoryTitle'>No hay categorías disponibles.</h2>";
    }

    function displayCategoriesTable(array $categories) {
        echo "<div id='categoryContainer'>";
        echo "<h2 id='categoryTitle'>Lista de Categorías</h2>";
        
        echo "<table class='styled-table'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Nombre</th>";
        echo "<th>Acción</th>"; 
        echo "</tr>";
        echo "</thead>";
        
        echo "<tbody>";
        
        foreach ($categories as $category) {
            
            if ($category['id'] == $_ENV['SAFE_CATEGORY']){//saltarse al seguro
                continue;
            }

            echo "<tr>";
            echo "<td>" . htmlspecialchars($category['id']) . "</td>";
            echo "<td>" . htmlspecialchars($category['nombre']) . "</td>";
            // boton de eliminar
            echo "<td>
                    <form action='" . BASE_URL . "categories/delete' method='POST' style='display:inline;'>
                        <input type='hidden' name='id' value='" . htmlspecialchars($category['id']) . "'>
                        <input type='submit' value='Eliminar'>
                    </form>
                </td>";
            echo "</tr>";
        }
        
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    }

    if (empty($categories)) {
        displayNoCategoriesMessage();
    } else {
        displayCategoriesTable($categories);
    }
?>


