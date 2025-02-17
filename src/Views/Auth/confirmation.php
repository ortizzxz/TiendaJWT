<?php if (isset($_SESSION['confirmation'])): ?>
    <?php if ($_SESSION['confirmation'] === 'success'): ?>
        <p style="color: green;">Tu cuenta ha sido exitosamente verificada. Ya puedes iniciar sesión.</p>
    <?php elseif ($_SESSION['confirmation'] === 'fail'): ?>
        <p style="color: red;">Ha habido un error confirmando tu cuenta o el token ya ha expirado. Por favor intentalo nuevamente más tarde.</p>
    <?php endif; ?>
    <?php unset($_SESSION['confirmation']); ?>
<?php else: ?>
    <p>Por favor revise su correo y haga clic en el enlace de confirmación.</p>
<?php endif; ?>

<p><a href="<?php echo BASE_URL; ?>">Volver a la página principal</a></p>
