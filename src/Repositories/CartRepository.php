<?php
namespace Repositories;
use Lib\Database;

class CartRepository
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function findCart($sessionId, $userId = null)
    {
        $sql = "SELECT id FROM carts WHERE session_id = :session_id OR user_id = :user_id";
        $params = [
            ':session_id' => $sessionId,
            ':user_id' => $userId
        ];
        return $this->database->queryOne($sql, $params);
    }


    public function createCart($sessionId, $userId)
    {
        $sql = "INSERT INTO carts (session_id, user_id) VALUES (?, ?)";
        $this->database->execute($sql, [$sessionId, $userId]);
        return $this->database->lastInsertId();
    }

    public function getCartItems($cartId)
    {
        $sql = "SELECT ci.*, p.nombre, p.precio, p.stock 
                FROM cart_items ci 
                JOIN productOs p ON ci.product_id = p.id 
                WHERE ci.cart_id = :cart_id";
        return $this->database->customQuery($sql, [':cart_id' => $cartId]);
    }


    public function addToCart($cartId, $productId, $quantity, $price)
    {
        $sql = "INSERT INTO cart_items (cart_id, product_id, quantity, price) 
                VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE quantity = quantity + ?";
        return $this->database->execute($sql, [$cartId, $productId, $quantity, $price, $quantity]);
    }

    public function getItemQuantity($cartId, $productId)
    {
        $sql = "SELECT quantity FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id";
        $result = $this->database->queryOne($sql, [':cart_id' => $cartId, ':product_id' => $productId]);
        return $result['quantity'] ?? 0;
    }

    public function updateQuantity($cartId, $productId, $quantity)
    {
        $sql = "UPDATE cart_items SET quantity = :quantity WHERE cart_id = :cart_id AND product_id = :product_id";
        return $this->database->execute($sql, [':quantity' => $quantity, ':cart_id' => $cartId, ':product_id' => $productId]);
    }
    public function removeFromCart($cartId, $productId)
    {
        $sql = "DELETE FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id";
        $params = [':cart_id' => $cartId, ':product_id' => $productId];

        // Check if the item exists before trying to delete
        $checkSql = "SELECT COUNT(*) FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id";
        $count = $this->database->queryOne($checkSql, $params)['COUNT(*)'];
        error_log("CartRepository: Item count before delete: $count");

        $result = $this->database->execute($sql, $params);
        $affectedRows = $result;  // Assuming execute() now returns rowCount()

        error_log("CartRepository: Executing SQL: $sql with params: " . json_encode($params) . ". Affected rows: $affectedRows");

        return $affectedRows;
    }

    public function isEmptyCart($userId)
    {
        $sql = "SELECT COUNT(*) as item_count 
            FROM cart_items ci 
            JOIN carts c ON ci.cart_id = c.id 
            WHERE c.user_id = :user_id";

        $params = [':user_id' => $userId];

        $result = $this->database->queryOne($sql, $params);

        //  item_count = 0 ?  cart empty
        return $result['item_count'] == 0;
    }


    public function getCartTotal($cartId)
    {
        $sql = "SELECT SUM(quantity * price) as total FROM cart_items WHERE cart_id = :cart_id";
        $params = [':cart_id' => $cartId];
        $result = $this->database->queryOne($sql, $params);
        return $result['total'] ?? 0;
    }


    public function findCartByUserId($userId)
    {
        $sql = "SELECT id FROM carts WHERE user_id = :user_id";
        return $this->database->queryOne($sql, [':user_id' => $userId]);
    }

    public function clearCart($cartId)
    {
        $sql = "DELETE FROM cart_items WHERE cart_id = :cart_id";
        $params = [':cart_id' => $cartId];

        try {
            $result = $this->database->execute($sql, $params);
            return $result > 0; // Returns true if at least one row was affected
        } catch (\PDOException $e) {
            error_log("Error clearing cart: " . $e->getMessage());
            return false;
        }
    }


    public function getCartProductQuantity($cartId, $productId)
    {
        $sql = "SELECT quantity FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id";
        $params = [':cart_id' => $cartId, ':product_id' => $productId];
    
        $result = $this->database->queryOne($sql, $params);
    
        return $result['quantity'] ?? 0; //  no hay resultado, devuelve 0
    }
    

}
