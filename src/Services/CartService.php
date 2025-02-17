<?php
namespace Services;
use Repositories\CartRepository;

class CartService
{
    private CartRepository $cartRepository;

    public function __construct()
    {
        $this->cartRepository = new CartRepository();
    }

    public function getOrCreateCart($sessionId, $userId = null)
    {
        $cart = $this->cartRepository->findCart($sessionId, $userId);

        if (!$cart) {
            return $this->cartRepository->createCart($sessionId, $userId);
        }

        return $cart['id'];
    }

    public function getCartItems($cartId)
    {
        return $this->cartRepository->getCartItems($cartId);
    }

    public function addToCart($cartId, $productId, $quantity, $price)
    {
        return $this->cartRepository->addToCart($cartId, $productId, $quantity, $price);
    }

    public function updateQuantity($cartId, $productId, $action)
    {
        $currentQuantity = $this->cartRepository->getItemQuantity($cartId, $productId);
        $newQuantity = $action === 'increase' ? $currentQuantity + 1 : max(1, $currentQuantity - 1);
        return $this->cartRepository->updateQuantity($cartId, $productId, $newQuantity);
    }


    public function removeFromCart($cartId, $productId)
    {
        error_log("CartService: Attempting to remove product $productId from cart $cartId");
        $affectedRows = $this->cartRepository->removeFromCart($cartId, $productId);
        error_log("CartService: Affected rows: $affectedRows");
        return $affectedRows > 0;
    }
    
    public function isEmptyCart($userId){
        return $this->cartRepository->isEmptyCart($userId); 
    }

    public function getCartTotal($cartId)
    {
        return $this->cartRepository->getCartTotal($cartId);
    }

    public function getCartForUser($userId)
    {
        $cart = $this->cartRepository->findCartByUserId($userId);

        if (!$cart) {
            // Crear si no existe
            return $this->cartRepository->createCart(session_id(), $userId);
        }
        return $cart['id'];
    }

    public function transferSessionCartToDatabase($userId)
    {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            return;
        }

        $cartId = $this->getCartForUser($userId);

        foreach ($_SESSION['cart'] as $productId => $item) {
            $this->addToCart($cartId, $productId, $item['quantity'], $item['price']);
        }

        // Clear the session cart after transfer
        unset($_SESSION['cart']);
    }

    public function clearCart($cartId){
        $cart = $this->cartRepository->clearCart($cartId);
    }


    public function getCartProductQuantity($cartId, $id){
        return $this->cartRepository->getCartProductQuantity($cartId, $id);
    }
}
