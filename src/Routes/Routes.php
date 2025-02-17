<?php
namespace Routes;
use Controllers\ErrorController;
use Controllers\ProductController;
use Controllers\CategoryController;
use Controllers\CartController;
use Controllers\AuthController;
use Controllers\OrderController;
use Lib\Router;
use Security\Security;
use APIServices\ApiProductController;


class Routes
{
    public static function index()
    {

        /* LANDING PAGE */
        Router::add('GET', '/', function () {
            (new ProductController())->index();
        });
        
        Router::add('GET', '/orders', function () {
            (new OrderController())->getOrders();
        });

        

        /* AUTH */
        Router::add('GET', '/register', function () {
            (new AuthController())->register();
        });

        Router::add('POST', '/register', function () {
            (new AuthController())->register();
        });

        Router::add('GET', '/login', function () {
            (new AuthController())->login();
        });

        Router::add('POST', '/login', function () {
            (new AuthController())->login();
        });

        Router::add('GET', '/logout', function () {
            (new AuthController())->logout();
        });

        /* ADMIN FUNCTIONS */
        /* CATEGORIES */
        Router::add('GET', '/categories', function () {
            (new CategoryController())->index();
        });

        Router::add('POST', '/categories', function () {
            (new CategoryController())->addCategory();
        });
        
        Router::add('POST', '/categories/delete', function () {
            (new CategoryController())->deleteCategory();
        });

        Router::add('GET', '/categories/:id', function(int $id) {
            (new CategoryController())->showProducts($id);
        });
       
        
        
        /* PRODUCTS */
        Router::add('GET', '/products', function () {
            (new ProductController())->index();
        });
        
        Router::add('POST', '/products', function () {
            (new ProductController())->addProduct();
        });
        
        Router::add('GET', '/products/delete/:id', function(int $id) {
            (new ProductController())->deleteProduct($id);
        });

        Router::add('POST', '/products/delete/:id', function(int $id) {
            (new ProductController())->deleteProduct($id);
        });

        Router::add('GET', '/products/edit/:id', function(int $id) {
            (new ProductController())->editProduct($id);
        });
       
        Router::add('POST', '/products/edit/:id', function(int $id) {
            (new ProductController())->editProduct($id);
        });
        
        
        Router::add('GET', '/products/category/{id}', function(int $id) {
            (new CategoryController())->showProducts($id);
        });
        

        Router::add('GET', '/products/category/{id}', function(int $id) {
            (new CategoryController())->showProducts($id);
        });

        Router::add('GET', '/products/category/:id', function (int $id) {
            (new CategoryController())->showProducts($id);
        });

        Router::add('GET', '/api/products', function () {
            (new ApiProductController())->getProducts();
        });
        
        
        Router::add('GET', '/api/products/{id}', function ($id) {
            (new ApiProductController())->getProduct($id);
        });
        
        Router::add('POST', '/api/products', function () {
            (new ApiProductController())->addProduct();
        });
        
        Router::add('PUT', '/api/products/{id}', function ($id) {
            (new ApiProductController())->updateProduct($id);
        });
        
        Router::add('DELETE', '/api/products/{id}', function ($id) {
            (new ApiProductController())->deleteProduct($id);
        });
        
        Router::add('GET', '/api/categories', function () {
            (new ApiProductController())->getCategories();
        });
        
        // Añade aquí otras rutas API...
        
        

        /* CART */
        Router::add('GET', '/cart', function () {
            (new CartController())->displayCart();
        });
        
    
        Router::add('POST', '/cart/update/:id', function (int $id) {
            (new CartController())->updateQuantity($id);
        });
        
        Router::add('POST', '/cart/remove/:id', function (int $id) {
            (new CartController())->removeProduct($id);
        });
        
        /* ORDER */
        Router::add('POST', '/order/create', function(){
            (new OrderController()) -> createOrder();                
        });
        Router::add('POST', '/orders/updateOrderState', function(){
            (new OrderController()) -> updateOrderState();                
        });
        
        Router::add('POST', '/proceedToPay', function(){
            (new OrderController()) -> shippingForm();                
        });
        

        /* ERROR */
        Router::add('GET', '/Error', function () {
            ErrorController::error404();
        });
        
        Router::add('GET', '/confirmAccount/:token', function ($token) {
            if (Security::validateJWTToken($token)) {
                (new AuthController()) -> confirmAccount($token);
            } else {
                echo 'Invalid or expired token';
            }
        });
        
        Router::add('GET', '/forgot-password', function () {
            (new AuthController())->forgotPassword();
        });
        
        Router::add('POST', '/forgot-password', function () {
            (new AuthController())->forgotPassword();
        });
        
        Router::add('GET', '/reset-password/:token', function ($token) {
            (new AuthController())->resetPassword($token);
        });
        
        Router::add('POST', '/reset-password/:token', function ($token) {
            (new AuthController())->resetPassword($token);
        });

        Router::add('GET', '/cart/add/:id', function ($id) {
            (new CartController())->addProduct($id);
        });
        
        Router::add('POST', '/cart/add/:id', function ($id) {
            (new CartController())->addProduct($id);
        });
        
        
        Router::add('POST', '/order/create', function(){
           (new OrderController())->createOrder(); 
        });
        Router::add('GET', '/order/paypalSuccess', function(){
           (new OrderController())->paypalSuccess(); 
        });
        Router::add('GET', '/order/paypalCancel', function(){
           (new OrderController())->paypalCancel(); 
        });
        
        
        Router::dispatch();
    }
}

?>