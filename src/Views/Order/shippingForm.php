<div class="form-container">
    <?php if (isset($_SESSION['pedido']) && $_SESSION['pedido'] == 'success'): ?>
        <p class="success-message">Pedido creado correctamente</p>
    <?php endif; ?>

    <?php if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])): ?>
        <div class="error-messages">
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h2>Dirección De Envío</h2>
    <form action="<?=BASE_URL?>order/create" method="POST" class="registration-form">

        <div class="form-group">
            <label for="provincia">Provincia:</label>
            <input type="text" id="provincia" name="shipping[provincia]" required>
        </div>

        <div class="form-group">
            <label for="localidad">Localidad:</label>
            <input type="text" id="localidad" name="shipping[localidad]" required>
        </div>

        <div class="form-group">
            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="shipping[direccion]" required>
        </div>

        <input type="submit" value="Crear Pedido" class="submit-button">
    </form>
</div>

<?php
// Limpiar las variables de sesión después de mostrarlas
if (isset($_SESSION['errors'])) {
    unset($_SESSION['errors']);
}

if (isset($_SESSION['pedido'])) {
    unset($_SESSION['pedido']);
}
?>