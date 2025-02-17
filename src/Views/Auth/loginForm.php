<div class="form-container">
    <?php if (isset($_SESSION['register']) && $_SESSION['register'] == 'success'): ?>
        <p class="success-register">Usuario registrado correctamente</p>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <p class="error-message-minwidth">
            <?php echo htmlspecialchars($_SESSION['error']); ?>
        </p>
    <?php endif; ?>
    <?php if (isset($_SESSION['authsuccess']) && $_SESSION['authsuccess'] == 'success'): ?>
        <p class="success-register">Contraseña actualizada. Ingrese en su cuenta.</p>
    <?php endif; ?>

    <?php if (isset($_SESSION['confirmado']) && $_SESSION['confirmado'] == 'fail'): ?>
        <p class="error">Usuario no confirmado - revise su correo para confirmar la cuenta.</p>
    <?php endif; ?>


    <h3>Iniciar sesión</h3>
    <form action="<?= BASE_URL ?>login" method="POST" class="registration-form">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="data[email]"
                value="<?= isset($_POST['data']['email']) ? htmlspecialchars($_POST['data']['email']) : '' ?>">
            <?php if (isset($_SESSION['errors']['email'])): ?>
                <p class="error"><?= $_SESSION['errors']['email'] ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" name="data[password]" id="password">
            <?php if (isset($_SESSION['errors']['password'])): ?>
                <p class="error"><?= $_SESSION['errors']['password'] ?></p>
            <?php endif; ?>
        </div>

        <input type="submit" value="Iniciar sesión" class="submit-button">
    </form>
    <p class="forgot-password">
        <a href="<?= BASE_URL ?>forgot-password">¿Olvidaste tu contraseña?</a>
    </p>
</div>

<?php
if (isset($_SESSION['register'])) {
    unset($_SESSION['register']);
}
if (isset($_SESSION['confirmado'])) {
    unset($_SESSION['confirmado']);
}
if (isset($_SESSION['login'])) {
    unset($_SESSION['login']);
}
if (isset($_SESSION['errors'])) {
    unset($_SESSION['errors']);
}
if (isset($_SESSION['authsuccess'])) {
    unset($_SESSION['authsuccess']);
}
if (isset($_SESSION['error'])) {
    unset($_SESSION['error']);
}
?>