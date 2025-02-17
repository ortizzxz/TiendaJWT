<?php
use Security\Security;
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda</title>
</head>

<body>
    <nav>
        <ul>
            <?php if (isset($_SESSION['identity'])): ?>
                <?php if ($_SESSION['identity']['rol'] === 'admin'): ?>
                  <!-- SI ES ADMIN -->
                    <li><a class="active" href="<?= BASE_URL; ?>">Admin Dashboard</a></li>
                    <li><a href="<?= BASE_URL; ?>register">Registrar usuario</a></li>
                    <li><a href="<?= BASE_URL; ?>orders">Gestionar pedidos</a></li>
                    <li><a href="<?= BASE_URL; ?>products">Gestionar productos</a></li>
                    <li><a href="<?= BASE_URL; ?>categories">Gestionar categorías</a></li>
                <?php else: ?>
                  <!-- SI ES USUARIO NORMAL -->
                    <li><a class="active" href="<?= BASE_URL; ?>">Tienda Online</a></li>
                    <li><a href="<?= BASE_URL; ?>products">Productos</a></li>
                    <li><a href="<?= BASE_URL; ?>categories">Categorías</a></li>
                    <li><a href="<?= BASE_URL; ?>orders">Mis Pedidos</a></li>
                    <li><a href="<?= BASE_URL; ?>cart">Carrito 🛒</a></li>
                <?php endif; ?>
                <li><a href="<?= BASE_URL; ?>logout">Cerrar Sesión (<?= ucfirst(strtolower($_SESSION['identity']['nombre'])); ?>)</a></li>
            <?php else: ?>
                <!-- SI NO ES USUARIO -->
                <li><a class="active" href="<?= BASE_URL; ?>">Tienda Online</a></li>
                <li><a href="<?= BASE_URL; ?>products">Productos</a></li>
                <li><a href="<?= BASE_URL; ?>categories">Categorías</a></li>
                <li><a href="<?= BASE_URL; ?>cart">Carrito 🛒</a></li>
                <li><a href="<?= BASE_URL; ?>login">Log In</a></li>
                <li><a href="<?= BASE_URL; ?>register">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>

</body>

</html>
