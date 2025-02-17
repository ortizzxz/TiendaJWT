<?php
namespace Repositories;
use Lib\Database;
use PDO;

class ProductRepository
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM productos";
        $result = $this->database->query($sql);
        if ($result) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return [];
        }
    }
    public function getCategories()
    {
        $sql = "SELECT * FROM categorias";
        $result = $this->database->query($sql);
        if ($result) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return [];
        }
    }

    public function getById(int $id): array
    {
        $stmt = $this->database->prepare("SELECT * FROM productos WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function getStockById(int $id): int
    {
        $stmt = $this->database->prepare("SELECT stock FROM productos WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int) $result['stock'] : 0;
    }

    public function updateStock(int $productId, int $quantity): bool
    {
        $currentStock = $this->getStockById($productId);

        if ($currentStock < $quantity) {
            // No se puede actualizar si no hay suficiente stock
            return false; 
        }

        $newStock = $currentStock - $quantity; //calculamos el nuevo stock

        $stmt = $this->database->prepare("UPDATE productos SET stock = :stock WHERE id = :id");
        $stmt->bindParam(':stock', $newStock, PDO::PARAM_INT);
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);

        return $stmt->execute();
    }


    public function save($product)
    {
        $sql = "INSERT INTO productos (id, categoria_id, nombre, descripcion, precio, stock, oferta, fecha, imagen) 
                    VALUES (null, :categoria_id, :nombre, :descripcion, :precio, :stock, :oferta, :fecha, :imagen)";

        $oferta = $product->getOferta() ?: null;

        $data = [
            'categoria_id' => $product->getCategoriaId(),
            'nombre' => $product->getNombre(),
            'descripcion' => $product->getDescripcion(),
            'precio' => $product->getPrecio(),
            'stock' => $product->getStock(),
            'oferta' => $oferta,
            'fecha' => date('Y-m-d H:i:s'),
            'imagen' => $product->getImagen()
        ];

        try {
            if (!$this->database->execute($sql, $data)) {
                return false;
            }
            return true;
        } catch (\PDOException $e) {
            error_log("Error al guardar el producto: " . $e->getMessage());
            return false;
        }
    }

    public function deleteById(int $id): bool {
        $db = $this->database->getConnection();
    
        try {
            $stmt = $db->prepare("DELETE FROM productos WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
            return $stmt->execute();
        } catch (\Exception $e) {
            throw new \Exception("Error al eliminar el producto: " . $e->getMessage());
        }
    }

    public function updateProduct(int $id, $productData): bool
    {
        // Step 1: Retrieve the current product from the database to check if it exists
        $currentProduct = $this->getById($id);
    
        if (empty($currentProduct)) {
            // Product doesn't exist
            return false;
        }
    
        // Step 2: Prepare the SQL query for updating the product
        $sql = "UPDATE productos 
                SET 
                    categoria_id = :categoria_id, 
                    nombre = :nombre, 
                    descripcion = :descripcion, 
                    precio = :precio, 
                    stock = :stock, 
                    oferta = :oferta, 
                    imagen = :imagen 
                WHERE id = :id";
    
        // Step 3: Prepare the data to be bound to the SQL query
        $oferta = isset($productData['oferta']) ? $productData['oferta'] : null;
    
        $data = [
            'id' => $id,
            'categoria_id' => $productData['categoria_id'],
            'nombre' => $productData['nombre'],
            'descripcion' => $productData['descripcion'],
            'precio' => $productData['precio'],
            'stock' => $productData['stock'],
            'oferta' => $oferta,
            'imagen' => $productData['imagen']
        ];
    
        // Step 4: Execute the query to update the product
        try {
            $stmt = $this->database->prepare($sql);
            return $stmt->execute($data);
        } catch (\PDOException $e) {
            // Log error if any issue occurs
            error_log("Error al actualizar el producto: " . $e->getMessage());
            return false;
        }
    }
    

}

