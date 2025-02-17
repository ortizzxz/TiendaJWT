<?php
namespace Controllers;
use Lib\Pages;
use Services\CategoryService;

session_start();

class CategoryController
{
    private Pages $pages;
    private CategoryService $categoryService;

    public function __construct()
    {
        $this->pages = new Pages();
        $this->categoryService = new CategoryService();
    }

    public function index()
    {
        $categories = $this->categoryService->getAll();

        // Verificar si el usuario está autenticado y es admin
        if (isset($_SESSION['identity']) && $_SESSION['identity']['rol'] === 'admin') {
            // Si el usuario es admin, vista de gestión
            $this->pages->render('Category/management', ['categories' => $categories]);
        } else {
            // Si el usuario no es admin, vista normal
            $this->pages->render('Category/index', ['categories' => $categories]);
        }
    }


    public function addCategory()
    {
        // Verificar si el usuario está autenticado y es admin
        if (!isset($_SESSION['identity']) || $_SESSION['identity']['rol'] !== 'admin') {
            // Redirigir a la página de inicio o a una página de acceso denegado
            header("Location: " . BASE_URL . "products");
            exit(); // Detener la ejecución del script
        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
            //sanitizar entrada
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
            $nombre = trim($nombre);

            //validar entrada
            if (empty($nombre)) {
                $_SESSION['error'] = "El nombre de la categoría no puede estar vacío.";
                header("Location: " . BASE_URL . "categories");
                exit();
            }

            if (strlen($nombre) > 15) {
                $_SESSION['error'] = "El nombre de la categoría es demasiado largo.";
                header("Location: " . BASE_URL . "categories");
                exit();
            } else if (strlen($nombre) < 3) {
                $_SESSION['error'] = "El nombre de la categoría es demasiado corto.";
                header("Location: " . BASE_URL . "categories");
                exit();
            }

            $result = $this->categoryService->addCategory($nombre);

            if ($result) {
                $_SESSION['success'] = "Categoría agregada con éxito.";
            } else {
                $_SESSION['error'] = "No se pudo agregar la categoría.";
            }

            header("Location: " . BASE_URL . "categories"); // aqui el render no funciona?
            exit();
        } else {
            header("Location: " . BASE_URL);
            exit();
        }
    }

    public function showProducts($categoryId)
    {
        // Get products by category ID
        $products = $this->categoryService->getProductsByCategory($categoryId);
        // Check if the category exists and has products
        if (empty($products)) {
            $_SESSION['error'] = "No hay productos disponibles en esta categoría.";
            header("Location: " . BASE_URL . "categories");
            exit();
        }

        // Render the view with products
        $this->pages->render('Product/index', ['data' => $products]);
    }


    public function deleteCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = $_POST['id']; // ID de la categoría a eliminar
            if( $id == $_ENV['SAFE_CATEGORY']) {
                $_SESSION['error'] = "Categoria no eliminable.";
                header("Location: " . BASE_URL . "categories");
                exit();
            }

            // Obtener productos asociados a la categoría
            $products = $this->categoryService->getProductsByCategory($id);
    
            if (!empty($products)) {
                // Mover productos a la categoría temporal con id = 0
                $moved = $this->categoryService->updateProductCategory($id, $_ENV['SAFE_CATEGORY']);
                if (!$moved) {
                    // Si no se pueden mover los productos, mostrar error
                    $_SESSION['error'] = "No se pudieron mover los productos de la categoría.";
                    header("Location: " . BASE_URL . "categories");
                    exit();
                }
            }
    
            // Intentar eliminar la categoría después de mover los productos
            $deleted = $this->categoryService->deleteCategory($id);
    
            if ($deleted) {
                $_SESSION['success'] = "Categoría eliminada con éxito.";
            } else {
                $_SESSION['error'] = "No se pudo eliminar la categoría. Verifica las restricciones.";
            }
    
            // Redirigir a la lista de categorías
            header("Location: " . BASE_URL . "categories");
            exit();
        }
    }
    


}