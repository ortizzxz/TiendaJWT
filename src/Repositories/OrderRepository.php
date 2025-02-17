<?php
namespace Repositories;
use Lib\Database;
use Models\Order;
use PDO;

class OrderRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function createOrder(Order $order)
    {
        try {
            $sql = "INSERT INTO pedidos (usuario_id, provincia, localidad, direccion, coste, estado, fecha, hora) 
            VALUES (:usuario_id, :provincia, :localidad, :direccion, :coste, :estado, :fecha, :hora)";

            $stmt = $this->db->prepare($sql);

            $params = [
                ':usuario_id' => $order->getUserId(),
                ':provincia' => $order->getProvince(),
                ':localidad' => $order->getLocality(),
                ':direccion' => $order->getAddress(),
                ':coste' => $order->getCost(),
                ':estado' => $order->getOrderState() ?? 'pendiente',
                ':fecha' => date('Y-m-d'),
                ':hora' => date('H:i:s')
            ];

            $stmt->execute($params);

            $orderId = $this->db->getConnection()->lastInsertId();

            if (!$orderId) {
                throw new \Exception("No se pudo obtener el ID del pedido insertado");
            }

            return $orderId;
        } catch (\PDOException $e) {
            error_log("Error de PDO en createOrder: " . $e->getMessage());
            throw new \Exception("Error al insertar el pedido en la base de datos");
        } catch (\Exception $e) {
            error_log("Error general en createOrder: " . $e->getMessage());
            throw $e;
        }
    }


    public function updateOrderState($orderId, $newState)
    {
        $query = "UPDATE pedidos SET estado = :newState WHERE id = :orderId";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':newState', $newState, PDO::PARAM_STR);
        $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);

        return $stmt->execute();
    }


    public function getOrdersByClient($clienteId)
    {
        // Consulta SQL para obtener los pedidos del cliente
        $stmt = $this->db->prepare('SELECT * FROM pedidos WHERE usuario_id = :cliente_id');
        $stmt->bindParam(':cliente_id', $clienteId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrders()
    {
        // Consulta SQL para obtener los pedidos del cliente
        $stmt = $this->db->prepare('SELECT * FROM pedidos');
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function createOrderLine(int $orderId, int $productId, int $quantity): bool
    {
        $sql = "INSERT INTO lineas_pedidos (id, pedido_id, producto_id, unidades) VALUES (null, :pedido_id, :producto_id, :unidades)";
        $params = [
            ':pedido_id' => $orderId,
            ':producto_id' => $productId,
            ':unidades' => $quantity
        ];
        return $this->db->execute($sql, $params);
    }


    public function beginTransaction(): void
    {
        $this->db->getConnection()->beginTransaction();
    }

    public function commit(): void
    {
        $this->db->getConnection()->commit();
    }

    public function rollback(): void
    {
        $this->db->getConnection()->rollBack();
    }



}

