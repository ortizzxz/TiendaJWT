<div class="form-container">
    <h3>Restablecer Contraseña</h3>

    <?php if (isset($_SESSION['error'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error']) ?></p>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>reset-password/<?= $token ?>" method="POST" class="reset-password-form">
        <div class="form-group">
            <label for="password">Nueva Contraseña</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirmar Contraseña</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <input type="submit" value="Restablecer Contraseña" class="submit-button">
    </form>
</div>

<?php
unset($_SESSION['error']);
?>
