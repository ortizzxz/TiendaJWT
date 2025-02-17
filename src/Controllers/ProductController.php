<?php
namespace Controllers;
use Models\Product;
use Lib\Pages;
use Services\ProductService;
use Services\CategoryService;
use APIServices\ApiProductController;

session_start();

class ProductController
{
    const ROLE_ADMIN = 'admin';
    private Pages $pages;
    private ProductService $productService;
    private CategoryService $categoryService;
    private ApiProductController $apiClient;


    public function __construct()
    {
        $this->apiClient = new ApiProductController(); 
        $this->pages = new Pages();
        $this->productService = new ProductService();
        $this->categoryService = new CategoryService();
    }

    public function index()
    {
        $data = $this->apiClient->get('api/products');
        $categories = $this->apiClient->get('api/categories');
        if ($data === null) {
            error_log("Failed to fetch products from API");
            $data = []; // Set to empty array to avoid null issues
        }
    
        if ($categories === null) {
            error_log("Failed to fetch categories from API");
            $categories = []; // Set to empty array to avoid null issues
        }
    
        // Check if the user is an admin or a normal user
        if (!isset($_SESSION['identity']) || $_SESSION['identity']['rol'] !== 'admin') {
            // Render the product index page for regular users
            $this->pages->render('Product/index', ['data' => $data]);
        } else {
            // Render the product management page for admin users
            $this->pages->render('Product/management', ['data' => $data, 'categories' => $categories]);
        }
    }
    

    public function addProduct()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['data'])) {
                $product = Product::fromArray($_POST['data']);

                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../../public/uploads/productos/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $imageName = uniqid() . '_' . basename($_FILES['imagen']['name']);
                    $imagePath = $uploadDir . $imageName;

                    $validMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    $maxSize = 4 * 1024 * 1024; // Max 4MB
                    if ($_FILES['imagen']['size'] > $maxSize) {
                        $_SESSION['addproduct'] = 'fail';
                        $_SESSION['errors'] = 'La imagen es demasiado grande. El tamaño máximo permitido es 2MB.';
                        header("Location: " . BASE_URL . "products");
                        exit();
                    }
                    if (!in_array($_FILES['imagen']['type'], $validMimeTypes)) {
                        $_SESSION['addproduct'] = 'fail';
                        $_SESSION['errors'] = 'El archivo debe ser una imagen válida (JPEG, PNG, GIF).';
                        header("Location: " . BASE_URL . "products");
                        exit();
                    }

                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $imagePath)) {
                        $product->setImagen($imageName);
                    } else {
                        $_SESSION['addproduct'] = 'fail';
                        $_SESSION['errors'] = 'Error al subir la imagen.';
                        header("Location: " . BASE_URL . "products");
                        exit();
                    }
                } else {
                    $_SESSION['addproduct'] = 'fail';
                    $_SESSION['errors'] = 'No se proporcionó una imagen válida.';
                    header("Location: " . BASE_URL . "products");
                    exit();
                }

                if ($product->validation()) {
                    try {
                        if ($this->productService->save($product)) {
                            $_SESSION['success'] = "Producto agregado con éxito.";
                            header("Location: " . BASE_URL . "products");
                            exit();
                        } else {
                            $_SESSION['addproduct'] = 'fail';
                            $_SESSION['errors'] = 'Error al guardar el producto.';
                            header("Location: " . BASE_URL . "products");
                            exit();
                        }
                    } catch (\Exception $e) {
                        $_SESSION['addproduct'] = 'fail';
                        $_SESSION['errors'] = $e->getMessage();
                        header("Location: " . BASE_URL . "products");
                        exit();
                    }
                } else {
                    $_SESSION['addproduct'] = 'fail';
                    $_SESSION['errors'] = Product::getErrors();
                    header("Location: " . BASE_URL . "products");
                    exit();
                }
            } else {
                $_SESSION['addproduct'] = 'fail';
                $_SESSION['errors'] = 'No se enviaron datos válidos.';
                header("Location: " . BASE_URL . "products");
                exit();
            }
        } else {
            $this->pages->render('Product/index');
        }
    }



    public function editProduct($id)
    {
        if ($_SESSION['identity']['rol'] == self::ROLE_ADMIN) {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $thisProduct = $this->productService->getById($id);
                $thisProduct = Product::fromArray($thisProduct);
                if ($thisProduct) {
                    $categories = $this->categoryService->getAll();
                    
                    $this->pages->render(
                        'Product/managementSingleProduct',
                        ['thisProduct' => $thisProduct->toArray(), 'categories' => $categories]
                    );
                } else {
                    $_SESSION['edit'] = 'fail';
                    $_SESSION['errors'] = 'Error al obtener el producto desde la BD';
                    $this->pages->render('Product/management');
                }
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['data'])) {
                    $product = Product::fromArray($_POST['data']);
                    $currentProduct = $this->productService->getById($id);
                    $currentProduct = Product::fromArray($currentProduct);

                    // Manejar la imagen (basado en addProduct)
                    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = __DIR__ . '/../../public/uploads/productos/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }

                        $imageName = uniqid() . '_' . basename($_FILES['imagen']['name']);
                        $imagePath = $uploadDir . $imageName;

                        $validMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
                        $maxSize = 4 * 1024 * 1024; // 4MB máximo
                        if ($_FILES['imagen']['size'] > $maxSize) {
                            $_SESSION['edit'] = 'fail';
                            $_SESSION['errors'] = 'La imagen es demasiado grande. Tamaño máximo: 4MB.';
                            header("Location: " . BASE_URL . "products/edit/$id");
                            exit();
                        }
                        if (!in_array($_FILES['imagen']['type'], $validMimeTypes)) {
                            $_SESSION['edit'] = 'fail';
                            $_SESSION['errors'] = 'El archivo debe ser una imagen válida (JPEG, PNG, GIF).';
                            header("Location: " . BASE_URL . "products/edit/$id");
                            exit();
                        }

                        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $imagePath)) {
                            $product->setImagen($imageName);

                            // Eliminar la imagen anterior, si existe
                            if ($currentProduct->getImagen() && file_exists($uploadDir . $currentProduct->getImagen())) {
                                unlink($uploadDir . $currentProduct->getImagen());
                            }
                        } else {
                            $_SESSION['edit'] = 'fail';
                            $_SESSION['errors'] = 'Error al subir la imagen.';
                            header("Location: " . BASE_URL . "products/edit/$id");
                            exit();
                        }
                    } else {
                        // Si no se sube una nueva imagen, mantener la imagen existente
                        $product->setImagen($currentProduct->getImagen());
                    }

                    // Validar y actualizar los datos del producto
                    if ($product->validation()) {
                        try {
                            if ($this->productService->updateProduct($id, $product->toArray())) {
                                $_SESSION['success'] = "Producto actualizado con éxito.";
                                header("Location: " . BASE_URL . "products");
                                exit();
                            } else {
                                $_SESSION['edit'] = 'fail';
                                $_SESSION['errors'] = 'Error al actualizar el producto.';
                                header("Location: " . BASE_URL . "products/edit/$id");
                                exit();
                            }
                        } catch (\Exception $e) {
                            $_SESSION['edit'] = 'fail';
                            $_SESSION['errors'] = $e->getMessage();
                            header("Location: " . BASE_URL . "products/edit/$id");
                            exit();
                        }
                    } else {
                        $_SESSION['edit'] = 'fail';
                        $_SESSION['errors'] = Product::getErrors();
                        header("Location: " . BASE_URL . "products/edit/$id");
                        exit();
                    }
                } else {
                    $_SESSION['edit'] = 'fail';
                    $_SESSION['errors'] = 'No se enviaron datos válidos.';
                    header("Location: " . BASE_URL . "products");
                    exit();
                }
            }
        } else {
            $this->pages->render('Product/management');
        }
    }

    public function deleteProduct(int $id)
    {

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            if (!isset($_SESSION['identity']) || $_SESSION['identity']['rol'] !== self::ROLE_ADMIN) {
                $_SESSION['errors'] = "No tienes permisos para realizar esta acción.";
                header("Location: " . BASE_URL . "products");
                exit();
            }

            try {
                if ($this->productService->deleteById($id)) {
                    $_SESSION['success'] = "Producto eliminado con éxito.";
                } else {
                    $_SESSION['errors'] = "Error al eliminar el producto. Es posible que no exista.";
                }
            } catch (\Exception $e) {
                $_SESSION['errors'] = "Error al intentar eliminar el producto: " . $e->getMessage();
            }

            header("Location: " . BASE_URL . "products");
            exit();
        } else {
            header("Location: " . BASE_URL);
        }
    }
}



?>