<div class="container">
    <?php
    function displayMessage()
    {
        if (isset($_SESSION['error'])) {
            echo "<div class='error-message'>" . htmlspecialchars($_SESSION['error']) . "</div>";
            unset($_SESSION['error']);
        } elseif (isset($_SESSION['success'])) {
            echo "<div class='success-message'>" . htmlspecialchars($_SESSION['success']) . "</div>";
            unset($_SESSION['success']);
        }
    }

    function displayNoCategoriesMessage()
    {
        echo "<h2 id='categoryTitle'>No hay categorías disponibles.</h2>";
    }

    function displayCategoriesGrid(array $categories)
{
    echo "<h2 id='categoryTitle'>Lista de Categorías</h2>";
    echo "<div class='categories-grid'>";

    foreach ($categories as $category) {
        if($category['id'] == $_ENV['SAFE_CATEGORY']){
            continue; // saltarse la safe cateogry
        }
        
        echo "<div class='category-card'>"; 
        echo "<a href='" . BASE_URL . "products/category/" . htmlspecialchars($category['id']) . "' class='category-link'>";
        
        echo "<h3 class='category-name'>" . htmlspecialchars($category['nombre']) . "</h3>";
        
        echo "</a>";
        echo "</div>"; 
    }

    echo "</div>";
}

    displayMessage();

    if (empty($categories)) {
        displayNoCategoriesMessage();
    } else {
        displayCategoriesGrid($categories);
    }
    ?>
</div>
