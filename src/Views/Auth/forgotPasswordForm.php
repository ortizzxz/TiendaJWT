<div class="form-container">
    <h3>Recuperar Contraseña</h3>

    <?php if (isset($_SESSION['message'])): ?>
        <p class="success"><?= htmlspecialchars($_SESSION['message']) ?></p>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error']) ?></p>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>forgot-password" method="POST" class="registration-form">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>

        <input type="submit" value="Enviar Instrucciones" class="submit-button">
    </form>

    <p class="back-to-login">
        <a href="<?= BASE_URL ?>login">Volver al inicio de sesión</a>
    </p>
</div>

<?php
// Limpiar mensajes de sesión después de mostrarlos
unset($_SESSION['message']);
unset($_SESSION['error']);
?>
