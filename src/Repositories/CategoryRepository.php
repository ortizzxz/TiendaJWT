<?php
namespace Repositories;

use Lib\Database;
use PDO;

class CategoryRepository
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM categorias";
        $result = $this->database->query($sql);

        if ($result) {
            return $result->fetchAll();
        } else {
            return [];
        }
    }

    public function addCategory($nombre): bool
    {
        $sql = "INSERT INTO categorias (nombre) VALUES (:nombre)";
        $data = [
            'nombre' => $nombre
        ];

        try {
            return $this->database->execute($sql, $data); // Usar el método execute para ejecutar la consulta
        } catch (\PDOException $e) {
            return false; // manejo de errores
        }
    }

    public function getProductsByCategory($categoryId)
    {
        $query = "SELECT * FROM productos WHERE categoria_id = :category_id";
        $stmt = $this->database->prepare($query);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM categorias WHERE id = :id";

        return $this->database->execute($sql, [':id' => $id]);
    }

    public function updateProductCategory($idFrom, $idTo): bool
{
    $sql = "UPDATE productos SET categoria_id = :idTo WHERE categoria_id = :idFrom";
    $data = [
        'idTo' => $idTo,
        'idFrom' => $idFrom
    ];
    try {
        return $this->database->execute($sql, $data);
    } catch (\PDOException $e) {
        return false;
    }
}

}
