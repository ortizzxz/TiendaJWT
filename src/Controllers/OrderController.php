<?php
namespace Controllers;

use Lib\EmailSender;
use Lib\Pages;
use Security\Security;
use Services\CartService;
use Services\OrderService;
use Services\ProductService;
use Models\Order;
use Models\User;
use Services\PayPalServices;


session_start();

class OrderController
{
    private Pages $pages;
    private OrderService $orderService;
    private ProductService $productService;
    private CartService $cartService;

    private EmailSender $emailSender;
    private PayPalServices $paypalService;


    public function __construct()
    {
        $this->pages = new Pages();
        $this->orderService = new OrderService();
        $this->productService = new ProductService();
        $this->cartService = new CartService();
        $this->emailSender = new EmailSender();
        $this->paypalService = new PayPalServices();

    }

    public function createOrder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "cart");
            exit();
        }

        $userId = $_SESSION['identity']['id'] ?? null;
        if (!$userId) {
            $_SESSION['error'] = 'Debe iniciar sesión para realizar el pedido';
            header("Location: " . BASE_URL);
            exit();
        }

        $cartId = $this->cartService->getCartForUser($userId);
        if ($this->cartService->isEmptyCart($userId)) {
            $_SESSION['error'] = 'El carrito está vacío';
            header("Location: " . BASE_URL . "cart");
            exit();
        }

        $cartItems = $this->cartService->getCartItems($cartId);

        // Aquí transformamos los $cartItems al formato adecuado
        $cartItemsFormatted = ['items' => []];
        foreach ($cartItems as $item) {
            $cartItemsFormatted['items'][] = [
                'id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ];
        }

        if (!$this->checkStockAvailability($cartItemsFormatted['items'])) {
            $_SESSION['error'] = 'Uno o más productos no tienen suficiente stock.';
            header("Location: " . BASE_URL . "cart");
            exit();
        }

        $user = User::fromArray($_SESSION['identity']);

        $_POST['shipping']['userId'] = $user->getId();
        $_POST['shipping']['cost'] = $this->cartService->getCartTotal($cartId);
        $order = Order::fromArray($_POST['shipping']);

        if (!$order->validation()) {
            $this->pages->render('Order/shippingForm');
            return;
        }

        // Ahora pasamos los cartItems ya formateados
        try {
            $orderId = $this->orderService->createOrder($order, $cartItemsFormatted);
        } catch (\Exception $e) {
            $_SESSION['error'] = "Ha habido un error con el pedido: " . $e->getMessage();
            header("Location: " . BASE_URL);
            exit();
        }

        // Actualizar el stock de los productos
        $this->updateProductStock($cartItemsFormatted['items']);

        // Enviar confirmación por email
        if (
            $this->emailSender->sendEmail(
                $user->getEmail(),
                $user->getName(),
                'Payment Confirmation',
                $this->getBodySchema($cartItemsFormatted['items'], $order->getCost())
            )
        ) {
            echo 'Mensaje enviado';
        } else {
            echo 'Mensaje no enviado';
        }

        // Crear el pago con PayPal
        $paypalUrl = $this->paypalService->createPayment(
            $order->getCost(),
            "EUR",
            BASE_URL . "order/paypalSuccess",
            BASE_URL . "order/paypalCancel"
        );

        if ($paypalUrl) {
            header("Location: " . $paypalUrl);
            exit();
        } else {
            $_SESSION['error'] = "Error al conectar con PayPal.";
            header("Location: " . BASE_URL . "cart");
            exit();
        }
    }

    public function shippingForm()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->pages->render('Order/shippingForm');
        } else {
            header('Location: ' . BASE_URL);
        }
    }

    private function isUserLoggedIn(): bool
    {
        return isset($_SESSION['identity']);
    }

    private function isCartEmpty($userId): bool
    {
        return $this->cartService->isEmptyCart($userId);
    }


    private function checkStockAvailability(array $cartItems): bool
    {
        return true; // CAMBIAR
    }

    private function updateProductStock(array $cartItems): void
    {
        if (isset($cartItems['items']) && is_array($cartItems['items'])) {
            foreach ($cartItems['items'] as $item) {
                if (isset($item['id']) && isset($item['quantity'])) {
                    $this->productService->updateStock($item['id'], $item['quantity']);
                }
            }
        }
    }
    public function getOrders()
    {
        $clienteId = $_SESSION['identity']['id'];

        if ($_SESSION['identity']['rol'] == $_ENV['ADMIN']) {
            $pedidos = $this->orderService->getOrders();
        } else {
            $pedidos = $this->orderService->getOrdersByClient($clienteId);
        }

        $this->pages->render('Order/myOrder', ['pedidos' => $pedidos]);
    }

    public function updateOrderState()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['estado']) && isset($_POST['order_id'])) {
            $orderId = $_POST['order_id'];
            $newState = $_POST['estado'];

            $this->orderService->updateOrderState($orderId, $newState);

            header('Location: ' . BASE_URL . 'orders');
            exit;
        }

    }

    public function getBodySchema(array $cartItems, float $total)
    {
        $body = "<h1>Confirmación de Pago</h1>";
        $body .= "<p>¡Gracias por tu compra! Aquí tienes los detalles de tu pedido:</p>";

        if (!empty($cartItems)) {
            $body .= "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
            $body .= "<thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th></tr></thead>";
            $body .= "<tbody>";

            foreach ($cartItems as $item) {
                $subtotal = $item['precio'] * $item['quantity'];
                $body .= "<tr>";
                $body .= "<td>" . htmlspecialchars($item['nombre']) . "</td>";
                $body .= "<td>" . number_format($item['precio'], 2, ',', '.') . " €</td>";
                $body .= "<td>" . (int) $item['quantity'] . "</td>";
                $body .= "<td>" . number_format($subtotal, 2, ',', '.') . " €</td>";
                $body .= "</tr>";
            }

            $body .= "</tbody>";
            $body .= "</table>";

            $body .= "<p><strong>Total: " . number_format($total, 2, ',', '.') . " €</strong></p>";
        } else {
            $body .= "<p>No se encontraron artículos en tu carrito.</p>";
        }

        return $body;
    }


    public function paypalSuccess()
    {
        if (!isset($_GET['paymentId'], $_GET['PayerID'])) {
            $_SESSION['error'] = "Pago no válido o cancelado.";
            header("Location: " . BASE_URL . "cart");
            exit();
        }

        $paymentId = $_GET['paymentId'];
        $payerId = $_GET['PayerID'];

        // Verificar el pago con PayPal
        $paymentVerified = $this->paypalService->executePayment($paymentId, $payerId);

        if ($paymentVerified) {
            $cartId = $this->cartService->getCartForUser($_SESSION['identity']['id']);
            $_SESSION['success'] = "Pago realizado con éxito.";
            $this->cartService->clearCart($cartId);
            header("Location: " . BASE_URL . "orders");
        } else {
            $_SESSION['error'] = "Error al procesar el pago.";
            header("Location: " . BASE_URL . "cart");
        }
        exit();
    }


    public function paypalCancel()
    {
        $_SESSION['error'] = "El pago fue cancelado.";
        header("Location: " . BASE_URL);
    }


}