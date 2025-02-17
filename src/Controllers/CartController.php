<?php
namespace Controllers;
use Lib\Pages;
use Services\ProductService;
use Models\Cart;
use Services\CartService;

session_start();

class CartController
{
    private Pages $pages;
    private ProductService $productService;
    private CartService $cartService;

    public function __construct()
    {
        $this->pages = new Pages();
        $this->productService = new ProductService();
        $this->cartService = new CartService();
    }

    public function addProduct($id)
    {
        $product = $this->productService->getById($id);
        if (!$product) {
            $_SESSION['error'] = "Producto no encontrado.";
            header('Location: ' . BASE_URL . 'products');
            exit;
        }

        if ($product['stock'] <= 0) {
            $_SESSION['error'] = "Este producto está agotado.";
            header('Location: ' . BASE_URL . 'products');
            exit;
        }

        $userId = $_SESSION['identity']['id'] ?? null;
        if ($userId) {
            // Usuario autenticado, obtener carrito de la BD
            $cartId = $this->cartService->getCartForUser($userId);
            $currentQuantity = $this->cartService->getCartProductQuantity($cartId, $id);
        } else {
            // Usuario no autenticado, obtener carrito de sesión
            $currentQuantity = $_SESSION['cart'][$id]['quantity'] ?? 0;
        }

        // si la cantidad nueva supera el stock
        if (($currentQuantity + 1) > $product['stock']) {
            $_SESSION['error'] = "No puedes añadir más unidades de este producto, stock insuficiente.";
            header('Location: ' . BASE_URL . 'products');
            exit;
        }

        // si hay stock suficiente, añadir el producto
        if ($userId) {
            $result = $this->cartService->addToCart($cartId, $id, 1, $product['precio']);
        } else {
            $result = $this->addToSessionCart($id, 1, $product['precio']);
        }

        $_SESSION['success'] = $result ? "Producto añadido exitosamente." : "Error al añadir producto.";
        header('Location: ' . BASE_URL . 'products');
        exit;
    }


    private function addToSessionCart($productId, $quantity, $price)
    {
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = [
                'quantity' => $quantity,
                'price' => $price
            ];
        }
        return true;
    }

    public function displayCart()
    {
        $userId = $_SESSION['identity']['id'] ?? null;
        if ($userId) {
            $cartId = $this->cartService->getCartForUser($userId);
            $cartItems = $this->cartService->getCartItems($cartId);
            $total = $this->cartService->getCartTotal($cartId);
        } else {
            $cartItems = $this->getSessionCartItems();
            $total = $this->calculateSessionCartTotal();
        }

        $this->pages->render('Cart/display', [
            'cartItems' => $cartItems,
            'total' => $total
        ]);
    }

    private function getSessionCartItems()
    {
        $cartItems = [];
        foreach ($_SESSION['cart'] ?? [] as $productId => $item) {
            $product = $this->productService->getById($productId);
            $cartItems[] = [
                'id' => $productId,
                'nombre' => $product['nombre'],
                'precio' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity'],
                'stock' => $product['stock'] // Add this line to include stock information
            ];
        }
        return $cartItems;
    }


    private function calculateSessionCartTotal()
    {
        $total = 0;
        foreach ($_SESSION['cart'] ?? [] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function removeProduct($productId)
    {
        $userId = $_SESSION['identity']['id'] ?? null;

        if ($userId) {
            $cartId = $this->cartService->getCartForUser($userId);
            $result = $this->cartService->removeFromCart($cartId, $productId);
        } else {
            unset($_SESSION['cart'][$productId]);
            $result = true;
        }

        if ($result) {
            $_SESSION['success'] = "Producto eliminado del carrito exitosamente.";
        } else {
            $_SESSION['error'] = "Ha habido un fallo al eliminar el producto del carrito.";
        }

        error_log("Final result: " . ($_SESSION['success'] ?? $_SESSION['error']));
        header('Location: ' . BASE_URL . 'cart');
        exit;
    }


    public function updateQuantity($id)
{
    $action = $_POST['action'] ?? '';
    if (!in_array($action, ['increase', 'decrease'])) {
        $_SESSION['error'] = "Acción inválida.";
        header('Location: ' . BASE_URL . 'cart');
        exit;
    }

    $product = $this->productService->getById($id);
    if (!$product) {
        $_SESSION['error'] = "Producto no encontrado.";
        header('Location: ' . BASE_URL . 'cart');
        exit;
    }

    $userId = $_SESSION['identity']['id'] ?? null;
    if ($userId) {
        $cartId = $this->cartService->getCartForUser($userId);
        $currentQuantity = $this->cartService->getCartProductQuantity($cartId, $id);
    } else {
        $currentQuantity = $_SESSION['cart'][$id]['quantity'] ?? 0;
    }

    if ($action === 'increase') {
        if ($currentQuantity + 1 > $product['stock']) {
            $_SESSION['error'] = "No puedes añadir más unidades, stock insuficiente.";
            header('Location: ' . BASE_URL . 'cart');
            exit;
        }
    }

    if ($userId) {
        $result = $this->cartService->updateQuantity($cartId, $id, $action);
    } else {
        $result = $this->updateSessionCartQuantity($id, $action);
    }

    $_SESSION['success'] = $result ? "Carrito actualizado correctamente." : "Error al actualizar carrito.";
    header('Location: ' . BASE_URL . 'cart');
    exit;
}


    private function updateSessionCartQuantity($productId, $action)
    {
        if (!isset($_SESSION['cart'][$productId])) {
            return false;
        }

        if ($action === 'increase') {
            $_SESSION['cart'][$productId]['quantity']++;
        } elseif ($action === 'decrease') {
            $_SESSION['cart'][$productId]['quantity'] = max(1, $_SESSION['cart'][$productId]['quantity'] - 1);
        }

        return true;
    }
}
