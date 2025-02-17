<div class="form-container">

    <h3>Registrarse</h3>
    <form action="<?= BASE_URL ?>register" method="POST" class="registration-form">
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" id="name" name="data[nombre]"
                value="<?= isset($_POST['data']['nombre']) ? htmlspecialchars($_POST['data']['nombre']) : '' ?>">
            <?php if (isset($_SESSION['errors']['nombre'])): ?>
                <p class="error"><?= $_SESSION['errors']['nombre'] ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="lastname">Apellido</label>
            <input type="text" id="lastname" name="data[apellidos]"
                value="<?= isset($_POST['data']['apellidos']) ? htmlspecialchars($_POST['data']['apellidos']) : '' ?>">
            <?php if (isset($_SESSION['errors']['apellidos'])): ?>
                <p class="error"><?= $_SESSION['errors']['apellidos'] ?></p>
            <?php endif; ?>
        </div>

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

        <?php if (isset($_SESSION['identity']) && $_SESSION['identity']['rol'] === 'admin'): ?>
            <div class="form-group">
                <label for="rol">Rol del usuario</label>
                <select name="data[rol]" id="rol">
                    <option value="user">Usuario normal</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
        <?php endif; ?>

        <input type="submit" value="Registrar" class="submit-button">
    </form>
    <?php if (isset($_SESSION['emailExists']) && $_SESSION['emailExists'] == 'true'): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['errors']) ?></p>
        <?php unset($_SESSION['emailExists']); // Limpiar la variable después de mostrarla ?>
    <?php endif; ?>

</div>

<?php
// Limpiar las variables de sesión después de mostrarlas
if (isset($_SESSION['errors'])) {
    unset($_SESSION['errors']);
}

if (isset($_SESSION['register'])) {
    unset($_SESSION['register']);
}
?>