<?php
namespace Services;
use Repositories\OrderRepository;
use Models\Order;

class OrderService
{
    private OrderRepository $orderRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
    }

    public function createOrder(Order $order, array $cartItems)
    {
        $this->orderRepository->beginTransaction();

        try {
            $orderId = $this->orderRepository->createOrder($order);

            foreach ($cartItems['items'] as $item) {
                $this->orderRepository->createOrderLine($orderId, $item['id'], $item['quantity']);
            }

            $this->orderRepository->commit();
            return $orderId;
        } catch (\Exception $e) {
            $this->orderRepository->rollback();
            error_log("Error al crear el pedido: " . $e->getMessage());
            throw $e;
        }
    }

    public function getOrders()
    {
        // Solicitar los pedidos del repositorio
        return $this->orderRepository->getOrders();
    }
    public function getOrdersByClient($clienteId)
    {
        // Solicitar los pedidos del repositorio
        return $this->orderRepository->getOrdersByClient($clienteId);
    }

    private function createOrderLines(int $orderId, array $cartItems): bool
    {
        try {
            foreach ($cartItems as $item) {
                if (isset($item['id']) && isset($item['quantity'])) {
                    $success = $this->orderRepository->createOrderLine($orderId, $item['id'], $item['quantity']);
                    if (!$success) {
                        throw new \Exception("Error al insertar línea de pedido");
                    }
                } else {
                    throw new \Exception("Item inválido en el carrito");
                }
            }
            return true;
        } catch (\Exception $e) {
            error_log("Error en createOrderLines: " . $e->getMessage());
            return false;
        }
    }

    // In OrderService.php

    public function updateOrderState($orderId, $newState)
    {
        return $this->orderRepository->updateOrderState($orderId, $newState);
    }


}
