<?php
namespace APIServices;

use Services\ProductService;
use Models\Product;

class ApiProductController
{
    private ProductService $productService;

    public function __construct()
    {
        $this->productService = new ProductService();
    }

    // Get all products
    public function getAll()
    {
        $products = $this->productService->getAll();
        $this->sendJsonResponse($products);
    }
    public function get($endpoint)
    {
        $url = BASE_URL . $endpoint;
        
        // Using file_get_contents() for simplicity or cURL for more flexibility
        $response = file_get_contents($url);
        
        // You could use cURL if you need more control over the request

        if ($response === FALSE) {
            return null;
        }
        
        return json_decode($response, true); // Assuming the response is valid JSON
    }

    // Get categories
    public function getCategories()
    {
        $categories = $this->productService->getCategories();
        $this->sendJsonResponse($categories);
    }

    // Get products from the product service
    public function getProducts()
    {
        $products = $this->productService->getAll(); // ankle
        $this->sendJsonResponse($products);
    }

    // Get a single product by ID
    public function getProduct($id)
    {
        $product = $this->productService->getById($id);
        if ($product) {
            $this->sendJsonResponse($product);
        } else {
            $this->sendJsonResponse(['error' => 'Producto no encontrado'], 404);
        }
    }

    // Add a new product
    public function addProduct()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $product = Product::fromArray($data);

        if ($product->validation()) {
            try {
                if ($this->productService->save($product)) {
                    $this->sendJsonResponse(['message' => 'Producto agregado con éxito'], 201);
                } else {
                    $this->sendJsonResponse(['error' => 'Error al guardar el producto'], 500);
                }
            } catch (\Exception $e) {
                $this->sendJsonResponse(['error' => $e->getMessage()], 500);
            }
        } else {
            $this->sendJsonResponse(['error' => Product::getErrors()], 400);
        }
    }

    // Update an existing product
    public function updateProduct($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $product = Product::fromArray($data);
        $product->setId($id);

        if ($product->validation()) {
            try {
                if ($this->productService->updateProduct($product->getId(), $product)) {
                    $this->sendJsonResponse(['message' => 'Producto actualizado con éxito']);
                } else {
                    $this->sendJsonResponse(['error' => 'Error al actualizar el producto'], 500);
                }
            } catch (\Exception $e) {
                $this->sendJsonResponse(['error' => $e->getMessage()], 500);
            }
        } else {
            $this->sendJsonResponse(['error' => Product::getErrors()], 400);
        }
    }

    // Delete a product
    public function deleteProduct($id)
    {
        try {
            if ($this->productService->deleteById($id)) {
                $this->sendJsonResponse(['message' => 'Producto eliminado con éxito']);
            } else {
                $this->sendJsonResponse(['error' => 'Error al eliminar el producto'], 500);
            }
        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    // Send a JSON response to the client
    private function sendJsonResponse($data, $statusCode = 200)
{
    // Set content type header to JSON
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code($statusCode); // Set the appropriate HTTP status code

    // Ensure no other output is generated
    ob_clean();
    flush();

    try {
        // JSON encode with proper error handling
        $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        echo $jsonData;  // Output the JSON response
    } catch (\JsonException $e) {
        // Handle JSON encoding errors
        error_log("JSON encoding failed: " . $e->getMessage());
        error_log("Data before encoding: " . print_r($data, true));
        http_response_code(500);  // Internal server error
        echo json_encode(['error' => 'Failed to encode response']);
    }

    // Ensure script stops to prevent other content (HTML) from being sent
    exit();
}

}
